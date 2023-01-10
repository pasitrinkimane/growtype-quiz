<?php

class Growtype_Quiz_Result_Crud
{
    public static function table_name()
    {
        global $wpdb;

        return $wpdb->prefix . 'quiz_results';
    }

    public static function delete_record($id)
    {
        global $wpdb;

        $table = self::table_name();

        return $wpdb->delete($table, array ('id' => $id));
    }

    /**
     * @return array|bool|object|null
     */
    public function get_quizes_results($args)
    {
        global $wpdb;

        extract(shortcode_atts(array (
            'order' => 'desc',
            'search' => '',
            'limit' => '20',
            'orderby' => 'created_at',
            'offset' => '0',
        ), $args));

        if ($limit === -1) {
            $limit = '18446744073709551610';
        }

        $table = self::table_name();

        if (!empty($args['search'])) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE id Like '%{$search}%' OR user_id Like '%{$search}%' OR quiz_id Like '%{$search}%' OR answers Like '%{$search}%' ORDER BY {$orderby} {$order} LIMIT {$limit} OFFSET {$offset}",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * from {$table} ORDER BY {$orderby} {$order} LIMIT {$limit} OFFSET {$offset}",
                ARRAY_A
            );
        }
    }

    /**
     * @return array|bool|object|null
     */
    public function get_total_results_amount()
    {
        return count($this->get_quizes_results([
            'limit' => -1
        ]));
    }

    /**
     * @return array|bool|object|null
     */
    public function get_single_quiz_results($quiz_id, $limit = 30, $based_on = 'performance')
    {
        global $wpdb;

        $table_name = self::table_name();

        $result = null;

        if ($based_on === 'any') {
            $result = $wpdb->get_results("SELECT * FROM $table_name where quiz_id=$quiz_id limit 0, $limit", ARRAY_A);
        } elseif ($based_on === 'performance') {
            $result = $wpdb->get_results("SELECT * FROM $table_name where quiz_id=$quiz_id order by correct_answers_amount desc, questions_amount asc, duration ASC limit 0, $limit", ARRAY_A);
        }

        return $result;
    }

    /**
     * @param $quiz_data
     * @return bool
     */
    public function save_quiz_results_data($quiz_data)
    {
        global $wpdb;

        $table_name = self::table_name();

        $user_id = null;
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
        }

        $quiz_id = $quiz_data['quiz_id'];
        $answers_decoded = json_decode(stripslashes($quiz_data['answers']), true);

        /**
         * Load post class
         */
        if (isset($quiz_data['files']) && !empty($quiz_data['files'])) {
            foreach ($quiz_data['files'] as $file_key => $file) {
                foreach ($answers_decoded as $answer_key => $answer) {
                    if (strpos($file_key, $answer_key) > -1) {
                        $uploaded_file = $this->upload_file_to_media_library($file);
                        $attachment_id = $uploaded_file['attachment_id'] ?? null;
                        if (!empty($attachment_id)) {
                            if (isset($answers_decoded[$answer_key]['files'])) {
                                array_push($answers_decoded[$answer_key]['files'], $attachment_id);
                            } else {
                                $answers_decoded[$answer_key]['files'] = [$attachment_id];
                            }
                        }

                    }
                }
            }
        }

        $answers = json_encode($answers_decoded);

        $questions_amount = growtype_quiz_get_quiz_data($quiz_data['quiz_id'])['questions_available_amount'] ?? null;
        $evaluate_quiz_results = $this->evaluate_quiz_results($quiz_data['quiz_id'], $answers);
        $correct_answers_amount = $evaluate_quiz_results['correct_answers_amount'] ?? null;
        $wrong_answers_amount = $evaluate_quiz_results['wrong_answers_amount'] ?? null;
        $questions_answered = $evaluate_quiz_results['questions_answered'] ?? null;

        $unique_hash = bin2hex(random_bytes(12) . time());

        $insert_data = [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'answers' => $answers,
            'duration' => $quiz_data['duration'] ?? null,
            'questions_amount' => $questions_amount,
            'questions_answered' => $questions_answered,
            'correct_answers_amount' => $correct_answers_amount,
            'wrong_answers_amount' => $wrong_answers_amount,
            'unique_hash' => $unique_hash,
        ];

        $insert_data = apply_filters('save_quiz_results_data', $insert_data, $quiz_data);

        $data_insert = $wpdb->insert($table_name, $insert_data);

        if (is_wp_error($data_insert)) {
            return false;
        }

        return $insert_data;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    public function get_quiz_single_result_data($id)
    {
        global $wpdb;

        $table_name = self::table_name();

        $result = $wpdb->get_results("SELECT * FROM $table_name where id=$id", ARRAY_A);

        return $result[0] ?? null;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    public function get_quiz_single_result_data_by_unique_hash($unique_hash)
    {
        global $wpdb;

        $table_name = self::table_name();

        $result = $wpdb->get_results("SELECT * FROM $table_name where unique_hash='{$unique_hash}'", ARRAY_A);

        return $result[0] ?? null;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    public function get_quiz_results_by_user_id($user_id)
    {
        global $wpdb;

        $table_name = self::table_name();

        $results = $wpdb->get_results("SELECT * FROM $table_name where user_id=$user_id", ARRAY_A);

        return $results;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     */
    public function update_quiz_single_result($id, $fields)
    {
        global $wpdb;

        $table_name = self::table_name();

        $wpdb->update($table_name, $fields, ['id' => $id]);

        return true;
    }

    /**
     * @param $file
     * @return array
     */
    public function upload_file_to_media_library($file)
    {
        $file_name = basename($file["name"]);
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_mime = mime_content_type($file['tmp_name']);

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_featured_image = wp_handle_upload($file, array ('test_form' => false));

        if (isset($upload_featured_image['error'])) {
            $response['success'] = false;
            $response['message'] = $upload_featured_image['error'];

            return $response;
        }

        $upload_featured_image_path = $upload_featured_image['file'];

        $upload_id = wp_insert_attachment(array (
            'guid' => $upload_featured_image_path,
            'post_mime_type' => $file_mime,
            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
            'post_content' => '',
            'post_status' => 'inherit'
        ), $upload_featured_image_path);

        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $upload_featured_image_path));

        $response['attachment_id'] = $upload_id;

        return $response;
    }

    /**
     * @param $quiz_id
     * @param $answers
     */
    public function evaluate_quiz_results($quiz_id, $answers)
    {
        $quiz_data = growtype_quiz_get_quiz_data($quiz_id);
        $questions = $quiz_data['questions'];

        $correct_answers = 0;
        $wrong_answers = 0;

        if (!is_array($answers)) {
            $answers = json_decode($answers, true);
        }

        foreach ($answers as $user_answer_key => $user_answer) {
            $question = null;
            foreach ($questions as $index => $single_question) {
                $question_key = !empty($single_question['key']) ? $single_question['key'] : 'question_' . ((int)$index + 1);

                if ($user_answer_key === $question_key) {
                    $question = $single_question;
                    break;
                }
            }

            if (empty($question)) {
                continue;
            }

            $answer_is_wrong = false;
            foreach ($question['options_all'] as $option) {
                if ($option['correct']) {
                    $option_value = !empty($option['value']) ? $option['value'] : growtype_quiz_format_option_value($option['label']);

                    foreach ($user_answer as $user_answer_single) {
                        if ($option_value !== $user_answer_single) {
                            $answer_is_wrong = true;
                            break;
                        }
                    }
                }
            }

            if ($answer_is_wrong) {
                $wrong_answers++;
            } else {
                $correct_answers++;
            }
        }

        return [
            'correct_answers_amount' => $correct_answers,
            'wrong_answers_amount' => $wrong_answers,
            'questions_answered' => !empty($answers) ? count($answers) : 0,
        ];
    }
}


