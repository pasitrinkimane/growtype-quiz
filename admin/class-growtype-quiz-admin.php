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
    use AdminSettingsGeneralTrait;

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

        if (is_admin()) {
            add_action('admin_menu', array ($this, 'add_default_options_page'));

            /**
             * General
             */
            add_action('admin_init', array ($this, 'general_content'));
        }
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

        wp_enqueue_style($this->growtype_quiz, GROWTYPE_QUIZ_URL . 'admin/css/growtype-quiz-admin.css', array (), $this->version, 'all');

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
    function add_default_options_page()
    {
        add_options_page(
            'Growtype - Quiz',
            'Growtype - Quiz',
            'manage_options',
            'growtype-quiz-settings',
            array ($this, 'growtype_quiz_settings_content'),
            1
        );
    }

    /**
     * @return void
     */
    function growtype_quiz_settings_content()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'growtype-quiz-settings') {
            ?>

            <div class="wrap">

                <h1>Growtype - Quiz settings</h1>

                <?php
                if (isset($_GET['updated']) && 'true' == esc_attr($_GET['updated'])) {
                    echo '<div class="updated" ><p>Theme Settings updated.</p></div>';
                }

                if (isset ($_GET['tab'])) {
                    $this->settings_tabs($_GET['tab']);
                } else {
                    $this->settings_tabs();
                }
                ?>

                <form id="growtype_quiz_main_settings_form" method="post" action="options.php">
                    <?php

                    if (isset ($_GET['tab'])) {
                        $tab = $_GET['tab'];
                    } else {
                        $tab = 'general';
                    }

                    switch ($tab) {
                        case 'general':
                            settings_fields('growtype_quiz_settings_general');

                            echo '<table class="form-table">';
                            do_settings_fields('growtype-quiz-settings', 'growtype_quiz_settings_general');
                            echo '</table>';

                            break;
                    }

                    submit_button();

                    ?>
                </form>
            </div>

            <?php
        }
    }

    /**
     * @param $current
     * @return void
     */
    function settings_tabs($current = 'general')
    {
        $tabs['general'] = 'General';

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=growtype-form-settings&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }
}
