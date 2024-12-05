<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/includes
 * @author     Your Name <email@example.com>
 */
class Growtype_Quiz
{
    const TYPE_SCORED = 'scored'; //caculates total results of correct answers
    const TYPE_SCORED_MOST_COMMON_ANSWER = 'scored_most_common_answer';  //calculates most common answer
    const TYPE_POLL = 'poll';
    const TYPE_GENERAL = 'general';
    const STYLE_GENERAL = 'general';
    const PLUGIN_KEY = 'growtype-quiz';

    const TOKEN_KEY= 'gqtoken';

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Growtype_Quiz_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $growtype_quiz The string used to uniquely identify this plugin.
     */
    protected $growtype_quiz;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    private $post_type;

    public function __construct()
    {
        if (defined('GROWTYPE_QUIZ_VERSION')) {
            $this->version = GROWTYPE_QUIZ_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        $this->growtype_quiz = self::PLUGIN_KEY;

        $this->load_admin_traits();

        /**
         * Quiz post type
         */
        $this->post_type = self::get_growtype_quiz_post_type();

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required traits for this plugin.
     */
    private function load_admin_traits()
    {
        /**
         * Admin traits
         */
        spl_autoload_register(function ($traitName) {
            $fileName = GROWTYPE_QUIZ_PATH . 'admin/traits/' . $traitName . '.php';

            if (file_exists($fileName)) {
                include $fileName;
            }
        });
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Growtype_Quiz_Loader. Orchestrates the hooks of the plugin.
     * - Growtype_Quiz_i18n. Defines internationalization functionality.
     * - Growtype_Quiz_Admin. Defines all hooks for the admin area.
     * - Growtype_Quiz_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/class-growtype-quiz-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/class-growtype-quiz-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once GROWTYPE_QUIZ_PATH . 'admin/class-growtype-quiz-admin.php';

        /**
         * Crud
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/methods/crud/growtype-quiz-result-crud.php';
        $this->loader = new Growtype_Quiz_Result_Crud();

        /**
         * Post
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/methods/cpt/growtype-quiz-cpt.php';
        $this->loader = new Growtype_Quiz_Cpt();

        /**
         * Ajax
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/methods/ajax/class-growtype-quiz-ajax.php';
        $this->loader = new Growtype_Quiz_Ajax();

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once GROWTYPE_QUIZ_PATH . 'public/class-growtype-quiz-public.php';

        /**
         * The helper functions
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/helpers/general.php';
        require_once GROWTYPE_QUIZ_PATH . 'includes/helpers/quiz.php';
        require_once GROWTYPE_QUIZ_PATH . 'includes/helpers/results.php';

        /**
         * Shortcode
         */
        require_once GROWTYPE_QUIZ_PATH . 'includes/methods/shortcodes/class-growtype-quiz-input-shortcode.php';
        $this->loader = new Growtype_Quiz_Input_Shortcode();

        require_once GROWTYPE_QUIZ_PATH . 'includes/methods/shortcodes/class-growtype-quiz-loader-shortcode.php';
        $this->loader = new Growtype_Quiz_Loader_Shortcode();

        /**
         * Load
         */
        $this->loader = new Growtype_Quiz_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Growtype_Quiz_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Growtype_Quiz_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Growtype_Quiz_Admin($this->get_growtype_quiz(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Growtype_Quiz_Public($this->get_growtype_quiz(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_growtype_quiz()
    {
        return $this->growtype_quiz;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Growtype_Quiz_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public static function get_growtype_quiz_post_type()
    {
        $custom_post_type = get_option('growtype_quiz_custom_post_type');

        return !empty($custom_post_type) ? $custom_post_type : (defined('GROWTYPE_QUIZ_POST_TYPE') ? GROWTYPE_QUIZ_POST_TYPE : 'quiz');
    }

    /**
     * @return string
     */
    public static function get_growtype_extra_post_types()
    {
        $custom_post_type = get_option('growtype_quiz_extra_post_types');

        return !empty($custom_post_type) ? explode(',', $custom_post_type) : [];
    }

    /**
     * @return string
     */
    public static function get_growtype_quiz_post_types()
    {
        $post_type = self::get_growtype_quiz_post_type();
        $extra_post_type = self::get_growtype_extra_post_types();

        return array_merge([$post_type], $extra_post_type);
    }

    /**
     * @return string
     */
    public static function get_growtype_quiz_post_type_label_name()
    {
        $custom_post_type_name = get_option('growtype_quiz_custom_post_type_label_name');

        return !empty($custom_post_type_name) ? $custom_post_type_name : __('Quizes', 'growtype-quiz');
    }

    /**
     * @return string
     */
    public static function get_growtype_quiz_post_type_label_singular_name()
    {
        $custom_post_type_name = get_option('growtype_quiz_custom_post_type_label_singular_name');

        return !empty($custom_post_type_name) ? $custom_post_type_name : __('Quiz', 'growtype-quiz');
    }
}
