<?php

/**
 * Google Places WP
 *
 * Showing points of interest on a Google map surrounding a target location. 
 *
 * @link              https://portfolio.2zotypes.com
 * @since             1.0.0
 * @package           Google_Places_Wp
 *
 * @wordpress-plugin
 * Plugin Name:       Google Places API WP
 * Plugin URI:        https://https://github.com/Jpatrick2zo/google-places-wp
 * Description:       Google Place API integration for Wordpress. Renders map and shows places of interest around a target location.
 * 
 * 
 * Version:           1.0.0
 * Author:            Jordan Tuzzeo
 * Author URI:        https://portfolio.2zotypes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       google-places-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) 
{
	die;
}

/* Plugin Version */
define( 'GOOGLE_PLACES_WP_VERSION', '1.0.0' );

/* Activate */
function activate_google_places_wp() 
{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-places-wp-activator.php';
	Google_Places_Wp_Activator::activate();
}

/* Deactivate */
function deactivate_google_places_wp() 
{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-places-wp-deactivator.php';
	Google_Places_Wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_google_places_wp' );
register_deactivation_hook( __FILE__, 'deactivate_google_places_wp' );

/* Require the plugins main class */
require plugin_dir_path( __FILE__ ) . 'includes/class-google-places-wp.php';

function run_google_places_wp() 
{
	$plugin = new Google_Places_Wp();
	$plugin->run();
}

run_google_places_wp();
