<?php

use function App\sage;

/**
 *
 */
if (!function_exists('growtype_quiz_render_svg')) {
    function growtype_quiz_render_svg($path)
    {
        $url = GROWTYPE_QUIZ_URL_PUBLIC . $path;

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
        $fallback_view = GROWTYPE_QUIZ_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.php';
        $fallback_blade_view = GROWTYPE_QUIZ_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.blade.php';
        $child_blade_view = get_stylesheet_directory() . '/views/' . GROWTYPE_QUIZ_TEXT_DOMAIN . '/' . str_replace('.', '/', $file_path) . '.blade.php';
        $child_view = get_stylesheet_directory() . '/views/' . GROWTYPE_QUIZ_TEXT_DOMAIN . '/' . str_replace('.', '/', $file_path) . '.php';

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
        $quiz_data['quiz_type'] = get_field('quiz_type', $quiz_id);
        $quiz_data['is_enabled'] = get_field('is_enabled', $quiz_id) ?? false;
        $quiz_data['save_answers'] = get_field('save_answers', $quiz_id);
        $quiz_data['show_correct_answer'] = get_field('show_correct_answer', $quiz_id);
        $quiz_data['correct_answer_trigger'] = get_field('correct_answer_trigger', $quiz_id);
        $quiz_data['slide_counter'] = get_field('slide_counter', $quiz_id);
        $quiz_data['slide_counter_position'] = get_field('slide_counter_position', $quiz_id);
        $quiz_data['limited_time'] = get_field('limited_time', $quiz_id);
        $quiz_data['duration'] = get_field('duration', $quiz_id);
        $quiz_data['progress_bar'] = get_field('progress_bar', $quiz_id);
        $quiz_data['use_question_title_nav'] = get_field('use_question_title_nav', $quiz_id);
        $quiz_data['randomize_slides_on_load'] = get_field('randomize_slides_on_load', $quiz_id);
        $quiz_data['questions'] = !empty(get_field('questions', $quiz_id)) ? get_field('questions', $quiz_id) : [];

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
if (!function_exists('growtype_quiz_get_user_results')) {
    function growtype_quiz_get_user_results($user_id)
    {
        $growtype_quiz_loader = new Growtype_Quiz_Result_Crud();

        return $growtype_quiz_loader->get_quiz_results_by_user_id($user_id);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_results_open_question_answers')) {
    function growtype_quiz_get_results_open_question_answers($quiz_id)
    {
        $quiz_results_data = growtype_quiz_get_results_data($quiz_id);
        $open_questions = [];

        $groups_amount = 0;
        $wrong_answers_amount = null;
        foreach ($quiz_results_data as $quiz_result_data) {

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
            $answers = json_decode($quiz_result_data['answers'], true);

            if (!isset($answers['question_open'])) {
                continue;
            }

            $open_questions[$id]['answer'] = $answers['question_open'] ?? null;
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

        if ($quiz_type === 'scored') {
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
    $quiz_result_data = growtype_quiz_get_user_results($user_id);

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
        return str_replace(' ', '_', strtolower($label));
    }
}
