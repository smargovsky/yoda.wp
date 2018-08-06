<?php
/**
 * Provide the view for a metabox
 *
 * @since       1.0.0
 *
 * @package     Yoda_WP
 * @subpackage  Yoda_WP/admin/partials
 */
global $post;
if ( empty( $post ) ) { return; }
if ( 'announcement' != $post->post_type ) { return; }
$this->meta = get_post_custom( $post->ID );

wp_nonce_field( $this->plugin_name, 'announcement-targeting' );

// ---------------------------- announcement-region ---------------------------------------
?><p><label>Regions</label></p><?php

$regions = array('us-east-1', 'eu-west-1', 'eu-central-1', 'ap-southeast-2', 'ap-northeast-1');
if ( ! empty( $this->meta['announcement-region'][0] ) ) {
    $value = maybe_unserialize( unserialize($this->meta['announcement-region'][0]) );
} else {
    $value = array();
}
foreach ($regions as $region) {
    $atts                   = array();
    $atts['class']          = '';
    $atts['description']    = '';
    $atts['id']             = 'announcement-region-' . $region;
    $atts['label']          = $region;
    $atts['name']           = 'announcement-region-' . $region;
    $atts['placeholder']    = '';
    $atts['type']           = 'checkbox';
    $atts['value']          = array_key_exists($region, $value) ? $value[$region] : 0;

    apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
    ?><p><?php
    include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-checkbox.php' );
    ?></p><?php
}


// --------------------------- announcement-feature-toggles ---------------------------------

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'announcement-feature-toggles';
$atts['label']          = 'Show if these Feature Toggles are On';
$atts['name']           = 'announcement-feature-toggles';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
?></p><?php

// --------------------------- announcement-permissions -------------------------------------

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'announcement-permissions';
$atts['label']          = 'Permissions to Show To';
$atts['name']           = 'announcement-permissions';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
?></p>
