<?php

/**
 *
 */
class Growtype_Quiz_Results_Shortcode
{
    function __construct()
    {
        if (!wp_is_json_request() && !is_admin()) {
            add_shortcode('growtype_quiz_results', array ($this, 'growtype_quiz_results_shortcode'));
        }
    }

    /**
     * @param $attr
     * @return string
     * Posts shortcode
     */
    function growtype_quiz_results_shortcode($attr)
    {
        $quiz_results = growtype_quiz_get_extended_user_quizes_results($attr['user_id'] ?? null, $attr['quiz_id'] ?? null);

        if (empty($quiz_results)) {
            return '';
        }

        ob_start();

        echo '<div class="quiz-results" style="display: grid;grid-template-columns: 1fr 1fr;gap: 20px;">';

        foreach ($quiz_results as $questions) {
            foreach ($questions as $question) {
                $question_intro = $question['question_intro'] ?? '';
                $answers = $question['answers'] ?? [];

                echo '<div class="quiz-result" style="background: #fff; color: #000; padding: 20px; border-radius: 10px;">';

                if (!empty($question_intro)) {

                    if (strpos($question_intro, '_') !== false) {
                        $question_intro = ucwords(str_replace('_', ' ', $question_intro));
                    }

                    echo '<div class="question-intro" style="font-weight: bold; margin-bottom: 10px;">' . wp_kses_post($question_intro) . '</div>';
                }

                if (!empty($answers)) {
                    echo '<div class="question-answers">';
                    echo '<strong>Answers:</strong>';
                    echo '<ul style="padding-left: 20px; margin: 8px 0;">';
                    foreach ($answers as $answer) {
                        $label = $answer['label'] ?? $answer['value'] ?? '';
                        if (!empty($label)) {
                            echo '<li>' . esc_html($label) . '</li>';
                        }
                    }
                    echo '</ul>';
                    echo '</div>';
                }

                echo '</div>';
            }
        }

        echo '</div>';

        $results_content = ob_get_clean();

        $results_content = apply_filters('growtype_quiz_results_shortcode_content', $results_content, $quiz_results, $attr);

        return $results_content;
    }
}
