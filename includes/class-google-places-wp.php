<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/Jpatrick2zo/google-places-wp
 * @since      1.0.0
 *
 * @package    Google_Places_Wp
 * @subpackage Google_Places_Wp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Google_Places_Wp
 * @subpackage Google_Places_Wp/includes
 * @author     Jordan Tuzzeo <jpatrick2zo@gmail.com>
 */
class Google_Places_Wp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Google_Places_Wp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'GOOGLE_PLACES_WP_VERSION' ) ) {
			$this->version = GOOGLE_PLACES_WP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'google-places-wp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_admin_filters();
		
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Google_Places_Wp_Loader. Orchestrates the hooks of the plugin.
	 * - Google_Places_Wp_i18n. Defines internationalization functionality.
	 * - Google_Places_Wp_Admin. Defines all hooks for the admin area.
	 * - Google_Places_Wp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-google-places-wp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-google-places-wp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-google-places-wp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-google-places-wp-public.php';

		$this->loader = new Google_Places_Wp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Google_Places_Wp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Google_Places_Wp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks(): void {

		$plugin_admin = new Google_Places_Wp_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Hook settings page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'google_places_menu' );

		// Register settings page sections
		$this->loader->add_action( 'admin_init', $plugin_admin, 'google_places_wp_register_sections' );

		// Register settings page fields
		$this->loader->add_action( 'admin_init', $plugin_admin, 'google_places_settings_fields' );

		// Register places custom post type
		$this->loader->add_action( 'admin_init', $plugin_admin, 'google_places_custom_post' );
		
		// Ajax for saving places in DB
		$this->loader->add_action( 'wp_ajax_google_places_save_results',$plugin_admin, 'google_places_save_results' );

		// Ajax for saving place type settings
		$this->loader->add_action( 'wp_ajax_google_places_save_places_settings',$plugin_admin, 'google_places_save_place_settings' );

	}

	private function define_admin_filters(): void
	{
		$plugin_admin = new Google_Places_Wp_Admin( $this->get_plugin_name(), $this->get_version() );

		// Filter for adding type="module" to scripts
		$this->loader->add_filter(
			'script_loader_tag', 
			$plugin_admin,
			'google_places_jsmodule',
			10,
			2
		);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks(): void {

		$plugin_public = new Google_Places_Wp_Public( 
			plugin_name: $this->get_plugin_name(), 
			version: 	 $this->get_version() 
		);

		$this->loader->add_action( hook: 'wp_enqueue_scripts', component: $plugin_public, callback: 'enqueue_styles' );

		$this->loader->add_action( hook: 'wp_enqueue_scripts', component: $plugin_public, callback: 'enqueue_scripts' );

		// Register custom post type
		$this->loader->add_action( hook: 'init', component: $plugin_public, callback: 'register_google_places_posts' );

		// Register shortcode
		$this->loader->add_action( hook: 'init', component: $plugin_public, callback: 'register_google_places_shortcodes' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Google_Places_Wp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader(): Google_Places_Wp_Loader {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version(): string {
		return $this->version;
	}

}
