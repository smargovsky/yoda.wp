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
if ( 'wizard' != $post->post_type ) { return; }
$this->meta = get_post_custom( $post->ID );

wp_nonce_field( $this->plugin_name, 'wizard-settings' );

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'url-for-iframe';
$atts['label']          = 'URL';
$atts['name']           = 'wizard-url';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['name']][0] ) ) {
    $atts['value'] = $this->meta[$atts['name']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['name'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
?></p><?php

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'wizard-permissions';
$atts['label']          = 'Permissions to Show To';
$atts['name']           = 'wizard-permissions';
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

$atts                   = array();
$atts['class']          = '';
$atts['description']    = '';
$atts['id']             = 'wizard-show-once';
$atts['label']          = 'Show Once?';
$atts['name']           = 'wizard-show-once';
$atts['placeholder']    = '';
$atts['type']           = 'checkbox';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-checkbox.php' );
?></p>
<div id="dialog-for-iframe">
    <iframe id="iframe-for-element-selection" name="iframe-for-element-selection" seamless="seamless" width='800' height='800'></iframe>
</div>
