<?php
$post = get_post();
$quiz_data = growtype_quiz_get_quiz_data($post->ID);
$theme_header = get_option('growtype_quiz_theme_header');
$iframe_hide_header_footer = get_option('growtype_quiz_iframe_hide_header_footer') && isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe';

if (!current_user_can('manage_options')) {
    if (!is_null($quiz_data['is_enabled']) && !$quiz_data['is_enabled']) {
        wp_redirect(get_home_url());
    }
}
?>
