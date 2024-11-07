<?php
/**
 * Handles setting up all pages, settings and the main interface in the WP dashboard.
 *
 * @link       https://github.com/Jpatrick2zo/google-places-wp
 * @since      1.0.0
 * @package    Google_Places_Wp
 * @subpackage Google_Places_Wp/admin
 * @author     Jordan Tuzzeo <jpatrick2zo@gmail.com>
 */
class Google_Places_Wp_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Ajax handler for processing and saving place settings
	 * 
	 * @return void
	 */
	public function google_places_save_place_settings() 
	{
		try {
			foreach($_POST as $key => $val) { 
				
				//TODO: change to strrpos
				if (!str_contains($key, 'google_places_field') || $key == 'action') continue;
				
				if(get_option($key) !== $val) { 
					update_option($key, $val);
				}
			}
		} catch (\Exception $e) {
			wp_send_json_error($e->getMessage());	
		}
		
		wp_send_json_success();
	}

	/**
	 * Ajax handler for proccessing found places and saving them as custom posts.
	 * Each place found has it's associated meta data saved as custom fields on the post. 
	 * Post Type: google_places_post
	 * 
	 * @return void
	 */
	public function google_places_save_results() 
	{
		if(isset($_POST['prop_meta'])) {
			$all_places = $_POST['prop_meta'];
		} else {
			wp_send_json_error(['error' => 'No places to save provided.']);
		}
		
		$place_ids       = array_column($all_places, column_key: 'id');
		$existing_places = get_posts(
			['post_type'  => 'google_places_post', 'posts_per_page' => -1]
		);
		
		if($existing_places) { 
			foreach($existing_places as $place) {
				
				$place_location_id = get_post_meta($place->ID, 'location_id');
				
				if($place_location_id && in_array($place_location_id[0], $place_ids )) {
					$existing_places_ids[$place->ID] = $place_location_id[0];
				} else {
					if(!isset($places_to_remove)) {
						$places_to_remove[$place->ID] = $place_location_id[0];
					} else {
						if(!in_array($place_location_id[0], $places_to_remove)) {
							$places_to_remove[$place->ID] = $place_location_id[0];
						}
					}
					
				}	
			}
			wp_reset_query();
		}

		if(isset($places_to_remove)) { 
			foreach($places_to_remove as $deleted_place_wp_id => $place_location_id) {
				if(!wp_delete_post($deleted_place_wp_id, true)) { 
					error_log("Google Places WP Error: Couldn't remove post ID -> " . $deleted_place_wp_id);
				}
			}
		}

		foreach($all_places as $place) { 
			
			$place_wp_custom_fields = [
				'name' 		     => $place['name'],
				'types' 	 	 => $place['type'],
				'url' 			 => $place['url'],
				'phone_number'   => $place['phone'],
				'pretty_address' => $place['address'],
				'lat' 			 => $place['lat'],
				'lng' 			 => $place['lng'],
				'location_id' 	 => $place['id']
			];

			if(isset($place['photo'])) {
				$photoUrl = $place['photo'];
				
				// TODO: Add field for a 2nd optional API key, if the 2nd key is present then we need to save the photos with that key instead in order to ensure it's never exposed to the frontend. 
				if(1 == 0) {
					$urlParsed = parse_url($photoUrl);
					
					parse_str($urlParsed['query'], $urlParams);
					// $urlParams['key'] = get_option('google_places_wp_apikey_public');
				    $urlParams = implode('&', $urlParams);
					
					// Rebuild the URL with the new 'public' api key set
					$url = sprintf(
						'https://places.googleapis.com%1$s?%2$s', 
						$urlParsed['path'],
						$urlParams
					);

					$photoUrl = $url; 
				}

			} else {
				$photoUrl = false;
			}

			$place_wp_custom_fields['photo'] = $photoUrl;
			
			if(!isset($existing_places_ids) || !in_array($place['id'], $existing_places_ids ) ) { 
				$place_wp_id = wp_insert_post([
					'post_type'   => 'google_places_post',
					'post_title'  => $place['name'],
					'post_status' => 'publish'
				]);
			} else {
				$place_wp_id = array_search($place['id'], $existing_places_ids);
			}
			

			if (!is_wp_error($place_wp_id)) {
				// Update post with all Place details as custom fields in WP
				foreach($place_wp_custom_fields as $custom_field_name => $value) {
					update_post_meta($place_wp_id, $custom_field_name, $value);
				}

			} else {
				error_log('Google Places WP Error: '. $place_wp_id->get_error_message());
				$place_errors[] = $place_wp_id->get_error_message();
			}
		}

		if(isset($place_errors)) {
			wp_send_json_error(['error' => 'Error saving places.']);	
		}
		
		wp_send_json_success();
	}

	/**
	 * Register the stylesheets for the admin settings page.
	 * 
	 * @return void
	 */
	public function enqueue_styles($hook_suffix) 
	{
		if ($hook_suffix === 'toplevel_page_google_places_settings') {
			wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/google-places-wp-admin.css', [], $this->version, 'all' );
		}
	}

	/**
	 * Register the scipts for the admin settings page.
	 * 
	 * @return void
	 */
	public function enqueue_scripts($hook_suffix)
	{
		if ($hook_suffix === 'toplevel_page_google_places_settings') {

			$api_key = get_option('google_places_field_apikey');

			$script_data = [
				'adminurl' => admin_url('admin-ajax.php')
			];

			if($api_key) {
				// Only include the google maps JS library if there's an API key set
 				wp_enqueue_script('google-maps-wp', plugin_dir_url(__FILE__) . '../js/google-maps-wp.js', [], $this->version, args: true);

				$property_coords = get_option('google_places_field_latlong');
				$coords 		 = $property_coords ? explode(',',$property_coords) : null;
				$saved_places    = query_posts(['post_type'  => 'google_places_post', 'posts_per_page' => -1]);

				if($saved_places) { 
					foreach($saved_places as $place) {
						$place_data = get_post_custom($place->ID);
						
						foreach($place_data as $place_field_name => $value) {
								$place_script_fields[$place_field_name] = $value[0];
						}
						
						$script_data['saved_places'][] = $place_script_fields;
					}
				}

				if(is_array($coords) ) {
					$script_data['coords'] = [
						'lat' => (float) $coords[0],
						'lng' => (float) $coords[1]
					];
				}
			}

			wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/google-places-wp-admin.js', ['jquery'], $this->version, true);

			
			
			// Google Maps Library loaded with it's own data seperately from the core plugin JS. 
			wp_localize_script( 
				'google-maps-wp',
				'google_places_wp',
				['apikey' =>  $api_key]
			);
			
			// Core plugin logic WP Admin script 
			wp_localize_script( 
				$this->plugin_name, 
				'gp_places', 
				$script_data
			);
			
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script($this->plugin_name);
		}
	}

	/**
	 * Handle adding modular JS scripts to the page
	 */
	public function google_places_jsmodule( $tag, $handle ) {
		$type = wp_scripts()->get_data( $handle, 'type' );

		if ( $type == 'module') {
			$tag = str_replace( '></', ' type="module"></', $tag );
		}
		return $tag;
	}

	/* Add menu link to admin sidebar */
	public function google_places_menu(): void 
	{
		add_menu_page( 'WP Places Options', 'WP Places', 'manage_options', 'google_places_settings', array($this,'google_places_options'),'dashicons-list-view',20);
	}

	/* Register sections for setting fields. */
	public function google_places_wp_register_sections(): void 
	{ 
		 add_settings_section(
			 'google_map_settings_section',
			 'WP Places API Settings',
			 [$this, 'google_map_settings_section_cb'],
			 'google_places_settings_group1'
		 );

		add_settings_section(
			 'google_places_ptype_section',
			 'Search Nearby',
			 [$this, 'google_map_places_section_cb'],
			 'google_places_settings_group2'
		 );
	}

	/* Register custom setting fields.*/
	public function google_places_settings_fields(): void 
	{
		require_once plugin_dir_path(__FILE__) . 'partials/google-places-settings.php';
	}

	/* Register custom posts.*/
	public function google_places_custom_post(): void 
	{
		require_once plugin_dir_path(__FILE__) . 'partials/google-places-custom-post.php';
	}

	public function google_map_settings_section_cb( $args ): void 
	{ ?>
		<div class="notice notice-warning" style="max-width: 70rem;">
        	<p>
				<strong>Required API libraries are Google Maps JS API and Places API. Make sure to always restrict your API Keys while using them in production <a href="https://cloud.google.com/docs/authentication/api-keys#securing" target="_blank">Learn More</a>. <br/>You can optionally provide a second API key which is restricted to just the MAPS JS API and that will be used when displaying the map using the shortcode.<br/><br/>
			
				While using the Google Places WP plugin if you're doing several searches it's possible you may incur a charge. <br/>
				Please review Google's latest pricing information for using their services.
			
				</strong>
			<p>

        </div>
 	<? }

	public function google_map_places_section_cb( $args ): void {
		echo '<p class="description">Radius can be set for each place type below, values are in meters.</p>';
	 }

	public function google_maps_render_field( $field ): void { 
		echo "<span>{$field['title']}</span>";
		
		call_user_func($field['callback'], $field['args']);

		if ($field['title'] && $field['args']['type'] != 'conditional_checkbox_text') {
			echo "<label for='{$field['id']}'></label>\n";
		}
	}
	public function do_conditional_settings_section($page) {
		global $wp_settings_sections, $wp_settings_fields;
	
		if (!isset($wp_settings_sections[$page])) {
			return;
		}
	
		foreach ((array) $wp_settings_sections[$page] as $section) {
			if ($section['title']) {
				echo "<h2>{$section['title']}</h2>\n";
			}
	
			if ($section['callback']) {
				call_user_func($section['callback'], $section);
			}
	
			if (!isset($wp_settings_fields[$page][$section['id']])) {
				continue;
			}

			$parent_id_array = array_reduce($wp_settings_fields[$page][$section['id']], function($carry, $entry) {
				if (isset($entry['args']['parent_id'])) {
					$carry[$entry['args']['parent_id']] = $entry['args']['uid'];
				}
				return $carry;
			}, []);

			
			$parent_children = [];
			// Remove all children from main section array and output them all at once inside parent field loop below. 
			foreach($parent_id_array as $id => $child) {
				$parent_children[$id][] = $wp_settings_fields[$page][$section['id']][$child];
				unset($wp_settings_fields[$page][$section['id']][$child]);
			}
	
			echo '<div class="google-places-ptype-section">';
			foreach ((array) $wp_settings_fields[$page][$section['id']] as $idx => $field) { 
				$isParent = isset($field['args']['parent']); 
				?>
				<div class="google-places-wp-ptypes-settings <?php echo ($isParent) ? 'google-places-wp-parent-group' : ''?>">
				
				<?php if ( $isParent ) { ?>
					 <div class="google-places-wp-ptypes-settings-group">
						<div class="field-group-parent">
				<?php } ?>
				
				<?php $this->google_maps_render_field($field); 

				if ( $isParent ) { ?>
					</div>
				<?php } 

				if($isParent && count($parent_children)) {
					foreach($parent_children as $child_field) {
						echo '<div class="field-group-child">';
							$this->google_maps_render_field($child_field[0]);
						echo '</div>';
					}
				} 
				
				echo '</div>';

				echo ($isParent) ? '</div>' : '';
			}
			
			echo '</div>';
		}
	}
	/** 
	 * Generates HTML for all custom setting fields in admin area. 
	 * 
	 * @param array $args - array with info on what field type to render 
	 * 
	 * @return void 
	 */
 	public function google_places_fields_cb( $args )
	{ 
 		 $value = get_option( $args['uid'] );
         
         if( ! $value ) {
            $value = $args['default'];
         }

          switch( $args['type'] ){
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" value="%3$s" />', $args['uid'],$args['type'], $value );
                printf('<p class="description">%1$s', $args['description']);
                break;

            case 'hidden':
	            printf( '<input name="%1$s" id="%1$s" type="%2$s" value="%3$s"/>', $args['uid'], $args['type'], $value);
	            break;

            case 'checkbox':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" value="1" %3$s/>', $args['uid'], $args['type'], checked(1, $value, false) );
				printf('<p class="description">%1$s', $args['description']);
            	break;

            } 	
	  }

	  public function google_places_custom_fields( $args )
	{ 
 		 $value = get_option( $args['uid'] );
        
		 if($args['type'] == 'conditional_checkbox_text') {
			$radius_value = get_option( $args['uid'].'_radius' );
			if(!$radius_value) {
				$radius_value = $args['default_radius'];
			}
		 }

         if( ! $value ) {
			$value = $args['default'];
         }

          switch( $args['type'] ) {
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" class="%4$s" id="%1$s" type="%2$s" value="%3$s" />', $args['uid'],$args['type'], $value, $args['class'] );
                break;
			
			case 'conditional_checkbox_text':
				// Custom checkbox + label + text input 
				printf( 
			'<input name="%1$s" class="control-parent" id="%1$s" type="%2$s" value="1" %3$s/>
					<label for="%1$s"></label>
					<input name="%4$s" id="%4$s" class="%8$s" type="%5$s" value="%6$s" />', 
			$args['uid'], // Add checkbox uid
					'checkbox', 
					checked(1, $value, false), 
					$args['uid'].'_radius', // Add radius text input uid 
					'number', 
					$radius_value,
					$args['class'],
					(checked(1, $value, false) ? 'parent-checked ' : ' '). 'controlled-radius-input',
					!checked(1, $value, false) ?: 'disabled'
				);
                break;

            case 'hidden':
	            printf( '<input name="%1$s" id="%1$s" type="%2$s" value="%3$s"/>', $args['uid'], $args['type'], $value);
	            break;

            case 'checkbox':
				printf( '<input name="%1$s" class="%4$s" id="%1$s" type="%2$s" value="1" %3$s/>', $args['uid'], $args['type'], checked(1, $value, false), $args['class'] );
            	break;

            } 	
	  }

	  

	/** 
	 * Render main admin settings page. 
	 * 
	 * @param array $args - array with info on what field type to render 
	 * 
	 * @return void 
	 */
	public function google_places_options() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'google-places-wp' ) );
		} ?>

		<div class="wrap">
			<div class="loading-overlay">
				<div class="loader"></div>
			</div>
			<div id="<?php echo $this->plugin_name; ?>-tabs" style="display: none;">
				<ul>
					<li><a href="#<?php echo $this->plugin_name; ?>-map-settings">Map Settings</a></li>
					<li><a href="#<?php echo $this->plugin_name; ?>-find-places">Find Places</a></li>
				</ul>

				<div id="<?php echo $this->plugin_name; ?>-map-settings">
					<?php include plugin_dir_path(__FILE__) . 'partials/google-places-wp-map-settings-tab.php'; ?>
				</div>
				
				<div id="<?php echo $this->plugin_name; ?>-find-places">.
					<?php include plugin_dir_path(__FILE__) . 'partials/google-places-wp-map-places-tab.php'; ?>
				</div>
			</div>
		</div>


		
		<?php 
	 }
}
