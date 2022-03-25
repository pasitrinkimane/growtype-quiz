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

        add_action('admin_menu', array ($this, 'admin_menu'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Growtype_Quiz_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Growtype_Quiz_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->growtype_quiz, plugin_dir_url(__FILE__) . 'css/growtype-quiz-admin.css', array (), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Growtype_Quiz_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Growtype_Quiz_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->growtype_quiz, plugin_dir_url(__FILE__) . 'js/growtype-quiz-admin.js', array ('jquery'), $this->version, false);

    }

    /**
     * Register the options page with the Wordpress menu.
     */
    function admin_menu() {
        // Vars.
        $slug = 'edit.php?post_type=quiz';

//        $cap = 'manage_options';
//
//        // Add menu items.
//        add_menu_page( __("Quizes",'growtype-quiz'), __("Quizes",'growtype-quiz'), $cap, $slug, false, 'dashicons-welcome-learn-more', 20 );
//        add_submenu_page( $slug, __('Quizes','growtype-quiz'), __('Quizes','growtype-quiz'), $cap, $slug );
//        add_submenu_page( $slug, __('Add New','growtype-quiz'), __('Add New','growtype-quiz'), $cap, 'post-new.php?post_type=quiz' );
    }
}
