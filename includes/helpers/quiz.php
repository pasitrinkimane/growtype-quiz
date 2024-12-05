<?php

/**
 *
 */
if (!function_exists('growtype_quiz_get_questions')) {
    function growtype_quiz_get_questions($quiz_id)
    {
        return get_field('questions', $quiz_id);
    }
}

/**
 *
 */
if (!function_exists('growtype_quiz_get_back_url')) {
    function growtype_quiz_get_back_url()
    {
        return apply_filters('growtype_quiz_back_url', home_url());
    }
}

/**
 *
 */
if (!function_exists('growtype_quiz_get_question_by_title')) {
    function growtype_quiz_get_question_by_title($question_title, $quiz_id)
    {
        $questions = growtype_quiz_get_questions($quiz_id);

        foreach ($questions as $question) {
            if ($question['question_title'] === $question_title) {
                return $question;
            }
        }

        return null;
    }
}

/**
 * Quiz results url
 */
if (!function_exists('growtype_quiz_question_is_disabled')) {
    function growtype_quiz_question_is_disabled($question)
    {
        return isset($question['disabled']) && $question['disabled'] && empty($question['disabled_if']) ? true : false;;
    }
}

/**
 * Quiz is enabled
 */
if (!function_exists('growtype_quiz_is_enabled')) {
    function growtype_quiz_is_enabled()
    {
        return apply_filters('growtype_quiz_is_enabled', true);
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
 *
 */
if (!function_exists('growtype_quiz_format_option_value')) {
    function growtype_quiz_format_option_value($label)
    {
        if (empty($label)) {
            return '';
        }

        return str_replace(array ('(', ')'), '', str_replace(array (' '), '_', strip_tags(strtolower($label))));
    }
}

/**
 *
 */
if (!function_exists('growtype_quiz_save_empty_answers')) {
    function growtype_quiz_save_empty_answers($quiz_id)
    {
        return class_exists('ACF') && get_field('save_empty_answers', $quiz_id) ? true : false;
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
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_quiz_data')) {
    function growtype_quiz_get_quiz_data($quiz_id)
    {
        /**
         * Check post type
         */
        if (get_post_type($quiz_id) !== Growtype_Quiz::get_growtype_quiz_post_type()) {
            return null;
        }

        /**
         * Get post content
         */
        return growtype_quiz_get_formatted_quiz_data($quiz_id);
    }
}

/**
 * @return array|bool|object|null
 * Get quiz data
 */
if (!function_exists('growtype_quiz_get_formatted_quiz_data')) {
    function growtype_quiz_get_formatted_quiz_data($quiz_id = null, $extra_data = [])
    {
        $intro_content = '';
        $intro_f_img = '';
        $iframe_hide_header_footer = get_option('growtype_quiz_iframe_hide_header_footer') && isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe';

        if (!empty($quiz_id)) {
            $intro_content = get_post($quiz_id);
            $intro_content = !empty($intro_content) ? apply_filters('the_content', $intro_content->post_content) : '';
            $intro_f_img = !empty(get_post_thumbnail_id($quiz_id)) && isset(wp_get_attachment_image_src(get_post_thumbnail_id($quiz_id), 'single-post-thumbnail')[0]) ? wp_get_attachment_image_src(get_post_thumbnail_id($quiz_id), 'single-post-thumbnail')[0] : '';
        }

        $quiz_data = [
            'quiz_id' => $quiz_id,
            'intro_content' => $intro_content,
            'intro_f_img' => $intro_f_img,
            'iframe_hide_header_footer' => $iframe_hide_header_footer,
            'quiz_type' => isset($extra_data['quiz_type']) && !empty($extra_data['quiz_type']) ? $extra_data['quiz_type'] : Growtype_Quiz::TYPE_POLL,
            'is_enabled' => true,
            'save_answers' => true,
            'show_correct_answer' => false,
            'show_question_nr_in_url' => false,
            'correct_answer_trigger' => false,
            'slide_counter' => true,
            'slide_counter_position' => 'top',
            'limited_time' => false,
            'duration' => null,
            'progress_bar' => true,
            'use_question_title_nav' => false,
            'randomize_slides_on_load' => false,
            'slide_counter_style' => 'steps',
            'save_data_on_load' => false,
            'start_btn_label' => apply_filters('growtype_quiz_start_btn_label', __('Start', 'growtype-quiz')),
            'finish_btn_label' => apply_filters('growtype_quiz_finish_btn_label', __('Finish', 'growtype-quiz')),
            'next_btn_label' => apply_filters('growtype_quiz_next_btn_label', __('Next question', 'growtype-quiz')),
            'back_btn_label' => apply_filters('growtype_quiz_back_btn_label', __('Back', 'growtype-quiz')),
            'has_additional_open_question' => false,
            'show_quiz_header' => true,
            'show_quiz_footer' => true,
            'update_quiz_data_if_token_exists' => apply_filters('growtype_quiz_update_quiz_data_if_token_exists', false, $quiz_id),
            'questions' => [
                [
                    'has_intro' => true,
                    'intro' => 'This intro demo text',
                    'is_visible' => false,
                    'always_visible' => false,
                    'custom_class' => null,
                    'question_style' => 'general',
                    'question_type' => 'basic',
                    'answer_type' => 'single',
                    'answer_style' => 'radio',
                    'funnel' => 'a',
                    'has_a_hint' => false,
                    'hide_footer' => false,
                    'question_title' => 'This is test question title',
                    'not_required' => false,
                    'hide_back_button' => false,
                    'hide_next_button' => false,
                    'hide_progress_bar' => false,
                    'has_url' => false,
                    'options_all' => [
                        [
                            'value' => 1,
                            'label' => 'Option 1',
                            'default' => true,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ],
                        [
                            'value' => 2,
                            'label' => 'Option 2',
                            'default' => false,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ]
                    ],
                ],
                [
                    'has_intro' => true,
                    'intro' => 'This intro demo text',
                    'is_visible' => false,
                    'always_visible' => false,
                    'custom_class' => null,
                    'question_style' => 'general',
                    'question_type' => 'basic',
                    'answer_type' => 'single',
                    'answer_style' => 'radio',
                    'funnel' => 'a',
                    'has_a_hint' => false,
                    'hide_footer' => false,
                    'question_title' => 'This is test question title',
                    'not_required' => false,
                    'hide_back_button' => false,
                    'hide_next_button' => false,
                    'has_url' => false,
                    'hide_progress_bar' => false,
                    'options_all' => [
                        [
                            'value' => 1,
                            'label' => 'Option 1',
                            'default' => true,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ],
                        [
                            'value' => 2,
                            'label' => 'Option 2',
                            'default' => false,
                            'extra_value' => null,
                            'default_belongs_to' => null,
                            'next_funnel' => 'a',
                        ]
                    ],
                ]
            ],
        ];

        if (!empty($extra_data)) {
            $quiz_data = array_merge($quiz_data, $extra_data);
        }

        if (class_exists('ACF') && !empty($quiz_id)) {
            $quiz_data['quiz_type'] = get_field('quiz_type', $quiz_id);
            $quiz_data['is_enabled'] = get_field('is_enabled', $quiz_id) ?? false;
            $quiz_data['save_answers'] = get_field('save_answers', $quiz_id);
            $quiz_data['show_correct_answer'] = get_field('show_correct_answer', $quiz_id) ? true : false;
            $quiz_data['correct_answer_trigger'] = get_field('correct_answer_trigger', $quiz_id);
            $quiz_data['slide_counter'] = get_field('slide_counter', $quiz_id);
            $quiz_data['slide_counter_position'] = get_field('slide_counter_position', $quiz_id);
            $quiz_data['limited_time'] = get_field('limited_time', $quiz_id);
            $quiz_data['duration'] = get_field('duration', $quiz_id);
            $quiz_data['progress_bar'] = get_field('progress_bar', $quiz_id);
            $quiz_data['use_question_title_nav'] = get_field('use_question_title_nav', $quiz_id);
            $quiz_data['randomize_slides_on_load'] = get_field('randomize_slides_on_load', $quiz_id);
            $quiz_data['slide_counter_style'] = get_field('slide_counter_style', $quiz_id);

            $quiz_data['questions'] = get_field('questions', $quiz_id, true);
            $quiz_data['questions'] = !empty($quiz_data['questions']) ? $quiz_data['questions'] : [];

            $quiz_data['hide_progress_bar'] = get_field('hide_progress_bar', $quiz_id);
            $quiz_data['hide_progress_bar'] = !empty($quiz_data['hide_progress_bar']) ? $quiz_data['hide_progress_bar'] : false;

            $quiz_data['show_question_nr_in_url'] = get_field('show_question_nr_in_url', $quiz_id) ? true : false;
            $quiz_data['show_question_nr_in_url'] = !empty($quiz_data['show_question_nr_in_url']) && $quiz_data['show_question_nr_in_url'] ? true : false;
            $quiz_data['has_additional_open_question'] = get_field('has_additional_open_question', $quiz_id) ? true : false;
            $quiz_data['has_default_values'] = get_field('has_default_values', $quiz_id) ? true : false;
        }

        /**
         * Update question keys
         */
        foreach ($quiz_data['questions'] as $key => $question) {
            $question_key = isset($question['has_custom_key']) && $question['has_custom_key'] ? $question['key'] : 'question_' . ($question['question_type'] ?? Growtype_Quiz::TYPE_GENERAL) . '_' . ($key + 1);

            $quiz_data['questions'][$key]['key'] = $question_key;
        }

        $quiz_data = apply_filters('growtype_quiz_get_quiz_data', $quiz_data, $quiz_id);

        /**
         * Set first question answer type
         */
        $quiz_data['first_question_answer_type'] = isset($quiz_data['questions'][0]['answer_type']) ? $quiz_data['questions'][0]['answer_type'] : 'single';

        /**
         * Prevent duplicate keys
         */
        $existing_keys = [];
        foreach ($quiz_data['questions'] as $key => $question) {
            if (in_array($question['key'], $existing_keys)) {
                $quiz_data['questions'][$key]['key'] = $question['key'] . '_duplicate_' . $key;
            }
            array_push($existing_keys, $question['key']);
        }

        if (empty($quiz_data)) {
            throw new Exception('Quiz data is empty. Please setup quiz data in admin panel.');
        }

        /**
         * Set missing question keys
         */
        foreach ($quiz_data['questions'] as $key => $question) {
            if (!isset($question['question_type'])) {
                $quiz_data['questions'][$key]['question_type'] = Growtype_Quiz::TYPE_GENERAL;
            }
            if (!isset($question['question_style'])) {
                $quiz_data['questions'][$key]['question_style'] = Growtype_Quiz::STYLE_GENERAL;
            }
            if (!isset($question['answer_style'])) {
                $quiz_data['questions'][$key]['answer_style'] = 'radio';
            }
            if (!isset($question['funnel'])) {
                $quiz_data['questions'][$key]['funnel'] = 'a';
            }

            if (isset($question['options_all']) && !empty($question['options_all'])) {
                foreach ($question['options_all'] as $option_key => $option) {
                    if (!isset($option['next_funnel'])) {
                        $quiz_data['questions'][$key]['options_all'][$option_key]['next_funnel'] = 'a';
                    }
                    if (!isset($option['value']) || empty($option['value'])) {
                        $quiz_data['questions'][$key]['options_all'][$option_key]['value'] = growtype_quiz_format_option_value($option['label']);
                    }
                }
            }
        }

        /**
         * Adjust questions
         */
        if ($quiz_data['randomize_slides_on_load'] && !empty($quiz_data['questions'])) {
            $questions_without_success = array_filter($quiz_data['questions'], function ($question) {
                return $question['question_type'] !== 'success';
            });

            $questions_success = array_filter($quiz_data['questions'], function ($question) {
                return $question['question_type'] === 'success';
            });

            shuffle($questions_without_success);

            $quiz_data['questions'] = array_merge($questions_without_success, $questions_success);
        }

        $has_success_question = array_filter($quiz_data['questions'], function ($question) {
            return isset($question['question_type']) && $question['question_type'] === 'success';
        });

        if (empty($has_success_question)) {
            $success_question = apply_filters('growtype_quiz_add_success_question', []);

            if (!empty($success_question)) {
                $quiz_data['questions'][] = $success_question;
            }
        }

        $quiz_data['questions_available'] = isset($quiz_data['questions']) && !empty($quiz_data['questions']) ? array_filter($quiz_data['questions'], function ($question) {
            $disabled = growtype_quiz_question_is_disabled($question);
            return $question['question_type'] !== 'info' && $question['question_type'] !== 'success' && !$disabled;
        }) : '';

        $quiz_data['questions_available_amount'] = isset($quiz_data['questions_available']) && !empty($quiz_data['questions_available']) ? count($quiz_data['questions_available']) : '';

        return $quiz_data;
    }
}
