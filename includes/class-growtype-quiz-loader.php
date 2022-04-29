<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/includes
 */

use function App\sage;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/includes
 * @author     Your Name <email@example.com>
 */
class Growtype_Quiz_Loader
{

    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    const CUSTOM_SLUG = 'quiz-results';

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        global $wpdb;

        $this->actions = array ();
        $this->filters = array ();

        $this->quiz_results_table_name = $wpdb->prefix . 'quiz_results';

        add_action('init', array ($this, 'register_post_types'), 5);
        add_action('init', array ($this, 'register_taxonomy'), 5);
        add_action('init', array ($this, 'create_tables'), 5);

        add_filter('single_template', array (__CLASS__, 'single_template_loader'));
        add_filter('page_template', array (__CLASS__, 'page_template_loader'));

        add_action('wp_ajax_quiz_data', array ($this, 'quiz_data'), 5);
        add_action('wp_ajax_nopriv_quiz_data', array ($this, 'quiz_data'), 5);

//        add_action('init', array ($this, 'custom_url'), 1);
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @param string $hook The name of the WordPress action that is being registered.
     * @param object $component A reference to the instance of the object on which the action is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     * @since    1.0.0
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
     * @since    1.0.0
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority The priority at which the function should be fired.
     * @param int $accepted_args The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     * @since    1.0.0
     * @access   private
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {

        $hooks[] = array (
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;

    }

    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {

        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array ($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array ($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

    }

    /**
     * Registers the post types.
     */
    function register_post_types()
    {
        $post_type = defined('GROWTYPE_QUIZ_POST_TYPE') ? GROWTYPE_QUIZ_POST_TYPE : 'quiz';

        register_post_type($post_type,
            array (
                'labels' => array (
                    'name' => __('Quizes'),
                    'singular_name' => __('Quiz')
                ),
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_icon' => 'dashicons-welcome-learn-more'
            )
        );
    }

    /**
     * Registers the post types.
     */
    function register_taxonomy()
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
    function create_tables()
    {
        global $wpdb;

        $quiz_results_table_name = $this->quiz_results_table_name;

        if ($wpdb->get_var("SHOW TABLES LIKE '$quiz_results_table_name'") != $quiz_results_table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $quiz_results_table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      user_id bigint(20) UNSIGNED NOT NULL,
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
        if (get_post_type() === 'quiz') {
            $default_file = 'single-quiz.blade.php';
            $template = plugin_dir_path(dirname(__FILE__)) . 'resources/views/' . $default_file;
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

            $default_file = 'page-results.blade.php';

            $results_page_template = get_child_template_resource_path() . '/views/growtype-quiz/' . $default_file;

            if (!file_exists($results_page_template)) {
                $results_page_template = plugin_dir_path(dirname(__FILE__)) . 'resources/views/' . $default_file;
            }

            if (empty($results_page) && str_contains($_SERVER['REQUEST_URI'], self::CUSTOM_SLUG)) {
                return $results_page_template;
            } elseif (!empty($results_page) && $results_page->ID === get_the_ID()) {
                return $results_page_template;
            }
        }

        return $template;
    }

    /**
     * @return void
     */
    function custom_url()
    {
        add_rewrite_endpoint(self::CUSTOM_SLUG, EP_ROOT);
    }

    /**
     * Handle quiz data
     */
    function quiz_data()
    {
        $quiz_data['answers'] = $_POST['answers'] ?? null;
        $quiz_data['status'] = $_POST['status'] ?? null;
        $quiz_data['quiz_id'] = $_POST['quiz_id'] ?? null;
        $quiz_data['duration'] = $_POST['duration'] ?? null;

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
            $update_quiz_data = self::save_quiz_results_data($quiz_data);

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
                $results_data = $this->get_quiz_result_data($id);
                $correct_answers_amount = $results_data['correct_answers_amount'];
                $wrong_answers_amount = $results_data['wrong_answers_amount'];

                if ($answer === 'true') {
                    $correct_answers_amount = $correct_answers_amount + 1;
                } else {
                    $wrong_answers_amount = $wrong_answers_amount + 1;
                }

                $this->update_quiz_result_data($id, [
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
     * @param $quiz_data
     * @return bool
     */
    private function save_quiz_results_data($quiz_data)
    {
        global $wpdb;

        $table_name = $this->quiz_results_table_name;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $quiz_id = $quiz_data['quiz_id'];
        $answers = json_encode($quiz_data['answers']);

        $result = $wpdb->get_results("SELECT * FROM $table_name where quiz_id='$quiz_id' and user_id='$user_id' and answers='$answers'");

        if (empty($result)) {
            $wpdb->insert($table_name, [
                'user_id' => $user_id,
                'quiz_id' => $quiz_id,
                'answers' => $answers,
                'duration' => $quiz_data['duration'],
                'questions_amount' => $this->get_quiz_data($quiz_data['quiz_id'])['questions_available_amount'],
                'correct_answers_amount' => $this->evaluate_quiz_answers($quiz_data['quiz_id'], $quiz_data['answers'])['correct_answers_amount'],
                'wrong_answers_amount' => $this->evaluate_quiz_answers($quiz_data['quiz_id'], $quiz_data['answers'])['wrong_answers_amount'],
            ]);
        }

        return true;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    public function get_quiz_result_data($id)
    {
        global $wpdb;

        $table_name = $this->quiz_results_table_name;
        $result = $wpdb->get_results("SELECT * FROM $table_name where id=$id", ARRAY_A);

        return $result[0] ?? null;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    public function get_quiz_result_data_by_user_id($user_id)
    {
        global $wpdb;

        $table_name = $this->quiz_results_table_name;
        $result = $wpdb->get_results("SELECT * FROM $table_name where user_id=$user_id", ARRAY_A);

        return $result[0] ?? null;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    private function update_quiz_result_data($id, $fields)
    {
        global $wpdb;

        $table_name = $this->quiz_results_table_name;

        $wpdb->update($table_name, $fields, ['id' => $id]);

        return true;
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
        $quiz_data['limited_time'] = get_field('limited_time', $quiz_id);
        $quiz_data['duration'] = get_field('duration', $quiz_id);
        $quiz_data['progress_bar'] = get_field('progress_bar', $quiz_id);
        $quiz_data['questions'] = get_field('questions', $quiz_id);

        $quiz_data['questions_available'] = array_filter($quiz_data['questions'], function ($question) {
            $disabled = $question['disabled'] ?? false;
            return $question['question_type'] !== 'info' && $question['question_type'] !== 'success' && !$disabled;
        });

        $quiz_data['questions_available_amount'] = count($quiz_data['questions_available']);

        return $quiz_data;
    }

    /**
     * @param $quiz_id
     * @param $answers
     */
    public function evaluate_quiz_answers($quiz_id, $user_answers)
    {
        $quiz_data = get_quiz_data($quiz_id);
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
     * @return array|bool|object|null
     */
    public function get_quiz_results_data($quiz_id, $limit = 30, $based_on = 'performance')
    {
        global $wpdb;

        $table_name = $this->quiz_results_table_name;

        if ($based_on === 'any') {
            $result = $wpdb->get_results("SELECT * FROM $table_name where quiz_id=$quiz_id limit 0, $limit", ARRAY_A);
        } elseif ($based_on === 'performance') {
            for ($wrong_answers = 1; $wrong_answers < 5; $wrong_answers++) {
                $result = $wpdb->get_results("SELECT *
FROM $table_name
where quiz_id=$quiz_id and evaluated=false and
(user_id, wrong_answers_amount) in
      (SELECT user_id, MIN(wrong_answers_amount) as wrong_answers_amount FROM wp_quiz_results where quiz_id=$quiz_id and wrong_answers_amount<$wrong_answers group by user_id)
order by wrong_answers_amount asc, duration ASC limit 0,$limit", ARRAY_A);
                if (count($result) > 3) {
                    break;
                }
            }
        }

        return $result ?? null;
    }
}
