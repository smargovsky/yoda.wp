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

// ----------------------------- announcement-type ---------------------------------------

$atts                               = array();
$atts['class']                      = 'widefat';
$atts['id']                         = 'announcement-type';
$atts['name']                       = 'announcement-type';
$atts['label']                      = 'Announcement Type';
$i                                  = 0;
$atts['selections'][$i]['label']    = 'Toast';
$atts['selections'][$i]['value']    = 'toast';
$i++;
$atts['selections'][$i]['label']    = 'Pop up';
$atts['selections'][$i]['value']    = 'pop-up';
$atts['value']                      = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-select.php' );
?></p><?php

// ----------------------------- open iframe btn -----------------------------------------
?>
<p>
    <button id="open-iframe" class="button button-primary button-large">Select Page / Element</button>
</p>
<?php

// ---------------------------- announcement-env ---------------------------------------
?><p><label>Environments</label></p><?php

$envs = [
    'dca'=> 'Development',
    'tca'=> 'Test',
    'pca' => 'Production'
];
if ( ! empty( $this->meta['announcement-env'][0] ) ) {
    $env_meta_values = maybe_unserialize( unserialize($this->meta['announcement-env'][0]) );
} else {
    $env_meta_values = array();
}
foreach ($envs as $env_key => $env_value) {
    $atts                   = array();
    $atts['class']          = '';
    $atts['description']    = '';
    $atts['id']             = 'announcement-env-' . $env_key;
    $atts['label']          = $env_value;
    $atts['name']           = 'announcement-env-' . $env_key;
    $atts['placeholder']    = '';
    $atts['type']           = 'checkbox';
    $atts['value']          = array_key_exists($env_key, $env_meta_values) ? $env_meta_values[$env_key] : 0;

    apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
    ?><p><?php
    include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-checkbox.php' );
    ?></p><?php
}
// --------------------------- announcement-url -------------------------------------

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'announcement-url';
$atts['label']          = 'URL';
$atts['name']           = 'announcement-url';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['value']          = '';
$atts['readonly']       = true;
if ( ! empty( $this->meta[$atts['name']][0] ) ) {
    $atts['value'] = $this->meta[$atts['name']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['name'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
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
$atts['value']          = '';
$atts['readonly']       = true;
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
    <p>
        <label for="url-for-iframe">Select App Environment</label>
        <select name="url-for-iframe" id="url-for-iframe">
            <option value="https://localhost:4300/dca" selected="selected">Localhost</option>
            <option value="https://apps.inindca.com">Development</option>
            <option value="https://apps.inintca.com">Testing</option>
            <option value="https://apps.mypurecloud.com">Production</option>
        </select>
    </p>
    <iframe id="iframe-for-element-selection" name="iframe-for-element-selection" seamless="seamless" width='800' height='800'></iframe>
</div>
