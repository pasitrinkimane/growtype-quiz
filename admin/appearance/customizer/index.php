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
add_filter('growtype_page_is_among_enabled_pages', function ($status, $pages) {
    if (in_array('quiz', $pages) && get_post_type() === 'quiz') {
        $status = true;
    }

    return $status;
}, 100, 2);
