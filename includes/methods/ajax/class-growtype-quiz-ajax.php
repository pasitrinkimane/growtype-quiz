<?php

/**
 *
 */
class Growtype_Quiz_Ajax
{

    function __construct()
    {
        /**
         * Ajax save quiz details
         */
        add_action('wp_ajax_growtype_quiz_save_data', array ($this, 'growtype_quiz_save_data_handler'));
        add_action('wp_ajax_nopriv_growtype_quiz_save_data', array ($this, 'growtype_quiz_save_data_handler'));

        /**
         * Ajax save quiz update extra_details
         */
        add_action('wp_ajax_growtype_quiz_update_extra_details', array ($this, 'growtype_quiz_update_extra_details_handler'));
        add_action('wp_ajax_nopriv_growtype_quiz_update_extra_details', array ($this, 'growtype_quiz_update_extra_details_handler'));

        /**
         * Ajax evaluate quiz open question
         */
        add_action('wp_ajax_growtype_quiz_evaluate_open_question', array ($this, 'growtype_quiz_evaluate_open_question_handler'));
        add_action('wp_ajax_nopriv_growtype_quiz_evaluate_open_question', array ($this, 'growtype_quiz_evaluate_open_question_handler'));
    }

    /**
     * Handle quiz data
     */
    function growtype_quiz_save_data_handler()
    {
        $submitted_quiz_data['quiz_id'] = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
        $submitted_quiz_data['quiz_slug'] = isset($_POST['quiz_slug']) ? $_POST['quiz_slug'] : '';
        $quiz_id_is_required = apply_filters('growtype_quiz_id_is_required', true);

        if ($quiz_id_is_required && empty($submitted_quiz_data['quiz_id'])) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing quiz id', 'growtype-quiz'),
            ], 400);
        }

        /**
         * Retrieve quiz answers
         */
        $submitted_quiz_data['answers'] = isset($_POST['answers']) && !empty($_POST['answers']) ? json_decode(stripslashes($_POST['answers']), true) : null;

        $save_empty_answers = $quiz_id_is_required && growtype_quiz_save_empty_answers($submitted_quiz_data['quiz_id']);

        if (!$save_empty_answers && empty($submitted_quiz_data['answers'])) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing answers', 'growtype-quiz'),
            ], 400);
        }

        /**
         * Check other details
         */
        $submitted_quiz_data['unique_hash'] = isset($_POST['unique_hash']) ? $_POST['unique_hash'] : null;
        $submitted_quiz_data[Growtype_Quiz::TOKEN_KEY] = isset($_POST[Growtype_Quiz::TOKEN_KEY]) ? $_POST[Growtype_Quiz::TOKEN_KEY] : null;
        $submitted_quiz_data['duration'] = isset($_POST['duration']) ? $_POST['duration'] : null;
        $submitted_quiz_data['files'] = isset($_FILES) ? $_FILES : null;
        $submitted_quiz_data['extra_details'] = isset($_POST['extra_details']) ? json_decode(stripslashes($_POST['extra_details']), true) : [];

        $server_details = [
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
            'http_x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
            'http_referer' => $_SERVER['HTTP_REFERER'] ?? '',
        ];

        $submitted_quiz_data['extra_details'] = array_merge($submitted_quiz_data['extra_details'], $server_details);
        $submitted_quiz_data['ip_address'] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] ?? '') : '';

        $quiz_data = $quiz_id_is_required ? growtype_quiz_get_quiz_data($submitted_quiz_data['quiz_id']) : [];
        $update_quiz_data_if_token_exists = $quiz_data['update_quiz_data_if_token_exists'] ?? false;
        $existing_quiz_data = null;

        if ($update_quiz_data_if_token_exists) {
            $token = $submitted_quiz_data[Growtype_Quiz::TOKEN_KEY];

            $existing_quiz_data = Growtype_Quiz_Result_Crud::get_quiz_single_result_data_by_unique_hash($token);
        }

        $result_crud = new Growtype_Quiz_Result_Crud();

        /**
         * Update existing quiz data
         */
        if (!empty($existing_quiz_data)) {
            $insert_values = $result_crud->get_insert_values_from_quiz_data($submitted_quiz_data);

            $data_to_update = [
                'user_id' => !empty($insert_values['user_id']) ? $insert_values['user_id'] : $existing_quiz_data['user_id'],
                'quiz_slug' => $insert_values['quiz_slug'],
                'answers' => json_encode(array_merge(json_decode($existing_quiz_data['answers'], true), json_decode($insert_values['answers'], true))),
                'duration' => (int)$insert_values['duration'] + (int)$existing_quiz_data['duration'],
                'questions_amount' => (int)$insert_values['questions_amount'] + (int)$existing_quiz_data['questions_amount'],
                'questions_answered' => (int)$insert_values['questions_answered'] + (int)$existing_quiz_data['questions_answered'],
                'correct_answers_amount' => (int)$insert_values['correct_answers_amount'] + (int)$existing_quiz_data['correct_answers_amount'],
                'wrong_answers_amount' => (int)$insert_values['wrong_answers_amount'] + (int)$existing_quiz_data['wrong_answers_amount'],
                'extra_details' => json_encode(array_merge(json_decode($existing_quiz_data['extra_details'], true), json_decode($insert_values['extra_details'], true))),
                'ip_address' => !empty($insert_values['ip_address']) ? $insert_values['ip_address'] : $existing_quiz_data['ip_address'],
                'updated_at' => current_time('mysql'),
            ];

            Growtype_Quiz_Result_Crud::update_quiz_single_result($existing_quiz_data['id'], $data_to_update);

            do_action('growtype_quiz_after_update_data', $existing_quiz_data, $submitted_quiz_data);

            $success_url = class_exists('ACF') ? get_field('success_url', $submitted_quiz_data['quiz_id']) : '';
            $success_url = apply_filters('growtype_quiz_success_url', $success_url, $submitted_quiz_data['quiz_id'] ?? '', $submitted_quiz_data, $existing_quiz_data);

            return wp_send_json([
                'success' => true,
                'updated' => true,
                'redirect_url' => $success_url,
                'results_url' => growtype_quiz_results_page_url($existing_quiz_data['unique_hash']),
                'unique_hash' => $existing_quiz_data['unique_hash'],
            ]);
        }

        /**
         * Insert new quiz data
         */
        $updated_quiz_data = $result_crud->save_quiz_results_data($submitted_quiz_data);

        if (!empty($updated_quiz_data)) {
            do_action('growtype_quiz_after_save_data', $updated_quiz_data, $submitted_quiz_data);

            $success_url = class_exists('ACF') ? get_field('success_url', $submitted_quiz_data['quiz_id']) : '';
            $success_url = apply_filters('growtype_quiz_success_url', $success_url, $submitted_quiz_data['quiz_id'] ?? '', $submitted_quiz_data, $existing_quiz_data);

            return wp_send_json([
                'success' => true,
                'redirect_url' => $success_url,
                'results_url' => growtype_quiz_results_page_url($updated_quiz_data['unique_hash']),
                'unique_hash' => $updated_quiz_data['unique_hash'],
            ]);
        }

        return wp_send_json([
            'success' => false,
            'message' => __('Missing data', 'growtype-quiz'),
        ], 400);
    }

    /**
     * Save quiz extra details
     */
    function growtype_quiz_update_extra_details_handler()
    {
        if (!isset($_POST['postdata'])) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing data', 'growtype-quiz'),
            ]);
        }

        $post_params = [];
        parse_str($_POST['postdata'], $post_params);

        $unique_hash = $post_params['growtype_quiz_unique_hash'] ?? '';

        if (empty($unique_hash)) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing hash', 'growtype-quiz'),
            ]);
        }

        $quiz_result = Growtype_Quiz_Result_Crud::get_quiz_single_result_data_by_unique_hash($unique_hash);

        if (empty($quiz_result)) {
            return wp_send_json([
                'success' => false,
            ]);
        }

        if (!empty($quiz_result['extra_details'])) {
            $post_params = array_merge(json_decode($quiz_result['extra_details'], true), $post_params);
        }

        Growtype_Quiz_Result_Crud::update_quiz_single_result($quiz_result['id'], [
            'extra_details' => json_encode($post_params)
        ]);

        return wp_send_json([
            'success' => true,
        ]);
    }

    /**
     * @return null
     */
    function growtype_quiz_evaluate_open_question_handler()
    {
        $answers = isset($_POST['answers']) ? $_POST['answers'] : null;
        $quiz_id = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;

        if (empty($quiz_id) || empty($answers)) {
            return wp_send_json([
                'success' => false
            ], 400);
        }

        foreach ($answers as $id => $answer) {
            $results_data = Growtype_Quiz_Result_Crud::get_quiz_single_result_data($id);
            $correct_answers_amount = $results_data['correct_answers_amount'];
            $wrong_answers_amount = $results_data['wrong_answers_amount'];

            if ($answer === 'true') {
                $correct_answers_amount = $correct_answers_amount + 1;
            } else {
                $wrong_answers_amount = $wrong_answers_amount + 1;
            }

            Growtype_Quiz_Result_Crud::update_quiz_single_result($id, [
                'correct_answers_amount' => $correct_answers_amount,
                'wrong_answers_amount' => $wrong_answers_amount,
                'evaluated' => true,
            ]);
        }

        return wp_send_json([
            'success' => true
        ], 200);
    }
}
