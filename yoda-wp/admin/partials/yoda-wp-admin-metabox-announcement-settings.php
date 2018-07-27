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

wp_nonce_field( $this->plugin_name, 'announcement-settings' );

// --------------------------- announcement-url -------------------------------------

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'url-for-iframe';
$atts['label']          = 'URL';
$atts['name']           = 'announcement-url';
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

// ----------------------------- announcement-type ---------------------------------------

$setatts                                = array();
$setatts['class']                       = 'widefat';
$setatts['id']                          = 'announcement-type';
$setatts['name']                        = 'announcement-type';
$setatts['label']                       = 'Announcement Type';
$i                                      = 0;
$setatts['selections'][$i]['label']     = 'Toast';
$setatts['selections'][$i]['value']     = 'toast';
$i++;
$setatts['selections'][$i]['label']     = 'Pop up';
$setatts['selections'][$i]['value']     = 'pop-up';
$setatts['value']          = '';
if ( ! empty( $this->meta[$setatts['id']][0] ) ) {
    $setatts['value'] = $this->meta[$setatts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $setatts['id'], $setatts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-select.php' );
?></p><?php

// --------------------------- announcement-selector -------------------------------------

$atts                   = array();
$atts['class']          = 'widefat  element-selector';
$atts['description']    = '';
$atts['id']             = 'announcement-selector';
$atts['label']          = 'Select Element on Page';
$atts['name']           = 'announcement-selector';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['icon']           = 'element-selection-mode dashicons dashicons-external';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
?></p><?php

// --------------------------- announcement-show-once -------------------------------------

$atts                   = array();
$atts['class']          = '';
$atts['description']    = '';
$atts['id']             = 'announcement-show-once';
$atts['label']          = 'Show Once?';
$atts['name']           = 'announcement-show-once';
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
