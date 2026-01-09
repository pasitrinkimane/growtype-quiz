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
        // SECURITY: Verify nonce to prevent CSRF attacks
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'growtype_quiz_ajax_nonce')) {
            error_log('Growtype Quiz - Save data nonce verification failed');
            return wp_send_json([
                'success' => false,
                'message' => __('Security verification failed. Please refresh the page and try again.', 'growtype-quiz'),
            ], 403);
        }
        
        $submitted_quiz_data['quiz_id'] = isset($_POST['quiz_id']) ? absint($_POST['quiz_id']) : null;
        $submitted_quiz_data['quiz_slug'] = isset($_POST['quiz_slug']) ? sanitize_text_field($_POST['quiz_slug']) : '';
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
        $submitted_quiz_data['unique_hash'] = isset($_POST['unique_hash']) ? sanitize_text_field($_POST['unique_hash']) : null;
        $submitted_quiz_data[Growtype_Quiz::TOKEN_KEY] = isset($_POST[Growtype_Quiz::TOKEN_KEY]) ? sanitize_text_field($_POST[Growtype_Quiz::TOKEN_KEY]) : null;
        $submitted_quiz_data['duration'] = isset($_POST['duration']) ? absint($_POST['duration']) : null;
        $submitted_quiz_data['files'] = isset($_FILES) ? $_FILES : null;
        $submitted_quiz_data['extra_details'] = isset($_POST['extra_details']) ? json_decode(stripslashes($_POST['extra_details']), true) : [];

        $server_details = [
            'remote_addr' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
            'http_x_forwarded_for' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']) : '',
            'http_referer' => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '',
        ];

        $submitted_quiz_data['extra_details'] = array_merge($submitted_quiz_data['extra_details'], $server_details);
        $submitted_quiz_data['ip_address'] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? sanitize_text_field(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] ?? '') : '';

        $quiz_data = isset($submitted_quiz_data['quiz_id']) && !empty($submitted_quiz_data['quiz_id']) ? growtype_quiz_get_quiz_data($submitted_quiz_data['quiz_id']) : [];

        $update_quiz_data_if_token_exists = $_POST['update_quiz_data'] ?? $quiz_data['update_quiz_data_if_token_exists'] ?? false;

        $existing_quiz_data = [];
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
        // SECURITY: Verify nonce to prevent CSRF attacks
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'growtype_quiz_ajax_nonce')) {
            error_log('Growtype Quiz - Update extra details nonce verification failed');
            return wp_send_json([
                'success' => false,
                'message' => __('Security verification failed. Please refresh the page and try again.', 'growtype-quiz'),
            ], 403);
        }
        
        $unique_hash = isset($_POST['unique_hash']) ? sanitize_text_field($_POST['unique_hash']) : '';

        if (empty($unique_hash)) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing hash', 'growtype-quiz'),
            ]);
        }

        $extra_details = isset($_POST['extra_details']) && !empty($_POST['extra_details']) ? stripslashes($_POST['extra_details']) : [];
        $extra_details = !empty($extra_details) ? json_decode($extra_details, true) : [];

        if (empty($extra_details)) {
            return wp_send_json([
                'success' => false,
                'message' => __('Missing data', 'growtype-quiz'),
            ]);
        }

        $post_params = $extra_details;

        $quiz_result = Growtype_Quiz_Result_Crud::get_quiz_single_result_data_by_unique_hash($unique_hash);

        if (empty($quiz_result)) {
            return wp_send_json([
                'success' => false,
            ]);
        }
        
        // SECURITY: Prevent IDOR - verify ownership if user is logged in
        if (is_user_logged_in()) {
            $current_user_id = get_current_user_id();
            $quiz_owner_id = isset($quiz_result['user_id']) ? absint($quiz_result['user_id']) : 0;
            
            if ($quiz_owner_id > 0 && $quiz_owner_id !== $current_user_id) {
                error_log(sprintf('Growtype Quiz - IDOR attempt: User %d tried to update quiz result for user %d', $current_user_id, $quiz_owner_id));
                return wp_send_json([
                    'success' => false,
                    'message' => __('Unauthorized action.', 'growtype-quiz'),
                ], 403);
            }
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
        // SECURITY: Verify nonce to prevent CSRF attacks
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'growtype_quiz_ajax_nonce')) {
            error_log('Growtype Quiz - Evaluate question nonce verification failed');
            return wp_send_json([
                'success' => false,
                'message' => __('Security verification failed. Please refresh the page and try again.', 'growtype-quiz'),
            ], 403);
        }
        
        // SECURITY: Require admin/editor capabilities to evaluate questions
        if (!current_user_can('edit_posts')) {
            error_log('Growtype Quiz - Unauthorized evaluation attempt by user ' . get_current_user_id());
            return wp_send_json([
                'success' => false,
                'message' => __('You do not have permission to evaluate quiz questions.', 'growtype-quiz'),
            ], 403);
        }
        
        $answers = isset($_POST['answers']) && is_array($_POST['answers']) ? $_POST['answers'] : null;
        $quiz_id = isset($_POST['quiz_id']) ? absint($_POST['quiz_id']) : null;

        if (empty($quiz_id) || empty($answers)) {
            return wp_send_json([
                'success' => false
            ], 400);
        }

        foreach ($answers as $id => $answer) {
            $id = absint($id);
            $answer = sanitize_text_field($answer);
            
            $results_data = Growtype_Quiz_Result_Crud::get_quiz_single_result_data($id);
            
            if (empty($results_data)) {
                continue;
            }
            
            $correct_answers_amount = absint($results_data['correct_answers_amount']);
            $wrong_answers_amount = absint($results_data['wrong_answers_amount']);

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
