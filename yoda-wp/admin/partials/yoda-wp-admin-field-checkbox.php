<?php
/**
 * Provides the markup for any checkbox field
 *
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/admin/partials
 */
?><label for="<?php echo esc_attr( $atts['id'] ); ?>">
    <input aria-role="checkbox"
        <?php checked( 1, $atts['value'], true ); ?>
        class="<?php echo esc_attr( $atts['class'] ); ?>"
        id="<?php echo esc_attr( $atts['id'] ); ?>"
        name="<?php echo esc_attr( $atts['name'] ); ?>"
        type="checkbox"
        value="1"
        <?php if ( isset( $atts['disabled'] ) && $atts['disabled'] ) echo 'disabled'; ?>/>
    <span class="description"><?php esc_html_e( $atts['label'], 'yoda-wp' ); ?></span>
</label>
