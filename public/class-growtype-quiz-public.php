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
        $scripts_should_be_loaded = $this->scripts_should_be_loaded();

        if ($scripts_should_be_loaded) {
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
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        $scripts_should_be_loaded = $this->scripts_should_be_loaded();

        if ($scripts_should_be_loaded) {
            /**
             * Add custom scripts to footer
             */
            add_action('wp_footer', array ($this, 'add_custom_scripts_to_footer'), 100);

            /**
             * Main
             */
            wp_enqueue_script($this->growtype_quiz, GROWTYPE_QUIZ_URL_PUBLIC . 'js/growtype-quiz-public.js', array ('jquery'), $this->version, true);

            $unique_hash = wp_generate_password(44, false);

            /**
             * Set local variables
             */
            $localize_data = array (
                'ajax_url' => admin_url('admin-ajax.php'),
                'unique_hash' => $unique_hash,
                'unit_system' => Growtype_Quiz_Public::DEFAULT_UNIT_SYSTEM,
            );

            if (isset($_GET[Growtype_Quiz::TOKEN_KEY]) && !empty($_GET[Growtype_Quiz::TOKEN_KEY])) {
                $localize_data[Growtype_Quiz::TOKEN_KEY] = $_GET[Growtype_Quiz::TOKEN_KEY];
            }

            wp_localize_script(Growtype_Quiz::PLUGIN_KEY, 'growtype_quiz_local',
                $localize_data
            );
        }
    }

    public function add_custom_scripts_to_footer()
    {
        ?>
        <script type="text/javascript">
            if (
                new URLSearchParams(window.location.search).get('question') === '1'
                || new URLSearchParams(window.location.search).get('question') === null
                || !$('.growtype-quiz').attr('data-show-question-nr-in-url')
            ) {
                sessionStorage.setItem('growtype_quiz_global', JSON.stringify({}));

                if (window.growtype_quiz_data === undefined) {
                    window.growtype_quiz_data = {};
                }

                window.growtype_quiz_data.answers = {};
                sessionStorage.setItem('growtype_quiz_answers', JSON.stringify(window.growtype_quiz_data.answers));
            }

            window.growtype_quiz_global = sessionStorage.getItem('growtype_quiz_global') !== null ? JSON.parse(sessionStorage.getItem('growtype_quiz_global')) : {};

            window.growtype_quiz_global.files = window.growtype_quiz_global.files instanceof FormData ? window.growtype_quiz_global.files : new FormData();
            window.growtype_quiz_global.already_visited_questions_keys = window.growtype_quiz_global.already_visited_questions_keys ? window.growtype_quiz_global.already_visited_questions_keys : [];
            window.growtype_quiz_global.already_visited_questions_funnels = window.growtype_quiz_global.already_visited_questions_funnels ? window.growtype_quiz_global.already_visited_questions_funnels : [];
            window.growtype_quiz_global.initial_funnel = window.growtype_quiz_global.initial_funnel ? window.growtype_quiz_global.initial_funnel : 'a';
            window.growtype_quiz_global.current_funnel = window.growtype_quiz_global.current_funnel ? window.growtype_quiz_global.current_funnel : window.growtype_quiz_global.initial_funnel;
            window.growtype_quiz_global.additional_questions_amount = window.growtype_quiz_global.additional_questions_amount ? window.growtype_quiz_global.additional_questions_amount : 0;
            window.growtype_quiz_global.current_question_counter_nr = window.growtype_quiz_global.current_question_counter_nr ? window.growtype_quiz_global.current_question_counter_nr : 1;
            window.growtype_quiz_global.unit_system = window.growtype_quiz_global.unit_system ? window.growtype_quiz_global.unit_system : window.growtype_quiz_local.unit_system;
        </script>
        <?php
    }

    /**
     * @return bool
     */
    public function scripts_should_be_loaded()
    {
        $posts = Growtype_Quiz::get_growtype_quiz_post_types();

        $scripts_should_be_loaded = apply_filters('growtype_quiz_scripts_should_be_loaded', false);

        if (!$scripts_should_be_loaded) {
            if (!is_admin() && !empty($posts)) {
                foreach ($posts as $post_type) {
                    if (get_post_type() === $post_type) {
                        $scripts_should_be_loaded = true;
                        break;
                    }
                }
            }
        }

        return $scripts_should_be_loaded;
    }
}
