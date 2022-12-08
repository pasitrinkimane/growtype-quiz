<?php

class Growtype_Quiz_Admin_Post
{
    const CUSTOM_SLUG = 'results';

    public function __construct()
    {
        $this->result_crud = new Growtype_Quiz_Admin_Result_Crud();

        add_action('init', array ($this, 'register_post_types'), 5);
        add_action('after_setup_theme', array ($this, 'extend_theme_support'), 5);
        add_action('init', array ($this, 'register_taxonomy'), 5);
        add_action('init', array ($this, 'create_tables'), 5);

        add_filter('single_template', array (__CLASS__, 'single_template_loader'));
        add_filter('page_template', array (__CLASS__, 'page_template_loader'));

        add_action('wp_ajax_growtype_quiz_save_data', array ($this, 'growtype_quiz_save_data_handler'));
        add_action('wp_ajax_nopriv_growtype_quiz_save_data', array ($this, 'growtype_quiz_save_data_handler'));

//        add_filter('acf/load_field/name=questions', array (__CLASS__, 'acf_question_key_default_value'));
//        add_action('init', array ($this, 'custom_url'), 1);
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
                'has_archive' => true,
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
     * Create required tabled
     */
    public function create_tables()
    {
        global $wpdb;

        $quiz_results_table_name = Growtype_Quiz_Admin_Result_Crud::table_name();

        if ($wpdb->get_var("SHOW TABLES LIKE '$quiz_results_table_name'") != $quiz_results_table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $quiz_results_table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      user_id bigint(20) DEFAULT NULL,
      quiz_id bigint(20) UNSIGNED NOT NULL,
      answers TEXT NOT NULL,
      duration INTEGER,
      questions_amount INTEGER,
      correct_answers_amount INTEGER,
      wrong_answers_amount INTEGER,
      evaluated BIT DEFAULT 0,
      created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY id (id)
    ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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
        if (get_post_type() === Growtype_Quiz::get_growtype_quiz_post_type()) {
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
        if (current_user_can('manage_options')) {
            $results_page = get_page_by_path('results');

            if ((empty($results_page) && strpos($_SERVER['REQUEST_URI'], self::CUSTOM_SLUG) === 0) || (!empty($results_page) && $results_page->ID === get_the_ID())) {
                $template = growtype_quiz_include_view('result.index', [], true);
            }
        }

        return $template;
    }

    /**
     * @return void
     */
    public function custom_url()
    {
        add_rewrite_endpoint(self::CUSTOM_SLUG, EP_ROOT);
    }

    /**
     * Handle quiz data
     */
    function growtype_quiz_save_data_handler()
    {
        $quiz_data['answers'] = $_POST['answers'] ?? null;
        $quiz_data['status'] = $_POST['status'] ?? null;
        $quiz_data['quiz_id'] = $_POST['quiz_id'] ?? null;
        $quiz_data['duration'] = $_POST['duration'] ?? null;
        $quiz_data['files'] = $_FILES ?? null;
        $quiz_data['http_referer'] = $_SERVER['HTTP_REFERER'] ?? null;

        if (empty($quiz_data['status'])) {
            return wp_send_json([
                'success' => false,
                'message' => 'Missing status',
            ], 400);
        }

        if (empty($quiz_data['answers'])) {
            return wp_send_json([
                'success' => false,
                'message' => 'Missing answers data',
            ], 400);
        }

        if ($quiz_data['status'] === 'save') {
            $update_quiz_data = $this->result_crud->save_quiz_results_data($quiz_data);

            if ($update_quiz_data) {
                return wp_send_json([
                    'success' => true,
                    'redirect_url' => class_exists('ACF') && get_field('success_url', $quiz_data['quiz_id']) ?? null,
                ]);
            }

            return wp_send_json([
                'success' => false,
                'message' => 'Missing user',
            ]);
        } elseif ($quiz_data['status'] === 'evaluate_result') {
            foreach ($quiz_data['answers'] as $id => $answer) {
                $results_data = $this->result_crud->get_quiz_single_result_data($id);
                $correct_answers_amount = $results_data['correct_answers_amount'];
                $wrong_answers_amount = $results_data['wrong_answers_amount'];

                if ($answer === 'true') {
                    $correct_answers_amount = $correct_answers_amount + 1;
                } else {
                    $wrong_answers_amount = $wrong_answers_amount + 1;
                }

                $this->result_crud->update_quiz_single_result($id, [
                    'correct_answers_amount' => $correct_answers_amount,
                    'wrong_answers_amount' => $wrong_answers_amount,
                    'evaluated' => true,
                ]);
            }

            return wp_send_json([
                'success' => true,
            ]);
        }

        exit();
    }

    /**
     * @return array|bool|object|null
     * Get admin quiz data
     */
    public function get_quiz_data($quiz_id)
    {
        $quiz_data['quiz_type'] = get_field('quiz_type', $quiz_id);
        $quiz_data['is_test_mode'] = get_field('is_test_mode', $quiz_id) ?? false;
        $quiz_data['is_enabled'] = get_field('is_enabled', $quiz_id) ?? false;
        $quiz_data['save_answers'] = get_field('save_answers', $quiz_id);
        $quiz_data['show_correct_answers_initially'] = get_field('show_correct_answers_initially', $quiz_id);
        $quiz_data['slide_counter'] = get_field('slide_counter', $quiz_id);
        $quiz_data['slide_counter_position'] = get_field('slide_counter_position', $quiz_id);
        $quiz_data['limited_time'] = get_field('limited_time', $quiz_id);
        $quiz_data['duration'] = get_field('duration', $quiz_id);
        $quiz_data['progress_bar'] = get_field('progress_bar', $quiz_id);
        $quiz_data['use_question_title_nav'] = get_field('use_question_title_nav', $quiz_id);
        $quiz_data['questions'] = !empty(get_field('questions', $quiz_id)) ? get_field('questions', $quiz_id) : [];

        $has_success_question = array_filter($quiz_data['questions'], function ($question) {
            return $question['question_type'] === 'success';
        });

        if (empty($has_success_question)) {
            $success_question = apply_filters('growtype_quiz_add_success_question', []);

            if (!empty($success_question)) {
                $quiz_data['questions'][] = $success_question;
            }
        }

        $quiz_data['questions_available'] = isset($quiz_data['questions']) && !empty($quiz_data['questions']) ? array_filter($quiz_data['questions'], function ($question) {
            $disabled = $question['disabled'] ?? false;
            return $question['question_type'] !== 'info' && $question['question_type'] !== 'success' && !$disabled;
        }) : '';

        $quiz_data['questions_available_amount'] = isset($quiz_data['questions_available']) && !empty($quiz_data['questions_available']) ? count($quiz_data['questions_available']) : '';

        return $quiz_data;
    }

    /**
     * @param $quiz_id
     * @param $answers
     */
    public function evaluate_quiz_answers($quiz_id, $user_answers)
    {
        $quiz_data = $this->get_quiz_data($quiz_id);
        $questions = $quiz_data['questions'];

        $correct_answers = 0;
        $wrong_answers = 0;
        foreach ($user_answers as $key => $user_answer) {
            $question = array_filter($questions, function ($question) use ($key) {
                return $question['key'] === $key;
            });

            if (!empty($question)) {
                $answer = array_filter(array_values($question)[0]['options_all'], function ($option) use ($user_answer) {
                    return $option['value'] === array_values($user_answer)[0];
                });
            }

            if (!empty($question) && isset($answer) && !empty($answer)) {
                $answer_correct = array_values($answer)[0]['correct'] ?? null;
                if ($answer_correct) {
                    $correct_answers++;
                } else {
                    $wrong_answers++;
                }
            }
        }

        return [
            'correct_answers_amount' => $correct_answers,
            'wrong_answers_amount' => $wrong_answers,
        ];
    }

    /**
     *Acf
     */
    public static function acf_question_key_default_value($field)
    {
        global $post;
//
//        if (get_post_meta($post->ID, $field['name'], true) == '') {
//            $field['value'] = 'FOO';
//        }

//        echo '<pre>' . var_export($field['sub_fields'], true) . '</pre>';

//
        $altered_field = $field;
        $altered_field['sub_fields'] = [];

        foreach ($field['sub_fields'] as $sub_field) {
            if ($sub_field['name'] === 'key' && empty($sub_field['value'])) {
                $sub_field['value'] = 'test';
            }

            array_push($altered_field['sub_fields'], $sub_field);
        }

//        echo '<pre>' . var_export(!empty($altered_field['sub_fields']) ? $altered_field : $field, true) . '</pre>';
//        die();

        return !empty($altered_field['sub_fields']) ? $altered_field : $field;
    }

    /**
     * @param $file
     * @return array
     */
    public function upload_file_to_media_library($file)
    {
        $file_name = basename($file["name"]);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_mime = mime_content_type($file['tmp_name']);

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_featured_image = wp_handle_upload($file, array ('test_form' => false));

        if (isset($upload_featured_image['error'])) {
            $response['success'] = false;
            $response['message'] = $upload_featured_image['error'];

            return $response;
        }

        $upload_featured_image_path = $upload_featured_image['file'];

        $upload_id = wp_insert_attachment(array (
            'guid' => $upload_featured_image_path,
            'post_mime_type' => $file_mime,
            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
            'post_content' => '',
            'post_status' => 'inherit'
        ), $upload_featured_image_path);

        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $upload_featured_image_path));

        $response['attachment_id'] = $upload_id;

        return $response;
    }
}


