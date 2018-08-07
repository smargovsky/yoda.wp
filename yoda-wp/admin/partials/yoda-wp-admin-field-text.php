<?php
/**
 * Provides the markup for any text field
 *
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/admin/partials
 */
if ( ! empty( $atts['label'] ) ) {
    ?><label for="<?php echo esc_attr( $atts['id'] ); ?>"><?php esc_html_e( $atts['label'], 'yoda-wp' ); ?>: </label><?php
}
?><input
    class="<?php echo esc_attr( $atts['class'] ); ?>"
    id="<?php echo esc_attr( $atts['id'] ); ?>"
    name="<?php echo esc_attr( $atts['name'] ); ?>"
    placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>"
    type="<?php echo esc_attr( $atts['type'] ); ?>"
    value="<?php echo esc_attr( $atts['value'] ); ?>"
    <?php if ( ! empty( $atts['readonly'] ) ) { ?> readonly <?php } ?> /><?php
if ( ! empty( $atts['icon'] ) ) {
    ?><span class="<?php echo esc_attr( $atts['icon'] ); ?>"></span><?php
}
if ( ! empty( $atts['description'] ) ) {
    ?><span class="description"><?php esc_html_e( $atts['description'], 'yoda-wp' ); ?></span><?php
}
