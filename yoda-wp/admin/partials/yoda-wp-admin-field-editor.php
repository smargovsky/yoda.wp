<?php
/**
 * Provides the markup for any WP Editor field
 *
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/admin/partials
 */
// wp_editor( $content, $editor_id, $settings = array() );
if ( ! empty( $atts['label'] ) ) {
    $id = uniqid('php_');
    ?><label for="<?php
    echo esc_attr( $id );
    ?>"><?php
        esc_html_e( $atts['label'], 'yoda-wp' );
    ?>: </label><?php
}
wp_editor( html_entity_decode( $atts['value'] ), $id, $atts['settings'] );
?><span class="description"><?php esc_html_e( $atts['description'], 'yoda-wp' ); ?></span>
