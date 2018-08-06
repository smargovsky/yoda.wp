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
$this->meta_last_sync = get_post_meta( $post->ID, 'translations-last-sync' );
$this->meta_last_sync = $this->meta_last_sync ? 'last updated ' . Yoda_WP_Util::time_elapsed_string(current($this->meta_last_sync)) : 'translations have not been updated';

$langs = [
    ['code' => 'da', 'display' => 'Danish'],
    ['code' => 'de', 'display' => 'German'],
    ['code' => 'es', 'display' => 'Spanish'],
    ['code' => 'fi', 'display' => 'Finnish'],
    ['code' => 'fr', 'display' => 'French'],
    ['code' => 'it', 'display' => 'Italian'],
    ['code' => 'ja', 'display' => 'Japanese'],
    ['code' => 'ko', 'display' => 'Korean'],
    ['code' => 'nl', 'display' => 'Dutch'],
    ['code' => 'no', 'display' => 'Norwegian'],
    ['code' => 'pl', 'display' => 'Polish'],
    ['code' => 'pt-br', 'display' => 'Portuguese (Brazil)'],
    ['code' => 'sv', 'display' => 'Swedish'],
    // ['code' => 'th', 'display' => 'Thai'],
    ['code' => 'tr', 'display' => 'Turkish'],
    ['code' => 'zh-cn', 'display' => 'Chinese (S)'],
    ['code' => 'zh-tw', 'display' => 'Chinese (T)']
];

$availableLangs = is_array($this->db::getGuideAvailableTranslations($post)) ? array_keys($this->db::getGuideAvailableTranslations($post)) : [];

foreach ($langs as $lang) {
    if ( in_array($lang['code'], $availableLangs) ) { ?>
        <p>
            <span class="dashicons dashicons-yes" style="color: #5cb85c"></span>
            <span style="color:#5cb85c"> <?php echo $lang['display']; ?> </span>
        </p>
    <?php } else { ?>
        <p>
            <span class="dashicons dashicons-no" style="color: gray"></span>
            <span style="color:gray"> <?php echo $lang['display']; ?> </span>
        </p>
    <?php }
}

?>

<hr/>

<div style="text-align:center;opacity:.7;font-size: .75em;"><em><?php echo $this->meta_last_sync ?></em></div>
