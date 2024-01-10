<?php

use function App\sage;

/**
 *
 */
if (!function_exists('growtype_quiz_render_svg')) {
    function growtype_quiz_render_svg($url)
    {
        $arrContextOptions = [
            "ssl" => array (
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        ];

        $response = file_get_contents(
            $url,
            false,
            stream_context_create($arrContextOptions)
        );

        return $response;
    }
}

/**
 * @param $path
 * @param null $data
 * @return mixed
 * Include view
 */
if (!function_exists('growtype_quiz_include_resource')) {
    function growtype_quiz_include_resource($file_path, $variables = array (), $print = false)
    {
        $output = null;

        $plugin_root = plugin_dir_path(__DIR__);
        $full_file_path = $plugin_root . 'resources/' . $file_path;

        if (file_exists($full_file_path)) {
            // Extract the variables to a local namespace
            extract($variables);

            // Start output buffering
            ob_start();

            // Include the template file
            include $full_file_path;

            // End buffering and return its contents
            $output = ob_get_clean();
        }

        if ($print) {
            print $output;
        }

        return $output;
    }
}

/**
 * Include custom view
 */
if (!function_exists('growtype_quiz_include_view')) {
    function growtype_quiz_include_view($file_path, $variables = array (), $only_template_path = false)
    {
        $stylesheet_dir = strpos(get_stylesheet_directory(), 'resources') !== false ? get_stylesheet_directory() : get_stylesheet_directory() . '/resources';
        $fallback_view = GROWTYPE_QUIZ_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.php';
        $fallback_blade_view = GROWTYPE_QUIZ_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.blade.php';
        $child_blade_view = $stylesheet_dir . '/views/' . GROWTYPE_QUIZ_TEXT_DOMAIN . '/' . str_replace('.', '/', $file_path) . '.blade.php';
        $child_view = $stylesheet_dir . '/views/' . GROWTYPE_QUIZ_TEXT_DOMAIN . '/' . str_replace('.', '/', $file_path) . '.php';

        $template_path = $fallback_view;

        if (file_exists($child_blade_view) && function_exists('App\template')) {
            if (!$only_template_path) {
                return App\template($child_blade_view, $variables);
            } else {
                $template_path = $child_blade_view;
            }
        } elseif (file_exists($child_view)) {
            $template_path = $child_view;
        } elseif (file_exists($fallback_blade_view) && function_exists('App\template')) {
            if (!$only_template_path) {
                return App\template($fallback_blade_view, $variables);
            } else {
                $template_path = $fallback_blade_view;
            }
        }

        if ($only_template_path) {
            return $template_path;
        }

        if (file_exists($template_path)) {
            extract($variables);
            ob_start();
            include $template_path;
            $output = ob_get_clean();
        }

        return isset($output) ? $output : '';
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_quiz_data')) {
    function growtype_quiz_get_quiz_data($quiz_id)
    {
        /**
         * Check post type
         */
        if (get_post_type($quiz_id) !== 'quiz') {
            return null;
        }

        /**
         * Get post content
         */
        $content_post = get_post($quiz_id);

        $quiz_data = [
            'quiz_id' => $quiz_id,
            'intro_content' => apply_filters('the_content', $content_post->post_content),
            'intro_f_img' => !empty(get_post_thumbnail_id($quiz_id)) && isset(wp_get_attachment_image_src(get_post_thumbnail_id($quiz_id), 'single-post-thumbnail')[0]) ? wp_get_attachment_image_src(get_post_thumbnail_id($quiz_id), 'single-post-thumbnail')[0] : '',
            'iframe_hide_header_footer' => get_option('growtype_quiz_iframe_hide_header_footer') && isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe',
            'quiz_type' => Growtype_Quiz::TYPE_POLL,
            'is_enabled' => true,
            'save_answers' => true,
            'show_correct_answer' => false,
            'show_question_nr_in_url' => false,
            'correct_answer_trigger' => false,
            'slide_counter' => true,
            'slide_counter_position' => 'top',
            'limited_time' => false,
            'duration' => null,
            'progress_bar' => true,
            'use_question_title_nav' => true,
            'randomize_slides_on_load' => false,
            'slide_counter_style' => 'steps',
            'save_data_on_load' => false,
            'start_btn_label' => __('Start', 'growtype-quiz'),
            'finish_btn_label' => __('Finish', 'growtype-quiz'),
            'next_btn_label' => __('Next question', 'growtype-quiz'),
            'back_btn_label' => __('Back', 'growtype-quiz'),
            'questions' => [
                [
                    'has_intro' => true,
                    'intro' => 'This intro demo text',
                    'is_visible' => false,
                    'always_visible' => false,
                    'custom_class' => null,
                    'question_style' => 'general',
                    'question_type' => 'basic',
                    'answer_type' => 'single',
                    'answer_style' => 'radio',
                    'funnel' => 'a',
                    'has_a_hint' => false,
                    'hide_footer' => false,
                    'question_title' => 'This is test question title',
                    'not_required' => false,
                    'hide_back_button' => false,
                    'hide_next_button' => false,
                    'hide_progress_bar' => false,
                    'has_url' => false,
                    'options_all' => [
                        [
                            'value' => 1,
                            'label' => 'Option 1',
                            'default' => true,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ],
                        [
                            'value' => 2,
                            'label' => 'Option 2',
                            'default' => false,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ]
                    ],
                ],
                [
                    'has_intro' => true,
                    'intro' => 'This intro demo text',
                    'is_visible' => false,
                    'always_visible' => false,
                    'custom_class' => null,
                    'question_style' => 'general',
                    'question_type' => 'basic',
                    'answer_type' => 'single',
                    'answer_style' => 'radio',
                    'funnel' => 'a',
                    'has_a_hint' => false,
                    'hide_footer' => false,
                    'question_title' => 'This is test question title',
                    'not_required' => false,
                    'hide_back_button' => false,
                    'hide_next_button' => false,
                    'has_url' => false,
                    'hide_progress_bar' => false,
                    'options_all' => [
                        [
                            'value' => 1,
                            'label' => 'Option 1',
                            'default' => true,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ],
                        [
                            'value' => 2,
                            'label' => 'Option 2',
                            'default' => false,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ]
                    ],
                ]
            ],
        ];

        if (class_exists('ACF')) {
            $quiz_data['quiz_type'] = get_field('quiz_type', $quiz_id);
            $quiz_data['is_enabled'] = get_field('is_enabled', $quiz_id) ?? false;
            $quiz_data['save_answers'] = get_field('save_answers', $quiz_id);
            $quiz_data['show_correct_answer'] = get_field('show_correct_answer', $quiz_id) ? true : false;
            $quiz_data['correct_answer_trigger'] = get_field('correct_answer_trigger', $quiz_id);
            $quiz_data['slide_counter'] = get_field('slide_counter', $quiz_id);
            $quiz_data['slide_counter_position'] = get_field('slide_counter_position', $quiz_id);
            $quiz_data['limited_time'] = get_field('limited_time', $quiz_id);
            $quiz_data['duration'] = get_field('duration', $quiz_id);
            $quiz_data['progress_bar'] = get_field('progress_bar', $quiz_id);
            $quiz_data['use_question_title_nav'] = get_field('use_question_title_nav', $quiz_id);
            $quiz_data['randomize_slides_on_load'] = get_field('randomize_slides_on_load', $quiz_id);
            $quiz_data['slide_counter_style'] = get_field('slide_counter_style', $quiz_id);

            $quiz_data['questions'] = get_field('questions', $quiz_id, true);
            $quiz_data['questions'] = !empty($quiz_data['questions']) ? $quiz_data['questions'] : [];

            $quiz_data['hide_progress_bar'] = get_field('hide_progress_bar', $quiz_id);
            $quiz_data['hide_progress_bar'] = !empty($quiz_data['hide_progress_bar']) ? $quiz_data['hide_progress_bar'] : false;

            $quiz_data['show_question_nr_in_url'] = get_field('show_question_nr_in_url', $quiz_id) ? true : false;
            $quiz_data['show_question_nr_in_url'] = !empty($quiz_data['show_question_nr_in_url']) && $quiz_data['show_question_nr_in_url'] ? true : false;
        }

        /**
         * Update question keys
         */
        foreach ($quiz_data['questions'] as $key => $question) {
            $question_key = isset($question['has_custom_key']) && $question['has_custom_key'] ? $question['key'] : 'question_' . $question['question_type'] . '_' . ($key + 1);

            $quiz_data['questions'][$key]['key'] = $question_key;
        }

        $quiz_data = apply_filters('growtype_quiz_get_quiz_data', $quiz_data, $quiz_id);

        /**
         * Set first question answer type
         */
        $quiz_data['first_question_answer_type'] = isset($quiz_data['questions'][0]['answer_type']) ? $quiz_data['questions'][0]['answer_type'] : 'single';

        /**
         * Prevent duplicate keys
         */
        $existing_keys = [];
        foreach ($quiz_data['questions'] as $key => $question) {
            if (in_array($question['key'], $existing_keys)) {
                $quiz_data['questions'][$key]['key'] = $question['key'] . '_duplicate_' . $key;
            }
            array_push($existing_keys, $question['key']);
        }

        if (empty($quiz_data)) {
            throw new Exception('Quiz data is empty. Please setup quiz data in admin panel.');
        }

        /**
         * Set missing question keys
         */
        foreach ($quiz_data['questions'] as $key => $question) {
            if (!isset($question['question_type'])) {
                $quiz_data['questions'][$key]['question_type'] = Growtype_Quiz::TYPE_GENERAL;
            }
            if (!isset($question['question_style'])) {
                $quiz_data['questions'][$key]['question_style'] = Growtype_Quiz::STYLE_GENERAL;
            }
            if (!isset($question['answer_style'])) {
                $quiz_data['questions'][$key]['answer_style'] = 'radio';
            }
            if (!isset($question['funnel'])) {
                $quiz_data['questions'][$key]['funnel'] = 'a';
            }

            if (isset($question['options_all']) && !empty($question['options_all'])) {
                foreach ($question['options_all'] as $option_key => $option) {
                    if (!isset($option['value'])) {
                        $quiz_data['questions'][$key]['options_all'][$option_key]['next_funnel'] = 'a';
                    }
                }
            }
        }

        /**
         * Adjust questions
         */
        if ($quiz_data['randomize_slides_on_load'] && !empty($quiz_data['questions'])) {
            $questions_without_success = array_filter($quiz_data['questions'], function ($question) {
                return $question['question_type'] !== 'success';
            });

            $questions_success = array_filter($quiz_data['questions'], function ($question) {
                return $question['question_type'] === 'success';
            });

            shuffle($questions_without_success);

            $quiz_data['questions'] = array_merge($questions_without_success, $questions_success);
        }

        $has_success_question = array_filter($quiz_data['questions'], function ($question) {
            return isset($question['question_type']) && $question['question_type'] === 'success';
        });

        if (empty($has_success_question)) {
            $success_question = apply_filters('growtype_quiz_add_success_question', []);

            if (!empty($success_question)) {
                $quiz_data['questions'][] = $success_question;
            }
        }

        $quiz_data['questions_available'] = isset($quiz_data['questions']) && !empty($quiz_data['questions']) ? array_filter($quiz_data['questions'], function ($question) {
            $disabled = growtype_quiz_question_is_disabled($question);
            return $question['question_type'] !== 'info' && $question['question_type'] !== 'success' && !$disabled;
        }) : '';

        $quiz_data['questions_available_amount'] = isset($quiz_data['questions_available']) && !empty($quiz_data['questions_available']) ? count($quiz_data['questions_available']) : '';

        return $quiz_data;
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_results_data')) {
    function growtype_quiz_get_results_data($quiz_id, $limit = 30, $based_on = 'performance')
    {
        $growtype_quiz_loader = new Growtype_Quiz_Result_Crud();

        return $growtype_quiz_loader->get_single_quiz_results($quiz_id, $limit, $based_on);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_user_results_by_id')) {
    function growtype_quiz_get_user_results_by_id($user_id)
    {
        $growtype_quiz_loader = new Growtype_Quiz_Result_Crud();

        return $growtype_quiz_loader->get_quiz_results_by_user_id($user_id);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_user_single_result_by_hash')) {
    function growtype_quiz_get_user_single_result_by_hash($hash)
    {
        $growtype_quiz_loader = new Growtype_Quiz_Result_Crud();

        return $growtype_quiz_loader->get_quiz_single_result_data_by_unique_hash($hash);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_results_open_question_answers')) {
    function growtype_quiz_get_results_open_question_answers($quiz_id, $open_question_key = 'question_open')
    {
        $quiz_results_data = growtype_quiz_get_results_data($quiz_id, 50);
        $open_questions = [];

        $groups_amount = 0;
        $wrong_answers_amount = null;
        $already_evaluated_questions_amount = 0;
        foreach ($quiz_results_data as $quiz_result_data) {

            if ($already_evaluated_questions_amount >= 3) {
                break;
            }

            if ($quiz_result_data['evaluated']) {
                $already_evaluated_questions_amount++;
                continue;
            }

            /**
             * Check if open question exists
             */
            $open_question_exists = false;
            $answers = json_decode($quiz_result_data['answers'], true);
            foreach ($answers as $key => $answer) {
                if (strpos($key, $open_question_key) !== false) {
                    $open_question_exists = true;
                }
            }

            if (!$open_question_exists) {
                continue;
            }

            if ($wrong_answers_amount !== $quiz_result_data['wrong_answers_amount']) {
                $wrong_answers_amount = $quiz_result_data['wrong_answers_amount'];

                if (count($open_questions) >= 3) {
                    break;
                }

                $groups_amount++;
            }

            if ($groups_amount > 2) {
                continue;
            }

            $id = $quiz_result_data['id'];

            $open_questions[$id]['answer'] = $answers[$open_question_key] ?? null;
        }

        return $open_questions;
    }
}

/**
 * @return bool
 */
if (!function_exists('growtype_quiz_get_current_user_quiz_id')) {
    function growtype_quiz_is_active()
    {
        return get_post_status(growtype_quiz_get_current_user_quiz_id()) === 'publish';
    }
}

/**
 * @return int
 */
if (!function_exists('growtype_quiz_get_current_user_quiz_id')) {
    function growtype_quiz_get_current_user_quiz_id()
    {
        $signup_data = Growtype_Form_Signup::get_signup_data(get_current_user_id());

        $quiz = get_page_by_path('default');
        $quiz_id = !empty($quiz) ? $quiz->ID : '';

        if (!empty($signup_data) && isset($signup_data['group']['value'])) {
            $group = $signup_data['group']['value'];

            $quiz = get_page_by_path($group);
            $quiz_id = !empty($quiz) ? $quiz->ID : '';
        }

        return $quiz_id ?? null;
    }
}

/**
 * @return string
 * Quiz link
 */
if (!function_exists('growtype_quiz_get_quiz_link')) {
    function growtype_quiz_get_quiz_link()
    {
        return get_permalink(growtype_quiz_get_current_user_quiz_id());
    }
}

/**
 * @param $quiz_result_data
 * @return array|null
 */
function growtype_quiz_map_quiz_results($quiz_result_data)
{
    $answers = isset($quiz_result_data['answers']) ? $quiz_result_data['answers'] : null;

    if (empty($answers)) {
        return null;
    }

    $answers = json_decode($answers, true);

    $quiz_id = $quiz_result_data['quiz_id'];

    $questions = growtype_quiz_get_questions($quiz_id);

    $quiz_type = get_field('quiz_type', $quiz_id);

    $results_data = [];
    foreach ($answers as $answer_key => $answers_values) {

        $index = 1;
        $question = null;
        foreach ($questions as $single_question) {
            $question_key = $single_question['key'];

            if (empty($question_key)) {
                $question_key = 'question_' . $index;
            }

            if ($question_key === $answer_key) {
                $question = $single_question;
                break;
            }

            $index++;
        }

        if (empty($question)) {
            continue;
        }

        $answer_options = isset($question['options_all']) ? $question['options_all'] : [];

        if (empty($answer_options)) {
            continue;
        }

        $user_answer_values = [];
        foreach ($answers_values as $answer) {
            $user_answer = array_filter($answer_options, function ($option) use ($answer) {
                return $option['value'] === $answer;
            });

            if (!empty($user_answer)) {
                array_push($user_answer_values, array_values($user_answer)[0]);
            }
        }

        $results_data[$answer_key] = [
            'answers' => $user_answer_values,
            'question_title' => $question['question_title'],
            'question_intro' => $question['intro'],
        ];

        if ($quiz_type === Growtype_Quiz::TYPE_SCORED) {
            $correct_answers = [];
            foreach ($answers_values as $answer) {
                $correct_answer = array_filter($answer_options, function ($option) use ($answer) {
                    return $option['correct'] && $option['value'] === $answer;
                });

                if (!empty($correct_answer)) {
                    array_push($correct_answers, array_values($correct_answer)[0]);
                }
            }

            $results_data[$answer_key]['correct_answers'] = $correct_answers;
        }
    }

    return $results_data;
}

/**
 * @param $quiz_id
 * @param $answers
 */
function growtype_quiz_get_extended_user_quiz_results($user_id)
{
    $quiz_result_data = growtype_quiz_get_user_results_by_id($user_id);

    if (empty($quiz_result_data)) {
        return null;
    }

    return growtype_quiz_map_quiz_results($quiz_result_data);
}

/**
 *
 */
if (!function_exists('growtype_quiz_get_questions')) {
    function growtype_quiz_get_questions($quiz_id)
    {
        return get_field('questions', $quiz_id);
    }
}

/**
 *
 */
if (!function_exists('growtype_quiz_get_question_by_title')) {
    function growtype_quiz_get_question_by_title($question_title, $quiz_id)
    {
        $questions = growtype_quiz_get_questions($quiz_id);

        foreach ($questions as $question) {
            if ($question['question_title'] === $question_title) {
                return $question;
            }
        }

        return null;
    }
}

/**
 *
 */
if (!function_exists('growtype_quiz_format_option_value')) {
    function growtype_quiz_format_option_value($label)
    {
        return str_replace(array ('(', ')'), '', str_replace(array (' '), '_', strip_tags(strtolower($label))));
    }
}

/**
 *
 */
if (!function_exists('growtype_quiz_save_empty_answers')) {
    function growtype_quiz_save_empty_answers($quiz_id)
    {
        return class_exists('ACF') && get_field('save_empty_answers', $quiz_id) ? true : false;
    }
}

/**
 * Quiz results url
 */
if (!function_exists('growtype_quiz_results_page_url')) {
    function growtype_quiz_results_page_url($unique_hash = '')
    {
        $results_page = get_option('growtype_quiz_results_page');
        return !empty($results_page) ? get_permalink($results_page) . '?token=' . $unique_hash : '';
    }
}

/**
 * Quiz results url
 */
if (!function_exists('growtype_quiz_question_is_disabled')) {
    function growtype_quiz_question_is_disabled($question)
    {
        return isset($question['disabled']) && $question['disabled'] && empty($question['disabled_if']) ? true : false;;
    }
}

/**
 * Quiz is enabled
 */
if (!function_exists('growtype_quiz_is_enabled')) {
    function growtype_quiz_is_enabled()
    {
        return apply_filters('growtype_quiz_is_enabled', true);
    }
}
