<?php

class Growtype_Quiz_Admin_Result_Crud
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
            for ($wrong_answers = 1; $wrong_answers < 5; $wrong_answers++) {
                $result = $wpdb->get_results("SELECT *
FROM $table_name
where quiz_id=$quiz_id and evaluated=false and
(user_id, wrong_answers_amount) in
      (SELECT user_id, MIN(wrong_answers_amount) as wrong_answers_amount FROM wp_quiz_results where quiz_id=$quiz_id and wrong_answers_amount<$wrong_answers group by user_id)
order by wrong_answers_amount asc, duration ASC limit 0,$limit", ARRAY_A);
                if (count($result) > 3) {
                    break;
                }
            }
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
        $growtype_quiz_admin_post = new Growtype_Quiz_Admin_Post();

        if (isset($quiz_data['files']) && !empty($quiz_data['files'])) {
            foreach ($quiz_data['files'] as $file_key => $file) {
                foreach ($answers_decoded as $answer_key => $answer) {
                    if (strpos($file_key, $answer_key) > -1) {
                        $uploaded_file = $growtype_quiz_admin_post->upload_file_to_media_library($file);
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

        $questions_amount = $growtype_quiz_admin_post->get_quiz_data($quiz_data['quiz_id'])['questions_available_amount'] ?? null;
        $correct_answers_amount = $growtype_quiz_admin_post->evaluate_quiz_answers($quiz_data['quiz_id'], $quiz_data['answers'])['correct_answers_amount'] ?? null;
        $wrong_answers_amount = $growtype_quiz_admin_post->evaluate_quiz_answers($quiz_data['quiz_id'], $quiz_data['answers'])['wrong_answers_amount'] ?? null;

        $insert_data = [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'answers' => $answers,
            'duration' => $quiz_data['duration'] ?? null,
            'questions_amount' => $questions_amount,
            'correct_answers_amount' => $correct_answers_amount,
            'wrong_answers_amount' => $wrong_answers_amount,
        ];

        $insert_data = apply_filters('save_quiz_results_data', $insert_data, $quiz_data);

        $wpdb->insert($table_name, $insert_data);

        return true;
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
}


