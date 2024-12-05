<?php

class Growtype_Quiz_Result_Crud
{
    public function __construct()
    {
        add_action('wp_login', array ($this, 'update_results_after_login'), 10, 2);
        add_action('user_register', array ($this, 'update_results_after_user_register'), 10, 2);

        /**
         * Clear local storage after login
         */
        add_action('wp_footer', function () {
            if (is_user_logged_in() && get_transient(self::user_logged_in_transient_name())) {
                ?>
                <script>
                    localStorage.removeItem('growtype_quiz_unique_hash');
                    localStorage.removeItem('growtype_quiz_answers');
                </script>
                <?php
                delete_transient(self::user_logged_in_transient_name());
            }
        });
    }

    public static function user_logged_in_transient_name()
    {
        return 'growtype_quiz_user_id_' . get_current_user_id() . '_logged_in';
    }

    function update_results_after_login($user_email, $user)
    {
        self::update_user_id($user->ID);

        set_transient(self::user_logged_in_transient_name(), true, HOUR_IN_SECONDS);
    }

    function update_results_after_user_register($user_id, $userdata)
    {
        self::update_user_id($user_id);
    }

    public static function update_user_id($user_id, $unique_hash = null)
    {
        $unique_hash = !empty($unique_hash) ? $unique_hash : growtype_quiz_get_unique_hash();

        if (!empty($unique_hash)) {
            $last_quiz_result = growtype_quiz_get_user_single_result_by_hash($unique_hash);
            if (!empty($last_quiz_result) && empty($last_quiz_result['user_id'])) {
                self::update_quiz_single_result($last_quiz_result['id'], [
                    'user_id' => $user_id
                ]);
            }
        }
    }

    public static function table_name()
    {
        global $wpdb;

        return $wpdb->prefix . 'growtype_quiz_results';
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

        if (is_user_logged_in()) {
            $quiz_data['user_id'] = get_current_user_id();
        }

        $quiz_id = isset($quiz_data['quiz_id']) ? $quiz_data['quiz_id'] : 0;

        /**
         * Check if empty answers should be saved
         */
        if (!empty($quiz_id) && !growtype_quiz_save_empty_answers($quiz_data['quiz_id']) && empty($quiz_data['answers'])) {
            return false;
        }

        $insert_values = $this->get_insert_values_from_quiz_data($quiz_data);

        if (empty($insert_values)) {
            return null;
        }

        $insert_data = [
            'user_id' => isset($insert_values['user_id']) && !empty($insert_values['user_id']) ? $insert_values['user_id'] : 0,
            'quiz_id' => !empty($insert_values['quiz_id']) ? $insert_values['quiz_id'] : 0,
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

        if (!$data_insert || is_wp_error($data_insert)) {
            return null;
        }

        /**
         * Set unique hash
         */
        set_transient('growtype_quiz_unique_hash', $insert_data['unique_hash'], WEEK_IN_SECONDS);

        return $insert_data;
    }

    public static function organize_files($files)
    {
        $organized_files = [];

        if (!empty($files)) {
            foreach ($files as $file_key => $file) {
                foreach ($file['name'] as $answer_key => $name) {
                    $organized_files[$file_key][] = [
                        'name' => $name,
                        'type' => $file['type'][$answer_key],
                        'tmp_name' => $file['tmp_name'][$answer_key],
                        'error' => $file['error'][$answer_key],
                        'size' => $file['size'][$answer_key],
                    ];
                }
            }
        }

        return $organized_files;
    }

    public function get_insert_values_from_quiz_data($quiz_data)
    {
        $quiz_id = isset($quiz_data['quiz_id']) ? $quiz_data['quiz_id'] : null;

        $images_organized = [];
        if (isset($quiz_data['files']) && !empty($quiz_data['files'])) {
            $images_organized = self::organize_files($quiz_data['files']);
        }

        /**
         * Get files upload location
         */
        $files_upload_location = apply_filters('growtype_quiz_files_upload_location', 'local', $quiz_data);

        /**
         * Upload images and attach to questions
         */
        if ($files_upload_location === 'local' && !empty($images_organized)) {
            foreach ($images_organized as $key => $images_group) {
                $question_key = explode('#_#', $key)[0] ?? '';
                foreach ($images_group as $file) {
                    $uploaded_file = $this->upload_file_to_media_library($file);
                    $attachment_id = $uploaded_file['attachment_id'] ?? null;
                    if (!empty($attachment_id) && !empty($question_key)) {
                        if (isset($quiz_data['answers'][$question_key]['files'])) {
                            array_push($quiz_data['answers'][$question_key]['files'], $attachment_id);
                        } else {
                            $quiz_data['answers'][$question_key]['files'] = [$attachment_id];
                        }
                    }
                }
            }
        }

        $unique_hash = isset($quiz_data['unique_hash']) && !empty($quiz_data['unique_hash']) ? $quiz_data['unique_hash'] : wp_generate_password(48, false);
        $questions_amount = !empty($quiz_id) && isset(growtype_quiz_get_quiz_data($quiz_id)['questions_available_amount']) ? growtype_quiz_get_quiz_data($quiz_id)['questions_available_amount'] : count($quiz_data['answers']);
        $evaluate_specific_quiz_answers = !empty($quiz_id) ? $this->evaluate_specific_quiz_answers($quiz_id, $quiz_data['answers']) : null;
        $correct_answers_amount = isset($evaluate_specific_quiz_answers['correct_answers_amount']) ? $evaluate_specific_quiz_answers['correct_answers_amount'] : null;
        $wrong_answers_amount = isset($evaluate_specific_quiz_answers['wrong_answers_amount']) ? $evaluate_specific_quiz_answers['wrong_answers_amount'] : null;
        $questions_answered = isset($evaluate_specific_quiz_answers['questions_answered']) ? $evaluate_specific_quiz_answers['questions_answered'] : $questions_amount;
        $extra_details = isset($quiz_data['extra_details']) && !empty($quiz_data['extra_details']) ? json_encode($quiz_data['extra_details']) : null;
        $ip_address = isset($quiz_data['ip_address']) ? $quiz_data['ip_address'] : null;
        $answers = is_array($quiz_data['answers']) ? json_encode($quiz_data['answers']) : $quiz_data['answers'];
        $user_id = isset($quiz_data['user_id']) ? $quiz_data['user_id'] : null;
        $duration = isset($quiz_data['duration']) ? $quiz_data['duration'] : null;

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
    public static function update_quiz_single_result($id, $fields)
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

        require_once(ABSPATH . 'wp-admin/includes/image.php');

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
        return self::evaluate_answers($answers, $quiz_data);
    }

    public static function evaluate_answers($answers, $quiz_data)
    {
        $evaluated_answers = [];
        $correct_answers_amount = 0;
        $correct_answers = [];
        $wrong_answers_amount = 0;
        $wrong_answers = [];
        $detailed_results = [];

        $questions = $quiz_data['questions'] ?? [];

        if ($quiz_data['quiz_type'] === Growtype_Quiz::TYPE_SCORED) {
            if (!is_array($answers)) {
                $answers = json_decode($answers, true);
            }

            foreach ($answers as $user_answer_key => $user_answer) {
                $question = null;
                foreach ($questions as $index => $single_question) {
                    $possible_keys = ['question', 'question_' . ((int)$index + 1)];
                    if (!empty($single_question['key'])) {
                        $possible_keys = [$single_question['key'], 'question', 'question_' . ((int)$index + 1)];
                    }

                    if (in_array($user_answer_key, $possible_keys)) {
                        $detailed_results[$user_answer_key] = [
                            'question_title' => trim(strip_tags($single_question['intro'])),
                            'answer_is_correct' => true,
                        ];

                        $question = $single_question;
                        break;
                    }
                }

                if (empty($question)) {
                    continue;
                }

                if (isset($question['options_all']) && !empty($question['options_all'])) {
                    $answer_is_wrong = false;

                    foreach ($question['options_all'] as $option) {
                        if ($option['correct']) {
                            $option_value = !empty($option['value']) ? $option['value'] : growtype_quiz_format_option_value($option['label']);
                            $detailed_results[$user_answer_key]['correct_answer'] = $option['label'];

                            foreach ($user_answer as $user_answer_single) {
                                if ($option_value !== $user_answer_single) {
                                    $detailed_results[$user_answer_key]['answer_is_correct'] = false;

                                    /**
                                     * Find user answer
                                     */
                                    foreach ($question['options_all'] as $option) {
                                        if ($option['value'] === $user_answer_single) {
                                            $detailed_results[$user_answer_key]['user_answer'] = $option['label'];
                                        }
                                    }

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
        } elseif ($quiz_data['quiz_type'] === Growtype_Quiz::TYPE_SCORED_MOST_COMMON_ANSWER) {
            $values_counted = array_count_values(array_column($answers, 'value'));
            $max_count = max($values_counted);
            $most_active_value = array_search($max_count, $values_counted);

            $evaluated_answers['most_active'] = $most_active_value;
        }

        $evaluated_answers['correct_answers_amount'] = $correct_answers_amount;
        $evaluated_answers['correct_answers'] = $correct_answers;
        $evaluated_answers['wrong_answers_amount'] = $wrong_answers_amount;
        $evaluated_answers['wrong_answers'] = $wrong_answers;
        $evaluated_answers['questions_answered'] = !empty($answers) ? count($answers) : 0;
        $evaluated_answers['detailed_results'] = $detailed_results;

        return $evaluated_answers;
    }

    public static function get_results_by_answers_params($params)
    {
        global $wpdb;

        $table = Growtype_Quiz_Result_Crud::table_name();

        $conditions = [];
        $values = [];

        foreach ($params as $key => $value) {
            $conditions[] = "answers LIKE %s";
            $values[] = '%"' . $key . '":["' . $value . '"]%';
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(" AND ", $conditions);

        $query = $wpdb->prepare($sql, ...$values);

        return $wpdb->get_results($query);
    }
}
