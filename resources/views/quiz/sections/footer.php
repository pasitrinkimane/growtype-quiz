<?php do_action('growtype_quiz_section_footer'); ?>

<?php
$theme_footer = get_option('growtype_quiz_theme_footer');
if (!empty($theme_footer)) {
    get_footer($theme_footer);
}
?>
