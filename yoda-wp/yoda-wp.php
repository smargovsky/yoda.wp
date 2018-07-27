<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Yoda_WP
 *
 * @wordpress-plugin
 * Plugin Name:       YODA WP
 * Plugin URI:        http://example.com/yoda-wp-uri/
 * Description:       Your Online Directory Assistant - Create and manage UI guides and announcements for your web application... like a space wizard.
 * Version:           1.0.0
 * Author:            Madison, WI UI Team
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yoda-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'YODA_WP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-yoda-wp-activator.php
 */
function activate_yoda_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yoda-wp-activator.php';
	Yoda_WP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-yoda-wp-deactivator.php
 */
function deactivate_yoda_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yoda-wp-deactivator.php';
	Yoda_WP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_yoda_wp' );
register_deactivation_hook( __FILE__, 'deactivate_yoda_wp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yoda-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_yoda_wp() {

	$plugin = new Yoda_WP();
	$plugin->run();

}
run_yoda_wp();
