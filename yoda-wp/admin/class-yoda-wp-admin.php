<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/admin
 * @author     Serge Margovsky <smargovsky@gmail.com>
 */
class Yoda_WP_Admin {

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

	private $error_message;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-api-db.php';
		$this->db = new Yoda_WP_API_DB();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yoda-wp-util.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yoda_WP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yoda_WP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yoda-wp-admin.css', array('wp-jquery-ui-dialog'), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yoda_WP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yoda_WP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yoda-wp-admin.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );

	}

    private function sanitizer( $type, $data ) {
        if ( empty( $type ) ) { return; }
        if ( empty( $data ) ) { return; }
        $return     = '';
        $sanitizer  = new Yoda_WP_Sanitize();
        $sanitizer->set_data( $data );
        $sanitizer->set_type( $type );
        $return = $sanitizer->clean();
        unset( $sanitizer );
        return $return;
	}


	const TABLE_GUIDES_COMPLETED = 'yoda_guides_completed';

	/**
	 * Create Yoda Tables
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public static function createTables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . self::TABLE_GUIDES_COMPLETED;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				guide_id mediumint(9) NOT NULL,
				user_id varchar(100) NOT NULL,
				completed_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

// --------------------------- ANNOUNCEMENTS -------------------------------------

/**
	 * Creates a new custom post type
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @uses 	register_post_type()
	 */
	public static function new_cpt_announcement() {
		$labels = array(
			'name'               => _x( 'Announcements', 'post type general name', 'yoda-wp' ),
			'singular_name'      => _x( 'Announcement', 'post type singular name', 'yoda-wp' ),
			'menu_name'          => _x( 'Announcements', 'admin menu', 'yoda-wp' ),
			'name_admin_bar'     => _x( 'Announcement', 'add new on admin bar', 'yoda-wp' ),
			'add_new'            => _x( 'Add New', 'announcement', 'yoda-wp' ),
			'add_new_item'       => __( 'Add New Announcement', 'yoda-wp' ),
			'new_item'           => __( 'New Announcement', 'yoda-wp' ),
			'edit_item'          => __( 'Edit Announcement', 'yoda-wp' ),
			'view_item'          => __( 'View Announcement', 'yoda-wp' ),
			'all_items'          => __( 'All Announcements', 'yoda-wp' ),
			'search_items'       => __( 'Search Announcements', 'yoda-wp' ),
			'parent_item_colon'  => __( 'Parent Announcements:', 'yoda-wp' ),
			'not_found'          => __( 'No announcements found.', 'yoda-wp' ),
			'not_found_in_trash' => __( 'No announcements found in Trash.', 'yoda-wp' )
		);

		$args = array(
			'labels'             => $labels,
	        'description'        => __( 'Description.', 'yoda-wp' ),
			'public'             => true,
			'publicly_queryable' => false,
			'exclude_from_search'=> true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest' 		 => true,
			'show_in_admin_bar'  => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'announcement' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'supports'           => array( 'title', 'revisions' , 'editor' )
		);

		register_post_type( 'announcement', $args );
	}

	/**
	 * Announcement CPT update messages.
	 *
	 * See /wp-admin/edit-form-advanced.php
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */
	function cpt_announcement_updated_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['announcement'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Announcement updated.', 'yoda-wp' ),
			2  => __( 'Custom field updated.', 'yoda-wp' ),
			3  => __( 'Custom field deleted.', 'yoda-wp' ),
			4  => __( 'Announcement updated.', 'yoda-wp' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Announcement restored to revision from %s', 'yoda-wp' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Announcement published.', 'yoda-wp' ),
			7  => __( 'Announcement saved.', 'yoda-wp' ),
			8  => __( 'Announcement submitted.', 'yoda-wp' ),
			9  => sprintf(
				__( 'Announcement scheduled for: <strong>%1$s</strong>.', 'yoda-wp' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'yoda-wp' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Announcement draft updated.', 'yoda-wp' )
		);

		if ( $post_type_object->publicly_queryable && 'announcement' === $post_type ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View announcement', 'yoda-wp' ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview announcement', 'yoda-wp' ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}

		return $messages;
	}

	/**
	 * Registers metaboxes with WordPress
	 *
	 * @param Object $post Existing post object.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function cpt_announcement_add_metaboxes( $post ) {
		/* add_meta_box(
			string $id,
			string $title,
			callable $callback,
			string|array|WP_Screen $screen = null,
			string $context = 'advanced',
			string $priority = 'default',
			array $callback_args = null )
		*/

	    add_meta_box(
			'announcement_settings',
			apply_filters( $this->plugin_name . '-metabox-announcement-settings', esc_html__( 'Settings', 'yoda-wp' ) ),
			array( $this, 'metabox' ),
			'announcement',
			'side',
			'default',
			array(
				'file' => 'announcement-settings'
			)
		);
        add_meta_box(
            'targeting',
            apply_filters( $this->plugin_name . '-metabox-targeting', esc_html__( 'Targeting', 'yoda-wp' ) ),
            array( $this, 'metabox' ),
            'announcement',
            'side',
            'default',
            array(
                'file' => 'targeting'
            )
        );
        add_meta_box(
            'translations',
            apply_filters( $this->plugin_name . '-metabox-translations', esc_html__( 'Translations', 'yoda-wp' ) ),
            array( $this, 'metabox' ),
            'announcement',
            'side',
            'default',
            array(
                'file' => 'translations'
            )
        );

	}

	public function cpt_announcement_save( $post_id, $object ) {
		if ( 'announcement' !== $object->post_type ) { return $post_id; }
        if ( 'auto-draft' === get_post_status( $post_id ) ) { return $post_id; }
        if ( 'trash' === get_post_status( $post_id ) ) { return $post_id; }

		$nonces = array('announcement-settings', 'announcement-targeting');

		$fields = array();
		$fields[] = array('announcement-url', 'text');
        $fields[] = array('announcement-selector', 'text');
		$fields[] = array('announcement-show-once', 'checkbox');
        $fields[] = array('announcement-type', 'select');

        $fields[] = array('announcement-permissions', 'text');
        $fields[] = array('announcement-feature-toggles', 'text');
        $fields[] = array('announcement-region', 'array');
        $fields[] = array('announcement-env', 'array');

        // take all announcement-region-* and combine into array announcement-region
        $_POST['announcement-region'] = array();
        $regions = array('us-east-1', 'eu-west-1', 'eu-central-1', 'ap-southeast-2', 'ap-northeast-1');
        foreach ($regions as $region) {
            $key = 'announcement-region-' . $region;
            if (array_key_exists($key, $_POST)) {
                $_POST['announcement-region'][$region] = $_POST[$key];
            }
        }

        // take all announcement-env-* and combine into array announcement-env
        $_POST['announcement-env'] = array();
        $envs = array('dca', 'tca', 'pca');
        foreach ($envs as $env) {
            $key = 'announcement-env-' . $env;
            if (array_key_exists($key, $_POST)) {
                $_POST['announcement-env'][$env] = $_POST[$key];
            }
        }

		$this->validate_meta( $_POST, $post_id, $object, $nonces, $fields);
	}

	function admin_notices() {
		if ( !isset($_SESSION['yoda-session-notice']) || !$_SESSION['yoda-session-notice'] ) {
			return;
		}
		$notice = $_SESSION['yoda-session-notice'];
		?>
		<div class="notice-<?php echo $notice['type']; ?> notice is-dismissible">
			<p><?php _e( $notice['message'], 'yoda_translations_notice' ); ?></p>
		</div>
		<?php
		$_SESSION['yoda-session-notice'] = false;
	}


    // --------------------------- WIZARDS -------------------------------------

    /**
     * Creates a new custom post type
     *
     * @since   1.0.0
     * @access  public
     * @uses    register_post_type()
     */
    public static function new_cpt_wizard() {
        $labels = array(
            'name'               => _x( 'Wizards', 'post type general name', 'yoda-wp' ),
            'singular_name'      => _x( 'Wizard', 'post type singular name', 'yoda-wp' ),
            'menu_name'          => _x( 'Wizards', 'admin menu', 'yoda-wp' ),
            'name_admin_bar'     => _x( 'Wizard', 'add new on admin bar', 'yoda-wp' ),
            'add_new'            => _x( 'Add New', 'wizard', 'yoda-wp' ),
            'add_new_item'       => __( 'Add New Wizard', 'yoda-wp' ),
            'new_item'           => __( 'New Wizard', 'yoda-wp' ),
            'edit_item'          => __( 'Edit Wizard', 'yoda-wp' ),
            'view_item'          => __( 'View Wizard', 'yoda-wp' ),
            'all_items'          => __( 'All Wizards', 'yoda-wp' ),
            'search_items'       => __( 'Search Wizards', 'yoda-wp' ),
            'parent_item_colon'  => __( 'Parent Wizards:', 'yoda-wp' ),
            'not_found'          => __( 'No wizards found.', 'yoda-wp' ),
            'not_found_in_trash' => __( 'No wizards found in Trash.', 'yoda-wp' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'yoda-wp' ),
            'public'             => true,
            'publicly_queryable' => false,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'show_in_admin_bar'  => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'wizard' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array( 'title', 'revisions' )
        );

        register_post_type( 'wizard', $args );
    }

    /**
     * Announcement CPT update messages.
     *
     * See /wp-admin/edit-form-advanced.php
     *
     * @param array $messages Existing post update messages.
     *
     * @return array Amended post update messages with new CPT update messages.
     */
    function cpt_wizard_updated_messages( $messages ) {
        $post             = get_post();
        $post_type        = get_post_type( $post );
        $post_type_object = get_post_type_object( $post_type );

        $messages['wizard'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __( 'Wizard updated.', 'yoda-wp' ),
            2  => __( 'Custom field updated.', 'yoda-wp' ),
            3  => __( 'Custom field deleted.', 'yoda-wp' ),
            4  => __( 'Wizard updated.', 'yoda-wp' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Wizard restored to revision from %s', 'yoda-wp' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => __( 'Wizard published.', 'yoda-wp' ),
            7  => __( 'Wizard saved.', 'yoda-wp' ),
            8  => __( 'Wizard submitted.', 'yoda-wp' ),
            9  => sprintf(
                __( 'Wizard scheduled for: <strong>%1$s</strong>.', 'yoda-wp' ),
                // translators: Publish box date format, see http://php.net/date
                date_i18n( __( 'M j, Y @ G:i', 'yoda-wp' ), strtotime( $post->post_date ) )
            ),
            10 => __( 'Wizard draft updated.', 'yoda-wp' )
        );

        if ( $post_type_object->publicly_queryable && 'wizard' === $post_type ) {
            $permalink = get_permalink( $post->ID );

            $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View wizard', 'yoda-wp' ) );
            $messages[ $post_type ][1] .= $view_link;
            $messages[ $post_type ][6] .= $view_link;
            $messages[ $post_type ][9] .= $view_link;

            $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
            $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview wizard', 'yoda-wp' ) );
            $messages[ $post_type ][8]  .= $preview_link;
            $messages[ $post_type ][10] .= $preview_link;
        }

        return $messages;
    }

    /**
     * Registers metaboxes with WordPress
     *
     * @param Object $post Existing post object.
     *
     * @since   1.0.0
     * @access  public
     */
    public function cpt_wizard_add_metaboxes( $post ) {
        /* add_meta_box(
            string $id,
            string $title,
            callable $callback,
            string|array|WP_Screen $screen = null,
            string $context = 'advanced',
            string $priority = 'default',
            array $callback_args = null )
        */

        add_meta_box(
            'wizard_settings',
            apply_filters( $this->plugin_name . '-metabox-wizard-settings', esc_html__( 'Settings', 'yoda-wp' ) ),
            array( $this, 'metabox' ),
            'wizard',
            'advanced',
            'high',
            array(
                'file' => 'wizard-settings'
            )
        );
        add_meta_box(
            'wizard_steps',
            apply_filters( $this->plugin_name . '-metabox-wizard-steps', esc_html__( 'Steps', 'yoda-wp' ) ),
            array( $this, 'metabox' ),
            'wizard',
            'advanced',
            'default',
            array(
                'file' => 'wizard-steps'
            )
        );
        add_meta_box(
            'translations',
            apply_filters( $this->plugin_name . '-metabox-translations', esc_html__( 'Translations', 'yoda-wp' ) ),
            array( $this, 'metabox' ),
            'wizard',
            'side',
            'default',
            array(
                'file' => 'translations'
            )
        );
    }

    public function cpt_wizard_save( $post_id, $object ) {
        if ( 'wizard' !== $object->post_type ) { return $post_id; }
		if ( 'auto-draft' === get_post_status( $post_id ) ) { return $post_id; }
		if ( 'trash' === get_post_status( $post_id ) ) { return $post_id; }


        $nonces = array('wizard-settings', 'wizard-steps-repeater');

        $fields = array();
        $fields[] = array('wizard-url', 'text');
        $fields[] = array('wizard-permissions', 'text');
        $fields[] = array('wizard-show-once', 'checkbox');
        $fields[] = array('wizard-steps-repeater', 'repeater', array(
            array( 'step-title', 'text' ),
            array( 'step-selector', 'text' ),
            array( 'stepContent', 'editor')
        ) );

        $this->validate_meta( $_POST, $post_id, $object, $nonces, $fields);
		}


    // ------------------------- Metaboxes --------------------------------

	/**
	 * Calls a metabox file specified in the add_meta_box args.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @return 	void
	 */
	public function metabox( $post, $params ) {
		if ( ! is_admin() ) { return; }
		if ( 'announcement' !== $post->post_type && 'wizard' !== $post->post_type ) { return; }
		if ( ! empty( $params['args']['classes'] ) ) {
			$classes = 'repeater ' . $params['args']['classes'];
		}

		include( plugin_dir_path( __FILE__ ) . 'partials/yoda-wp-admin-metabox-' . $params['args']['file'] . '.php' );
	}

	/**
	 * Saves metabox data
	 *
	 * Repeater section works like this:
	 *  	Loops through meta fields
	 *  		Loops through submitted data
	 *  		Sanitizes each field into $clean array
	 *   	Gets max of $clean to use in FOR loop
	 *   	FOR loops through $clean, adding each value to $new_value as an array
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @param 	int 		$post_id 		The post ID
	 * @param 	object 		$object 		The post object
	 * @return 	void
	 */
	public function validate_meta( $posted, $post_id, $object, $nonces, $fields ) {
		//wp_die( '<pre>' . print_r( $_POST ) . '</pre>' );
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return $post_id; }

        foreach ( $nonces as $nonce ) {
            if ( ! isset( $posted[$nonce] ) ) { return $post_id; }
            if ( isset( $posted[$nonce] ) && ! wp_verify_nonce( $posted[$nonce], $this->plugin_name ) ) { return $post_id; }
        }

		foreach ( $fields as $meta ) {
			$name = $meta[0];
			$type = $meta[1];
			if ( 'repeater' === $type && is_array( $meta[2] ) ) {
				$clean = array();
				foreach ( $meta[2] as $i => $field ) {
					foreach ( $posted[$field[0]] as $data ) {
						if ( empty( $data ) ) { continue; }
						$clean[$field[0]][] = $this->sanitizer( $field[1], $data );
					} // foreach
				} // foreach
				$count 		= $this->get_max( $clean );
				$new_value 	= array();
				for ( $i = 0; $i < $count; $i++ ) {
					foreach ( $clean as $field_name => $field ) {
						$new_value[$i][$field_name] = $field[$i];
					} // foreach $clean
				} // for
			} else {
				$new_value = $this->sanitizer( $type, $posted[$name] ?? null );
			}
			update_post_meta( $post_id, $name, $new_value );
		} // foreach
	}

	/**
	 * Returns the count of the largest arrays
	 *
	 * @param 		array 		$array 		An array of arrays to count
	 * @return 		int 					The count of the largest array
	 */
 	private static function get_max( $array ) {
 		if ( empty( $array ) ) { return '$array is empty!'; }
 		$count = array();
		foreach ( $array as $name => $field ) {
			$count[$name] = count( $field );
		} //
		$count = max( $count );
		return $count;
 	}
}
