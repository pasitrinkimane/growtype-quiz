<?php

use function App\sage;

/**
 * @param $path
 * @param null $data
 * @return mixed
 * Include view
 */
function include_quiz_view($path, $data = null)
{
    $plugin_root = plugin_dir_path(__DIR__);
    $full_path = $plugin_root . 'resources/views/' . str_replace('.', '/', $path) . '.blade.php';

    if (empty($data)) {
        return sage('blade')->render($full_path);
    }

    return sage('blade')->render($full_path, $data);
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
function growtype_quiz_get_quiz_data($quiz_id)
{
    $growtype_quiz_loader = new Growtype_Quiz_Loader();

    return $growtype_quiz_loader->get_quiz_data($quiz_id);
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
function get_quiz_results_data()
{
    $growtype_quiz_loader = new Growtype_Quiz_Loader();

    return $growtype_quiz_loader->get_quiz_results_data();
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
function get_quiz_result_data_by_user_id($user_id)
{
    $growtype_quiz_loader = new Growtype_Quiz_Loader();

    return $growtype_quiz_loader->get_quiz_result_data_by_user_id($user_id);
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
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
