<?php

use function App\sage;

/**
 * @param $path
 * @param null $data
 * @return mixed
 * Include view
 */
if (!function_exists('include_quiz_view')) {
    function include_quiz_view($path, $data = null)
    {
        $plugin_root = plugin_dir_path(__DIR__);
        $full_path = $plugin_root . 'resources/views/' . str_replace('.', '/', $path) . '.blade.php';

        if (empty($data)) {
            return sage('blade')->render($full_path);
        }

        return sage('blade')->render($full_path, $data);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_quiz_data')) {
    function growtype_quiz_get_quiz_data($quiz_id)
    {
        $growtype_quiz_loader = new Growtype_Quiz_Loader();

        return $growtype_quiz_loader->get_quiz_data($quiz_id);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('get_quiz_results_data')) {
    function get_quiz_results_data()
    {
        $growtype_quiz_loader = new Growtype_Quiz_Loader();

        return $growtype_quiz_loader->get_quiz_results_data();
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('get_quiz_result_data_by_user_id')) {
    function get_quiz_result_data_by_user_id($user_id)
    {
        $growtype_quiz_loader = new Growtype_Quiz_Loader();

        return $growtype_quiz_loader->get_quiz_result_data_by_user_id($user_id);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('get_quiz_results_open_question_answers')) {
    function get_quiz_results_open_question_answers($quiz_id)
    {
        $growtype_quiz_loader = new Growtype_Quiz_Loader();
        $quiz_results_data = $growtype_quiz_loader->get_quiz_results_data($quiz_id);
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
 * @param $quiz_id
 * @param $answers
 */
function growtype_quiz_get_quiz_answers_list($user_id)
{
    $quiz_result_data = get_quiz_result_data_by_user_id($user_id);

    if (empty($quiz_result_data)) {
        return null;
    }

    $answers = isset($quiz_result_data['answers']) ? $quiz_result_data['answers'] : null;

    if (empty($answers)) {
        return null;
    }

    $answers = json_decode($answers, true);

    $quiz_id = $quiz_result_data['quiz_id'];

    $questions = get_field('questions', $quiz_id);

    $answers_list = [];
    foreach ($answers as $key => $answer) {
        $question = array_where($questions, function ($question) use ($key) {
            return $question['key'] === $key;
        });

        $question = array_values($question)[0];

        $options = isset($question['options_all']) ? $question['options_all'] : [];

        if (empty($options)) {
            continue;
        }

        $correct_answer = array_where($options, function ($option) use ($answer) {
            return $option['correct'];
        });

        $user_answer = array_where($options, function ($option) use ($answer) {
            return $option['value'] === $answer[0];
        });

        $correct_answer_value = array_values($correct_answer)[0]['value'] ?? '-';
        $correct_answer_label = array_values($correct_answer)[0]['label'] ?? '-';

        $user_answer_value = array_values($user_answer)[0]['value'] ?? '-';
        $user_answer_label = array_values($user_answer)[0]['label'] ?? '-';

        $answers_list[$key] = [
            'correct_answer_value' => $correct_answer_value,
            'correct_answer_label' => $correct_answer_label,
            'user_answer_value' => $user_answer_value,
            'user_answer_label' => $user_answer_label,
            'user_answer_is_correct' => $correct_answer_value === $user_answer_value,
            'question_intro' => $question['intro']
        ];
    }

    return $answers_list;
}
