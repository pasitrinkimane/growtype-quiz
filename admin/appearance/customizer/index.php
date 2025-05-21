<?php

/**
 * Add pages to customizer
 */
add_filter('growtype_customizer_extend_available_pages', function ($pages) {
    $pages['quiz'] = 'Quiz';

    return $pages;
}, 0, 1);

/**
 * Enabled pages
 */
add_filter('growtype_page_is_among_enabled_pages', function ($page_enabled, $enabled_pages) {
    if (in_array(Growtype_Quiz::get_growtype_quiz_post_type(), $enabled_pages) && get_post_type() === Growtype_Quiz::get_growtype_quiz_post_type()) {
        $page_enabled = true;
    }

    return $page_enabled;
}, 100, 2);
