<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
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
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Serge Margovsky <smargovsky@gmail.com>
 */
class Yoda_WP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Yoda_WP_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'yoda-wp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init_api();
		$this->start_session();
		$this->load_env();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Yoda_WP_Loader. Orchestrates the hooks of the plugin.
	 * - Yoda_WP_i18n. Defines internationalization functionality.
	 * - Yoda_WP_Admin. Defines all hooks for the admin area.
	 * - Yoda_WP_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-yoda-wp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-yoda-wp-public.php';

		/**
		 * The class responsible for registering all the API routes for all our Yoda guides and announcements.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-api-routes.php';

		/**
		 * The class responsible for sanitizing user input
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-sanitizer.php';

		$this->loader = new Yoda_WP_Loader();
		$this->sanitizer = new Yoda_WP_Sanitize();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Yoda_WP_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Yoda_WP_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Yoda_WP_Admin( $this->get_yoda_wp(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'new_cpt_announcement' );
		$this->loader->add_action( 'add_meta_boxes_announcement', $plugin_admin, 'cpt_announcement_add_metaboxes' );
		$this->loader->add_action( 'save_post_announcement', $plugin_admin, 'cpt_announcement_save', 10, 2 );
		// $this->loader->add_action( 'publish_announcement', $plugin_admin, 'cpt_announcement_publish', 10, 2 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices', 10, 2 );

		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'cpt_announcement_updated_messages' );

		$this->loader->add_action( 'init', $plugin_admin, 'new_cpt_wizard' );
		$this->loader->add_action( 'add_meta_boxes_wizard', $plugin_admin, 'cpt_wizard_add_metaboxes' );
		$this->loader->add_action( 'save_post_wizard', $plugin_admin, 'cpt_wizard_save', 10, 2 );
		// $this->loader->add_action( 'publish_wizard', $plugihttps://bitbucket.org/inindca/yoda-js/src/master/n_admin, 'cpt_wizard_publish', 10, 2 );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'cpt_wizard_updated_messages' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Yoda_WP_Public( $this->get_yoda_wp(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Register all the API Routes.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function init_api() {

		$plugin_api_routes = new Yoda_WP_API_Routes();

		$this->loader->add_action( 'init', $plugin_api_routes, 'cors_init' );
		$this->loader->add_action( 'rest_api_init', $plugin_api_routes, 'rest_api_init' );
	}

	private function start_session() {
		// Use this to pass around errors on post save/publish.
    if(!session_id()) {
        session_start();
    }
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
	public function get_yoda_wp() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Yoda_WP_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public static function display_session_message($message, $type) {
		$_SESSION['yoda-session-notice'] = [
			'message' => $message,
			'type' => $type
		];
	}

	public static function display_notice_message($message, $type) {
		add_action( 'admin_notices', function() use ($message, $type) {
			?>
			<div class="notice notice-<?php echo $type; ?> is-dismissible">
				<p><?php _e( $message, 'yoda-text-notice' ); ?></p>
			</div>
			<?php
		} );
	}

	public static function load_env() {
		try {
			$dotenv = new Dotenv\Dotenv(plugin_dir_path( dirname( __FILE__ ) ));
			$dotenv->load();
		} catch  (Dotenv\Exception\InvalidPathException $e) {
			Yoda_WP::display_notice_message("Uhoh, YODA is missing its .env file!", 'error');
		}
	}

}
