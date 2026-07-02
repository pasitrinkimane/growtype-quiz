<?php

/**
 *
 */
if (!function_exists('growtype_quiz_render_svg')) {
    function growtype_quiz_render_svg($url)
    {
        $arrContextOptions = [
            "ssl" => array (
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        ];

        $response = file_get_contents(
            $url,
            false,
            stream_context_create($arrContextOptions)
        );

        return $response;
    }
}

/**
 * @param $path
 * @param null $data
 * @return mixed
 * Include view
 */
if (!function_exists('growtype_quiz_include_resource')) {
    function growtype_quiz_include_resource($file_path, $variables = array (), $print = false)
    {
        $output = null;

        $plugin_root = plugin_dir_path(__DIR__);
        $full_file_path = $plugin_root . 'resources/' . $file_path;

        if (file_exists($full_file_path)) {
            // Extract the variables to a local namespace
            extract($variables);

            // Start output buffering
            ob_start();

            // Include the template file
            include $full_file_path;

            // End buffering and return its contents
            $output = ob_get_clean();
        }

        if ($print) {
            print $output;
        }

        return $output;
    }
}

/**
 * Include custom view
 */
if (!function_exists('growtype_quiz_include_view')) {
    function growtype_quiz_include_view($file_path, $variables = array (), $only_template_path = false)
    {
        $stylesheet_dir = strpos(get_stylesheet_directory(), 'resources') !== false ? get_stylesheet_directory() : get_stylesheet_directory() . '/resources';

        /**
         * Ordered list of views root directories to search.
         * The base child-theme path is always the last entry so it acts as the final fallback.
         * Domains/plugins should use array_unshift() to prepend their path with highest priority.
         *
         * @param string[] $dirs  Absolute paths to views root directories (no trailing slash).
         */
        $views_dirs = apply_filters('growtype_quiz_views_dirs', [$stylesheet_dir]);

        $fallback_view = GROWTYPE_QUIZ_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.php';
        $fallback_blade_view = GROWTYPE_QUIZ_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.blade.php';

        $template_path = $fallback_view;
        $relative      = str_replace('.', '/', $file_path);

        foreach ($views_dirs as $dir) {
            $blade = rtrim($dir, '/') . '/views/' . GROWTYPE_QUIZ_TEXT_DOMAIN . '/' . $relative . '.blade.php';
            $php   = rtrim($dir, '/') . '/views/' . GROWTYPE_QUIZ_TEXT_DOMAIN . '/' . $relative . '.php';

            if (file_exists($blade) && function_exists('App\template')) {
                if (!$only_template_path) {
                    return App\template($blade, $variables);
                }
                $template_path = $blade;
                break;
            } elseif (file_exists($php)) {
                $template_path = $php;
                break;
            }
        }

        if ($template_path === $fallback_view) {
            if (file_exists($fallback_blade_view) && function_exists('App\template')) {
                if (!$only_template_path) {
                    return App\template($fallback_blade_view, $variables);
                }
                $template_path = $fallback_blade_view;
            }
        }

        if ($only_template_path) {
            return $template_path;
        }

        if (file_exists($template_path)) {
            extract($variables);
            ob_start();
            include $template_path;
            $output = ob_get_clean();
        }

        return isset($output) ? $output : '';
    }
}






