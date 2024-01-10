<?php

class Growtype_Quiz_Cpt
{

    public function __construct()
    {
        add_action('init', array ($this, 'register_post_types'), 5);
        add_action('after_setup_theme', array ($this, 'extend_theme_support'), 5);
        add_action('init', array ($this, 'register_taxonomy'), 5);
        add_action('init', array ($this, 'create_tables'), 5);
        add_filter('single_template', array (__CLASS__, 'single_template_loader'));
        add_filter('page_template', array (__CLASS__, 'page_template_loader'));
    }

    /**
     * Registers the post types.
     */
    public function register_post_types()
    {
        $post_type = Growtype_Quiz::get_growtype_quiz_post_type();
        $post_type_name = Growtype_Quiz::get_growtype_quiz_post_type_label_name();
        $post_type_singular_name = Growtype_Quiz::get_growtype_quiz_post_type_label_singular_name();

        register_post_type($post_type,
            array (
                'labels' => array (
                    'name' => $post_type_name,
                    'singular_name' => $post_type_singular_name
                ),
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_icon' => 'dashicons-welcome-learn-more',
                'has_archive' => false,
                'supports' => array ('title', 'editor', 'thumbnail'),
            )
        );
    }

    public function extend_theme_support()
    {
        add_theme_support('post-thumbnails');
    }

    /**
     * Registers the post types.
     */
    public function register_taxonomy()
    {
        $tax = defined('GROWTYPE_QUIZ_TAXONOMY') ? GROWTYPE_QUIZ_TAXONOMY : 'quiz_cat';
        $post_type = defined('GROWTYPE_QUIZ_POST_TYPE') ? GROWTYPE_QUIZ_POST_TYPE : 'quiz';

        $labels = array (
            'name' => __('Category', $tax, 'growtype-quiz'),
            'singular_name' => __('Category', $tax, 'growtype-quiz'),
        );

        $args = array (
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'show_in_menu' => true,
        );

        register_taxonomy($tax, $post_type, $args);
    }

    /**
     * Create required table
     */
    public function create_tables()
    {
        global $wpdb;

        $quiz_results_table_name = Growtype_Quiz_Result_Crud::table_name();

        if ($wpdb->get_var("SHOW TABLES LIKE '$quiz_results_table_name'") != $quiz_results_table_name) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $quiz_results_table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      user_id bigint(20) DEFAULT NULL,
      quiz_id bigint(20) UNSIGNED NOT NULL,
      answers TEXT DEFAULT NULL,
      duration INTEGER,
      questions_amount INTEGER,
      questions_answered INTEGER,
      correct_answers_amount INTEGER,
      wrong_answers_amount INTEGER,
      evaluated BIT DEFAULT 0,
      unique_hash TEXT NOT NULL,
      extra_details TEXT DEFAULT NULL,
      ip_address TEXT DEFAULT NULL,
      created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY id (id),
      INDEX user_id (user_id),
      INDEX quiz_id (quiz_id)
    ) $charset_collate;";

            dbDelta($sql);
        }
    }

    /**
     * Load a template.
     *
     * Handles template usage so that we can use our own templates instead of the theme's.
     *
     * Templates are in the 'templates' folder.
     * @param string $template Template to load.
     * @return string
     */
    public static function single_template_loader($template)
    {
        $extra_post_types = Growtype_Quiz::get_growtype_extra_post_types();

        if (get_post_type() === Growtype_Quiz::get_growtype_quiz_post_type() || in_array(get_post_type(), $extra_post_types)) {
            $template = growtype_quiz_include_view('quiz.index', [], true);
        }

        return $template;
    }

    /**
     * Load a template.
     *
     * Handles template usage so that we can use our own templates instead of the theme's.
     *
     * Templates are in the 'templates' folder.
     * @param string $template Template to load.
     * @return string
     */
    public static function page_template_loader($template)
    {
        $results_page_id = get_option('growtype_quiz_results_page');

        if (!empty($results_page_id) && (int)$results_page_id === get_the_ID()) {
            $template = growtype_quiz_include_view('result.index', [], true);
        }

        return $template;
    }
}


