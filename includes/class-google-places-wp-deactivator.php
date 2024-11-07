<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/Jpatrick2zo/google-places-wp
 * @since      1.0.0
 *
 * @package    Google_Places_Wp
 * @subpackage Google_Places_Wp/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Google_Places_Wp
 * @subpackage Google_Places_Wp/includes
 * @author     Jordan Tuzzeo <jpatrick2zo@gmail.com>
 */
class Google_Places_Wp_Deactivator {

	/**
	 * Remove all saved places and affiliated plugin settings.
	 * 
	 * @since    1.0.0
	 */
	public static function deactivate() 
	{
		$existingPlaces = get_posts([
			'post_type'  => 'google_places_post', 
			'posts_per_page' => -1
		]);

		// Remove all saved places
		foreach($existingPlaces as $place) {
			if(!wp_delete_post($place->ID, true)) { 
				error_log("Couldn't remove post ID while deactivating: " . $place->ID);
			}
		}
		
		// Remove all settings
		foreach ( wp_load_alloptions() as $option => $value ) {
			if ( strpos( $option, 'google_places_' ) === 0 ) {
				delete_option( $option );
			}
		}
	}
}
