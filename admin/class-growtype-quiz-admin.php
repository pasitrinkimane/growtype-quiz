<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/admin
 * @author     Your Name <email@example.com>
 */
class Growtype_Quiz_Admin
{
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
     * @param string $growtype_quiz The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($growtype_quiz, $version)
    {
        $this->growtype_quiz = $growtype_quiz;
        $this->version = $version;

        $this->load_settings();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->growtype_quiz, GROWTYPE_QUIZ_URL . 'admin/css/growtype-quiz-admin.css', array (), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->growtype_quiz, plugin_dir_url(__FILE__) . 'js/growtype-quiz-admin.js', array ('jquery'), $this->version, false);
    }

    /**
     * @return void
     */
    private function load_settings()
    {
        /**
         * Appearance
         */
        require_once GROWTYPE_QUIZ_PATH . 'admin/appearance/customizer/index.php';

        /**
         * Settings
         */
        require_once GROWTYPE_QUIZ_PATH . 'admin/methods/setting/growtype-quiz-admin-setting.php';
        $this->loader = new Growtype_Quiz_Admin_Setting();

        /**
         * Result
         */
        require_once GROWTYPE_QUIZ_PATH . 'admin/methods/result/growtype-quiz-admin-result.php';
        $this->loader = new Growtype_Quiz_Admin_Result();

        /**
         * Statistics
         */
        require_once GROWTYPE_QUIZ_PATH . 'admin/methods/statistic/growtype-quiz-admin-statistic.php';
        $this->loader = new Growtype_Quiz_Admin_Statistic();
    }
}
