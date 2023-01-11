<?php

/**
 *
 */
class Growtype_Quiz_Ajax
{
    function __construct()
    {
        $this->result_crud = new Growtype_Quiz_Result_Crud();

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
    }

    /**
     * Handle quiz data
     */
    function growtype_quiz_save_data_handler()
    {
        $quiz_data['quiz_id'] = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;

        if (empty($quiz_data['quiz_id'])) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing quiz id', 'growtype-quiz'),
            ], 400);
        }

        /**
         * Retrieve quiz answers
         */
        $quiz_data['answers'] = isset($_POST['answers']) && !empty($_POST['answers']) ? json_decode(stripslashes($_POST['answers']), true) : null;

        if (!growtype_quiz_save_empty_answers($quiz_data['quiz_id']) && empty($quiz_data['answers'])) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing answers', 'growtype-quiz'),
            ], 400);
        }

        /**
         * Check other details
         */
        $quiz_data['unique_hash'] = $_POST['unique_hash'] ?? null;
        $quiz_data['duration'] = $_POST['duration'] ?? null;
        $quiz_data['files'] = $_FILES ?? null;
        $quiz_data['extra_details'] = $_POST['extra_details'] ?? [];

        $server_details = [
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
            'http_x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
            'http_referer' => $_SERVER['HTTP_REFERER'] ?? '',
        ];

        $quiz_data['extra_details'] = array_merge($quiz_data['extra_details'], $server_details);

        $quiz_data['ip_address'] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] ?? '') : '';

        /**
         * Update if exists unique_hash
         */
        if (!empty($quiz_data['unique_hash'])) {
            $existing_record = Growtype_Quiz_Result_Crud::get_quiz_single_result_data_by_unique_hash($quiz_data['unique_hash']);

            if (!empty($existing_record)) {

                $insert_values = $this->result_crud->get_insert_values_from_quiz_data($quiz_data);

                $this->result_crud->update_quiz_single_result($existing_record['id'], [
                    'user_id' => !empty($insert_values['user_id']) ? $insert_values['user_id'] : $existing_record['user_id'],
                    'answers' => $insert_values['answers'],
                    'duration' => $insert_values['duration'],
                    'questions_amount' => $insert_values['questions_amount'],
                    'questions_answered' => $insert_values['questions_answered'],
                    'correct_answers_amount' => $insert_values['correct_answers_amount'],
                    'wrong_answers_amount' => $insert_values['wrong_answers_amount'],
                    'extra_details' => $insert_values['extra_details'],
                    'ip_address' => $insert_values['ip_address'],
                    'updated_at' => current_time('mysql'),
                ]);

                return wp_send_json([
                    'success' => true,
                    'updated' => true,
                    'redirect_url' => get_field('success_url', $existing_record['quiz_id']),
                    'unique_hash' => $existing_record['unique_hash'],
                ]);
            }
        }

        /**
         * Insert new record
         */
        $updated_quiz_data = $this->result_crud->save_quiz_results_data($quiz_data);

        if (!empty($updated_quiz_data)) {
            return wp_send_json([
                'success' => true,
                'redirect_url' => get_field('success_url', $quiz_data['quiz_id']),
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
            return false;
        }

        if (!empty($quiz_result['extra_details'])) {
            $post_params = array_merge(json_decode($quiz_result['extra_details'], true), $post_params);
        }

        $this->result_crud->update_quiz_single_result($quiz_result['id'], [
            'extra_details' => json_encode($post_params)
        ]);

        return wp_send_json([
            'success' => true,
        ]);
    }
}
