<?php

/**
 * Quiz results url
 */
if (!function_exists('growtype_quiz_get_unique_hash')) {
    function growtype_quiz_get_unique_hash($user_id = null)
    {
        $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $http_x_forwarded_for = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $token = isset($_GET[Growtype_Quiz::TOKEN_KEY]) && !empty($_GET[Growtype_Quiz::TOKEN_KEY]) ? $_GET[Growtype_Quiz::TOKEN_KEY] : null;

        $transient_id = $http_x_forwarded_for . $remote_addr . $http_user_agent;
        $transient_id = str_replace(' ', '_', $transient_id);
        $transient_id = str_replace('/', '_', $transient_id);
        $transient_id = 'growtype_quiz_unique_hash_' . $transient_id;
        $transient_id = substr($transient_id, 0, 150);

        if (!empty($token)) {
            set_transient($transient_id, $token, HOUR_IN_SECONDS);
        } else {
            $user_id = !empty($user_id) ? $user_id : get_current_user_id();
            $token = get_user_meta($user_id, Growtype_Form_Crud::GROWTYPE_QUIZ_UNIQUE_HASH, true);

            if (empty($token)) {
                $token = get_transient($transient_id);
                $token = !empty($token) ? $token : '';
            }
        }

        return $token;
    }
}

/**
 * Quiz results url
 */
if (!function_exists('growtype_quiz_results_page_url')) {
    function growtype_quiz_results_page_url($unique_hash = '')
    {
        $results_page = get_option('growtype_quiz_results_page');
        return !empty($results_page) ? get_permalink($results_page) . '?' . Growtype_Quiz::TOKEN_KEY . '=' . $unique_hash : '';
    }
}

function growtype_quiz_map_quiz_answers_with_questions($answers, $questions, $quiz_type)
{
    $results_data = [];

    if (empty($questions)) {
        return $results_data;
    }

    foreach ($answers as $answer_key => $answers_values) {
        $index = 1;
        $question = null;
        foreach ($questions as $single_question) {
            $question_key = $single_question['key'] ?? '';
            $question_type = $single_question['question_type'] ?? '';

            if (empty($question_key)) {
                $question_key = 'question_' . $question_type . '_' . $index;
            }

            if ($question_key === $answer_key) {
                $question = $single_question;
                break;
            }

            $index++;
        }

        if (strpos($answer_key, 'additional_question') > -1) {
            $parent_key = str_replace('_additional_question', '', $answer_key);
            $results_data[$parent_key]['additional_question'] = $answers_values;
        }

        if (empty($question)) {
            continue;
        }

        $answer_options = isset($question['options_all']) ? $question['options_all'] : [];
        $answer_options = !empty($answer_options) ? $answer_options : [];

        $user_answer_values = [];
        foreach ($answers_values as $answer) {
            $user_answer = array_filter($answer_options, function ($option) use ($answer) {
                return ($option['value'] ?? '') === $answer || $answer === growtype_quiz_format_option_value($option['label']);
            });

            $formatted_answer = !empty($user_answer) ? array_values($user_answer)[0] : [];

            if (empty($formatted_answer)) {
                $formatted_answer = $answer;
            }

            if (!empty($formatted_answer)) {
                array_push($user_answer_values, $formatted_answer);
            }
        }

        $results_data[$answer_key] = [
            'answers' => $user_answer_values,
            'question_title' => $question['question_title'] ?? '',
            'question_intro' => !empty($answer_options) ? $question['intro'] : $answer_key
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
 * @param $quiz_result_data
 * @return array|null
 */
function growtype_quiz_map_quiz_results_with_acf_quiz_data($quiz_results_data)
{
    $quiz_results = [];

    foreach ($quiz_results_data as $quiz_result_data) {
        $answers = isset($quiz_result_data['answers']) ? $quiz_result_data['answers'] : null;

        if (empty($answers)) {
            return null;
        }

        $answers = json_decode($answers, true);

        $quiz_id = $quiz_result_data['quiz_id'];

        $questions = growtype_quiz_get_questions($quiz_id);

        $quiz_type = get_field('quiz_type', $quiz_id);

        $results_data = growtype_quiz_map_quiz_answers_with_questions($answers, $questions, $quiz_type);

        $quiz_results[$quiz_result_data['id']] = $results_data;
    }

    return $quiz_results;
}

/**
 * @param $quiz_id
 * @param $answers
 */
function growtype_quiz_get_extended_user_quizes_results($user_id = null, $quiz_id = null)
{
    $user_id = !empty($user_id) ? $user_id : get_current_user_id();
    $quiz_result_data = growtype_quiz_get_user_results($user_id);

    if (!empty($quiz_id)) {
        $quiz_result_data = array_filter($quiz_result_data, function ($value) use ($quiz_id) {
            return $value['quiz_id'] === $quiz_id;
        });
    }

    if (empty($quiz_result_data)) {
        return null;
    }

    return growtype_quiz_map_quiz_results_with_acf_quiz_data($quiz_result_data);
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
    function growtype_quiz_get_user_results($user_id = null)
    {
        $user_id = !empty($user_id) ? $user_id : get_current_user_id();
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
        if (empty($hash)) {
            return null;
        }

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
