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
if ( 'wizard' != $post->post_type && 'announcement' != $post->post_type ) { return; }
$this->meta = get_post_meta( $post->ID, 'translations' );

$langs = array( 'es', 'jp', 'no', 'ru', 'de', 'fr' );

$availableLangs = is_array($this->db::getGuideAvailableTranslations($post)) ? array_keys($this->db::getGuideAvailableTranslations($post)) : [];

foreach ($langs as $lang) {
    if ( in_array($lang, $availableLangs) ) { ?>
        <p>
            <span class="dashicons dashicons-thumbs-up" style="color: #5cb85c"></span><strong style="color:#5cb85c"> <?php echo $lang; ?> </strong>
        </p>
    <?php } else { ?>
        <p>
            <span class="dashicons dashicons-thumbs-down" style="color: red"></span><strong style="color:red"> <?php echo $lang; ?> </strong>
        </p>
    <?php }
}
