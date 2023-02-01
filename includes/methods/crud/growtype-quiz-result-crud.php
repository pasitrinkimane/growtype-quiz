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
    public static function get_quizes_results($args)
    {
        global $wpdb;

        extract(shortcode_atts(array (
            'order' => 'desc',
            'search' => '',
            'limit' => '20',
            'orderby' => 'created_at',
            'offset' => '0'
        ), $args));

        if ($limit === -1) {
            $limit = '18446744073709551610';
        }

        $table = self::table_name();

        if (!empty($args['search'])) {
            $query = "SELECT * from {$table} WHERE id Like '%{$search}%' OR user_id Like '%{$search}%' OR quiz_id Like '%{$search}%' OR answers Like '%{$search}%' ORDER BY {$orderby} {$order} LIMIT {$limit} OFFSET {$offset}";
        } else {
            $query = "SELECT * from {$table} ORDER BY {$orderby} {$order} LIMIT {$limit} OFFSET {$offset}";
        }

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * @return array|bool|object|null
     */
    public static function get_results_count()
    {
        return count(self::get_quizes_results([
            'limit' => -1
        ]));
    }

    /**
     * @return array|bool|object|null
     */
    public function get_single_quiz_results($quiz_id, $limit = 30, $based_on = 'any')
    {
        global $wpdb;

        if ($limit === -1) {
            $limit = '18446744073709551610';
        }

        $table_name = self::table_name();

        $result = null;

        if ($based_on === 'any') {
            $result = $wpdb->get_results("SELECT * FROM $table_name where quiz_id=$quiz_id and answers is not null order by id limit 0, $limit", ARRAY_A);
        } elseif ($based_on === 'performance') {
            $result = $wpdb->get_results("SELECT * FROM $table_name where quiz_id=$quiz_id and answers is not null order by correct_answers_amount desc, questions_amount asc, duration ASC limit 0, $limit", ARRAY_A);
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
            $quiz_data['user_id'] = $current_user->ID;
        }

        $quiz_id = isset($quiz_data['quiz_id']) ? $quiz_data['quiz_id'] : null;

        if (empty($quiz_id)) {
            return false;
        }

        /**
         * Check if empty answers should be saved
         */
        if (!growtype_quiz_save_empty_answers($quiz_data['quiz_id']) && empty($quiz_data['answers'])) {
            return false;
        }

        $insert_values = $this->get_insert_values_from_quiz_data($quiz_data);

        $insert_data = [
            'user_id' => $insert_values['user_id'],
            'quiz_id' => $insert_values['quiz_id'],
            'answers' => $insert_values['answers'],
            'duration' => $insert_values['duration'],
            'questions_amount' => $insert_values['questions_amount'],
            'questions_answered' => $insert_values['questions_answered'],
            'correct_answers_amount' => $insert_values['correct_answers_amount'],
            'wrong_answers_amount' => $insert_values['wrong_answers_amount'],
            'unique_hash' => $insert_values['unique_hash'],
            'extra_details' => $insert_values['extra_details'],
            'ip_address' => $insert_values['ip_address'],
        ];

        $insert_data = apply_filters('save_quiz_results_data', $insert_data, $quiz_data);

        $data_insert = $wpdb->insert($table_name, $insert_data);

        if (is_wp_error($data_insert)) {
            return false;
        }

        return $insert_data;
    }

    public function get_insert_values_from_quiz_data($quiz_data)
    {
        $quiz_id = $quiz_data['quiz_id'] ?? null;

        if (isset($quiz_data['files']) && !empty($quiz_data['files'])) {
            foreach ($quiz_data['files'] as $file_key => $file) {
                foreach ($quiz_data['answers'] as $answer_key => $answer) {
                    if (strpos($file_key, $answer_key) > -1) {
                        $uploaded_file = $this->upload_file_to_media_library($file);
                        $attachment_id = $uploaded_file['attachment_id'] ?? null;
                        if (!empty($attachment_id)) {
                            if (isset($quiz_data['answers'][$answer_key]['files'])) {
                                array_push($quiz_data['answers'][$answer_key]['files'], $attachment_id);
                            } else {
                                $quiz_data['answers'][$answer_key]['files'] = [$attachment_id];
                            }
                        }

                    }
                }
            }
        }

        $unique_hash = isset($quiz_data['unique_hash']) && !empty($quiz_data['unique_hash']) ? $quiz_data['unique_hash'] : bin2hex(random_bytes(12) . time());
        $questions_amount = growtype_quiz_get_quiz_data($quiz_id)['questions_available_amount'] ?? null;
        $evaluate_specific_quiz_answers = $this->evaluate_specific_quiz_answers($quiz_id, $quiz_data['answers']);
        $correct_answers_amount = $evaluate_specific_quiz_answers['correct_answers_amount'] ?? null;
        $wrong_answers_amount = $evaluate_specific_quiz_answers['wrong_answers_amount'] ?? null;
        $questions_answered = $evaluate_specific_quiz_answers['questions_answered'] ?? null;
        $extra_details = isset($quiz_data['extra_details']) && !empty($quiz_data['extra_details']) ? json_encode($quiz_data['extra_details']) : null;
        $ip_address = $quiz_data['ip_address'] ?? null;
        $answers = is_array($quiz_data['answers']) ? json_encode($quiz_data['answers']) : $quiz_data['answers'];
        $user_id = $quiz_data['user_id'] ?? null;
        $duration = $quiz_data['duration'] ?? null;

        return [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'answers' => $answers,
            'duration' => $duration,
            'questions_amount' => $questions_amount,
            'questions_answered' => $questions_answered,
            'correct_answers_amount' => $correct_answers_amount,
            'wrong_answers_amount' => $wrong_answers_amount,
            'unique_hash' => $unique_hash,
            'extra_details' => $extra_details,
            'ip_address' => $ip_address
        ];
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
    public static function get_quiz_single_result_data_by_unique_hash($unique_hash)
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
    public function evaluate_specific_quiz_answers($quiz_id, $answers)
    {
        $quiz_data = growtype_quiz_get_quiz_data($quiz_id);
        $questions = $quiz_data['questions'];

        $correct_answers_amount = 0;
        $correct_answers = [];
        $wrong_answers_amount = 0;
        $wrong_answers = [];

        if (!empty($answers)) {
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
                    array_push($wrong_answers, $user_answer[0] . '_#_' . $user_answer_key);
                    $wrong_answers_amount++;
                } else {
                    array_push($correct_answers, $user_answer[0] . '_#_' . $user_answer_key);
                    $correct_answers_amount++;
                }
            }
        }

        return [
            'correct_answers_amount' => $correct_answers_amount,
            'correct_answers' => $correct_answers,
            'wrong_answers_amount' => $wrong_answers_amount,
            'wrong_answers' => $wrong_answers,
            'questions_answered' => !empty($answers) ? count($answers) : 0,
        ];
    }
}


