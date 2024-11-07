<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/Jpatrick2zo/google-places-wp
 * @since      1.0.0
 *
 * @package    Google_Places_Wp
 * @subpackage Google_Places_Wp/public
 * @author     Jordan Tuzzeo <jpatrick2zo@gmail.com>
 */

class Google_Places_Wp_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function register_google_places_posts()
	{
		$labels = [
			'name'                  => __( 'WP GPlaces', 'google-places-wp' ),
			'singular_name'         => __( 'WP GPlace', 'google-places-wp' ),
			'menu_name'             => '',
			'name_admin_bar'        => '',
			'archives'              => '',
			'attributes'            => '',
			'parent_item_colon'     => '',
			'all_items'             => '',
			'add_new_item'          => '',
			'add_new'               => '',
			'new_item'              => '',
			'edit_item'             => '',
			'update_item'           => '',
			'view_item'             => '',
			'view_items'            => '',
			'search_items'          => '',
			'not_found'             => '',
			'not_found_in_trash'    => '',
			'featured_image'        => '',
			'set_featured_image'    => '',
			'remove_featured_image' => '',
			'use_featured_image'    => '',
			'insert_into_item'      => '',
			'uploaded_to_this_item' => '',
			'items_list'            => '',
			'items_list_navigation' => '',
			'filter_items_list'     => '',
		];
	
		$rewrite = [
			'slug'                  => 'post_type',
			'with_front'            => false,
			'pages'                 => false,
			'feeds'                 => false,
		];
	
		$args = [
			'label'                 => __( 'WP GPlace', 'google-places-wp' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'menu_position'         => 5,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'post',
		];
	
		register_post_type( 'google_places_post', $args );

	}

	/**
	 * Register the stylesheets for the public-facing side of the plugin
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/google-places-wp-public.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() 
	{
		// TODO: add field for 2nd "restricted" API key and check here - if it exists use that instead. 
		$api_key 			    = get_option('google_places_field_apikey');
		$google_places_wp_mapdata	= [];
		
		$google_places_wp_options  = [
			'coords' 		  => 'google_places_field_latlong',
			'street_address'  => 'google_places_field_streetaddress',
			'city' 			  => 'google_places_field_city',
			'map_url' 	      => 'google_places_field_url',
			'location_name'   => 'google_places_field_placename_short'	
		];

		foreach($google_places_wp_options as $option_name => $option_field_name) 
		{
			if ($option_name == 'coords') {
				$coords = explode(',', get_option($option_field_name));
				$google_places_wp_mapdata[$option_name] = [
					'lat' => (float) $coords[0],
					'lng' => (float) $coords[1],
				];

			} else {
				$google_places_wp_mapdata[$option_name] = get_option($option_field_name);
			}	
		}

		if($api_key) {
			wp_enqueue_script('google-maps-wp', plugin_dir_url(__FILE__) . '../js/google-maps-wp.js', [], $this->version, false);
		}

		$saved_places = get_posts(['post_type'  => 'google_places_post', 'posts_per_page' => -1]);

		if($saved_places) {
			$places = [];
			foreach($saved_places as $place) {
				$saved_place_data         = get_post_custom($place->ID);
				$saved_place_location_id   = '';
				foreach($saved_place_data as $place_field_name => $value) {
					
					if($place_field_name == 'location_id') {
						$saved_place_location_id = $value[0];
					}

					$place_data[$place_field_name] = $value[0]; 
				}

				$places[$saved_place_location_id] = $place_data; 
			}
			
			$google_places_wp_mapdata['places'] = $places; 
		} else {
		    $google_places_wp_mapdata['places'] = false; 
		}

		wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/google-places-wp-public.js', ['jquery'], $this->version, true);

		
		
		// Google Maps Library loaded with it's own data seperately from the core plugin JS. 
		wp_localize_script( 
			'google-maps-wp',
			'google_places_wp',
			['apikey' =>  $api_key]
		);
		
		wp_localize_script( $this->plugin_name, 'google_places_wp', [
			'apikey'	 => get_option('google_places_field_apikey'),
			'admin_ajax' => admin_url('admin-ajax.php'),
			'mapdata'   => $google_places_wp_mapdata
		]);

		wp_enqueue_script($this->plugin_name);
	}

	/**
	 * Render gpwpshowmap shortcode HTML
	 *
	 * @since    1.0.0
	 */
	 public function render_shortcode() 
	 {
		
		$options    = [
			'api_key' 		  => 'google_places_field_apikey',
			'street_address'  => 'google_places_field_streetaddress',
			'city' 			  => 'google_places_field_city',
			'map_url' 	      => 'google_places_field_url',
			'prop_shortname'  => 'google_places_field_placename_short',
			'target_location' => 'google_places_field_latlong'			
		];

		foreach($options as $option_name => $value) 
		{
			$settings[$option_name] = get_option($value);
		}

		if(!$settings['api_key'] || !$settings['target_location']) {
			
			$html = '<p style="text-align: center; margin: 1rem; background: red;color: #FFF;">
				<strong>Please enter your API Key in the WP Places settings page and make sure you have configured your map properly.</strong>
			</p>';

			return $html; 
		}

		$placeTypeFields = [
			'google_places_field_ptype_hospital'      => [ 'label' => 'Hospital' ],
			'google_places_field_ptype_library'       => [ 'label' => 'Library' ],
			'google_places_field_ptype_restaurant'    => [ 'label' => 'Restaurant' ],
			'google_places_field_ptype_pharmacy'      => [ 'label' => 'Pharmacy' ],
			'google_places_field_ptype_school'        => [ 'label' => 'School' ],
			'google_places_field_ptype_shopping'      => [ 'label' => 'Shopping Mall' ],
			'google_places_field_ptype_train_station' => [ 'label' => 'Train Station' ],
			'google_places_field_ptype_park'		  => [ 'label' => 'Park' ],
			'google_places_field_ptype_college' 	  => [ 'label' => 'College' ],
			'google_places_field_ptype_entertainment' => [ 'label' => 'Entertainment' ]
		];

		$placeTypeButtons = ''; 
		foreach($placeTypeFields as $placeTypeName => $placeTypeProperties) {
			
			// Match last part of field name to get a valid place type
			$placeTypeReg = preg_match('/^(?:[^_]*_){3}(.*)/',$placeTypeName,$matches);

			if($placeTypeReg) {
				$placeType = explode('_', $matches[1])[1];
			}

			$placeTypeButtons .= sprintf('<li><button data-place-type="%1$s">%2$s</button><div class="google-wp-places-places-listed"></div></li>',
			$placeType,
					$placeTypeProperties['label']
			);
		}

		$html = sprintf(
	'<div class="google-maps-wp-wrapper">
				<div class="google-maps-wp-map-container">
					<div class="section-heading">
						<p class="section-tag-thin">
							<span class="section-tag-bold">
								%1$s
							</span> 
						</p>
					</div>
					
					<div class="google-places-wp-map-flex">
						<div class="google-places-wp-map-box-left">
							<ul class="map-controller">
								%2$s
							</ul>
						</div>

						<div class="google-places-wp-map-box-right">
							<div id="google-places-wp-map"></div>
						</div>
					</div>
				</div>
			</div>', 
			get_option('google_places_field_maptitle') ?? __('Change your map title in Google Places WP admin settings.', 'google-places-wp'), $placeTypeButtons
		);

		return $html; 
	}

	public function register_google_places_shortcodes() 
	{	
		add_shortcode( 'gpwpshowmap', [ $this, 'render_shortcode' ] );
	}
}
