<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/public
 * @author     Your Name <email@example.com>
 */
class Growtype_Quiz_Public
{
    const DEFAULT_UNIT_SYSTEM = 'metric';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $growtype_quiz The ID of this plugin.
     */
    private $growtype_quiz;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $growtype_quiz The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($growtype_quiz, $version)
    {
        $this->growtype_quiz = $growtype_quiz;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * Main
         */
        wp_enqueue_style($this->growtype_quiz, GROWTYPE_QUIZ_URL_PUBLIC . 'css/growtype-quiz-public.css', array (), $this->version, 'all');

        /**
         * Themes
         */
        if (get_option('growtype_quiz_theme') === 'theme-1') {
            wp_enqueue_style($this->growtype_quiz . '/theme-1', GROWTYPE_QUIZ_URL_PUBLIC . 'css/growtype-quiz-public-theme-1.css', array (), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->growtype_quiz, GROWTYPE_QUIZ_URL_PUBLIC . 'js/growtype-quiz-public.js', array ('jquery'), time(), false);

        $localize_data = array (
            'ajax_url' => admin_url('admin-ajax.php'),
            'unique_hash' => wp_generate_password(44, false, false),
            'unit_system' => Growtype_Quiz_Public::DEFAULT_UNIT_SYSTEM,
        );

        $post = get_post();

        if (!empty($post)) {
            $quiz_data = growtype_quiz_get_quiz_data($post->ID);

            if (!empty($quiz_data)) {
                if (!current_user_can('manage_options')) {
                    if (!is_null($quiz_data['is_enabled']) && !$quiz_data['is_enabled']) {
                        wp_redirect(get_home_url());
                    }
                }

                $localize_data['show_correct_answer'] = $quiz_data['show_correct_answer'] ? true : false;
                $localize_data['show_question_nr_in_url'] = $quiz_data['show_question_nr_in_url'] ? true : false;
                $localize_data['correct_answer_trigger'] = $quiz_data['correct_answer_trigger'];
                $localize_data['save_data_on_load'] = $quiz_data['save_data_on_load'];
                $localize_data['save_answers'] = $quiz_data['save_answers'] === false ? 'false' : 'true';
            }
        }

        wp_localize_script($this->growtype_quiz, 'growtype_quiz_local',
            $localize_data
        );
    }
}
