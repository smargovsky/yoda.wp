<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Serge Margovsky <smargovsky@gmail.com>
 */
class Yoda_WP_Activator {

    /*
     *
     * Declare custom post types, taxonomies, and plugin settings
     * Flushes rewrite rules afterwards
     *
     * @since       1.0.0
     */
    public static function activate() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-yoda-wp-admin.php';
        Yoda_WP_Admin::createTables();
        Yoda_WP_Admin::new_cpt_announcement();
        Yoda_WP_Admin::new_cpt_wizard();
        flush_rewrite_rules();
	}

}
