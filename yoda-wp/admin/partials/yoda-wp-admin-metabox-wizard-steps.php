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

wp_nonce_field( $this->plugin_name, 'wizard-steps-repeater' );
$count      = 1;
$setatts                    = array();
$setatts['class']           = 'repeater';
$setatts['id']              = 'wizard-steps-repeater';
$setatts['label-add']       = 'Add Step';
$setatts['label-edit']      = 'Edit Step';
$setatts['label-header']    = 'Create Step';
$setatts['label-remove']    = 'Remove Step';
$setatts['title-field']     = 'step-title'; // which field provides the title for each fieldset?
$i                          = 0;
$setatts['fields'][$i]['text']['class']                 = 'widefat repeater-title';
$setatts['fields'][$i]['text']['description']           = '';
$setatts['fields'][$i]['text']['id']                    = 'step-title';
$setatts['fields'][$i]['text']['label']                 = 'Step Title';
$setatts['fields'][$i]['text']['name']                  = 'step-title';
$setatts['fields'][$i]['text']['placeholder']           = 'Step ' . $count;
$setatts['fields'][$i]['text']['type']                  = 'text';
$setatts['fields'][$i]['text']['value']                 = '';
$i++;
$setatts['fields'][$i]['text']['class']                 = 'widefat element-selector';
$setatts['fields'][$i]['text']['description']           = '';
$setatts['fields'][$i]['text']['id']                    = 'step-selector';
$setatts['fields'][$i]['text']['label']                 = 'Select Element on Page';
$setatts['fields'][$i]['text']['name']                  = 'step-selector';
$setatts['fields'][$i]['text']['placeholder']           = 'Click to select an element';
$setatts['fields'][$i]['text']['icon']                  = 'element-selection-mode dashicons dashicons-external';
$setatts['fields'][$i]['text']['type']                  = 'text';
$setatts['fields'][$i]['text']['value']                 = '';
$i++;
$setatts['fields'][$i]['editor']['description']    = '';
$setatts['fields'][$i]['editor']['id']             = 'stepContent';
$setatts['fields'][$i]['editor']['name']             = 'stepContent';
$setatts['fields'][$i]['editor']['label']          = 'Content';
$setatts['fields'][$i]['editor']['settings']['textarea_name'] = 'stepContent';
$setatts['fields'][$i]['editor']['settings']['tinymce'] = false;
$setatts['fields'][$i]['editor']['settings']['quicktags'] = true;
$setatts['fields'][$i]['editor']['value']          = '';

$i++;

apply_filters( $this->plugin_name . '-field-repeater-wizard-steps', $setatts );

$repeater   = array();
if ( ! empty( $this->meta[$setatts['id']] ) ) {
    $repeater = maybe_unserialize( $this->meta[$setatts['id']][0] );
}
if ( ! empty( $repeater ) ) {
    $count = count( $repeater );
}
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-repeater.php' );
