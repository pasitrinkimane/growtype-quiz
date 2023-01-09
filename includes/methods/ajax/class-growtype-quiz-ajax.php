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
         * Ajax save quiz extra_details
         */
        add_action('wp_ajax_growtype_quiz_save_extra_details', array ($this, 'growtype_quiz_save_extra_details_handler'));
        add_action('wp_ajax_nopriv_growtype_quiz_save_extra_details', array ($this, 'growtype_quiz_save_extra_details_handler'));
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

        $update_quiz_data = $this->result_crud->save_quiz_results_data($quiz_data);

        if (!empty($update_quiz_data)) {
            return wp_send_json([
                'success' => true,
                'redirect_url' => class_exists('ACF') && get_field('success_url', $quiz_data['quiz_id']) ?? null,
                'unique_hash' => $update_quiz_data['unique_hash'],
            ]);
        }

        return wp_send_json([
            'success' => false,
            'message' => 'Missing user',
        ]);
    }

    /**
     * Save post ajax
     */
    function growtype_quiz_save_extra_details_handler()
    {
        if (!isset($_POST['postdata'])) {
            return wp_send_json([
                'success' => false,
                'message' => 'Missing date',
            ]);
        }

        $post_params = [];
        parse_str($_POST['postdata'], $post_params);

        $unique_hash = $post_params['growtype_quiz_unique_hash'] ?? '';

        if (empty($unique_hash)) {
            return wp_send_json([
                'success' => false,
                'message' => 'Missing hash',
            ]);
        }

        $quiz_result = $this->result_crud->get_quiz_single_result_data_by_unique_hash($unique_hash);

        if (empty($quiz_result)) {
            return false;
        }

        $this->result_crud->update_quiz_single_result($quiz_result['id'], [
            'extra_details' => json_encode($post_params)
        ]);

        return wp_send_json([
            'success' => true,
        ]);
    }
}
