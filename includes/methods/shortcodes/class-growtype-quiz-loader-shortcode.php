<?php

/**
 *
 */
class Growtype_Quiz_Loader_Shortcode
{
    function __construct()
    {
        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_quiz_loader', array ($this, 'growtype_quiz_loader_shortcode'));
        }
    }

    /**
     * @param $attr
     * @return string
     * Posts shortcode
     */
    function growtype_quiz_loader_shortcode($attr)
    {
        $params = [
            'heading' => __('Calculating', 'growtype-quiz') . ' ' . '<span class="growtype-quiz-loader-percentage">0%</span>',
            'content' => '',
            'redirect' => isset($attr['redirect']) ? $attr['redirect'] : 'true',
            'redirect_url' => $this->format_done_url(isset($attr['redirect_url']) ? $attr['redirect_url'] : ''),
            'continue_btn_text' => __('Continue', 'growtype-quiz'),
            'duration' => isset($attr['duration']) ? $attr['duration'] : '20',
        ];

        $params = apply_filters('growtype_quiz_loader_params', $params);

        ob_start();

        echo growtype_quiz_include_view('quiz.partials.components.loader', ['params' => $params]);

        return ob_get_clean();
    }

    function format_done_url($done_url)
    {
        if ($done_url === '#results') {
            $done_url = growtype_quiz_results_page_url();
        }

        return $done_url;
    }
}
