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

        $results_content = '';

        if (!empty($quiz_results)) {
            ob_start();

            ?>

            <style>
                .quiz-results {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1rem;
                }

                @media (max-width: 768px) {
                    .quiz-results {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
            <?php

            echo '<div class="quiz-results">';

            foreach ($quiz_results as $questions) {
                foreach ($questions as $question) {
                    $question_intro = isset($question['question_title']) && !empty($question['question_title']) ? $question['question_title'] : $question['question_intro'];
                    $answers = $question['answers'] ?? [];

                    echo '<div class="quiz-result" style="background:#fff;color:#000;padding:20px;border-radius:10px;">';

                    if (!empty($question_intro)) {
                        $text_intro = strip_tags($question_intro);
                        if (strpos($text_intro, '_') !== false) {
                            $text_intro = ucwords(str_replace('_', ' ', $text_intro));
                        }

                        if (preg_match('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/', $question_intro, $matches)) {
                            $text_intro = strip_tags($matches[0]);
                        }

                        $text_intro = '<h3>' . $text_intro . '</h3>';

                        echo '<div class="question-intro" style="font-weight:bold;margin-bottom:10px;">'
                            . wp_kses_post($text_intro)
                            . '</div>';
                    }

                    if (!empty($answers)) {
                        echo '<div class="question-answers">';
                        echo '<strong>Answers:</strong>';
                        echo '<ul style="padding-left: 20px; margin: 8px 0;">';

                        foreach ($answers as $answer_key => $answer) {
                            // Case 1: Answer is associative array with 'label' or 'value'
                            if (is_array($answer) && (isset($answer['label']) || isset($answer['value']))) {
                                $label = $answer['label'] ?? $answer['value'];
                                $label = strip_tags($label);

                                echo '<li>' . wp_kses(
                                        $label,
                                        array (
                                            'svg' => array (
                                                'xmlns' => true,
                                                'viewBox' => true,
                                                'width' => true,
                                                'height' => true,
                                                'fill' => true,
                                                'stroke' => true,
                                            ),
                                            'path' => array (
                                                'd' => true,
                                                'fill' => true,
                                                'stroke' => true,
                                            ),
                                            'g' => array (
                                                'fill' => true,
                                            ),
                                        )
                                    ) . '</li>';

                                // Case 2: Answer is a nested array (e.g. key/value traits)
                            } elseif (is_array($answer)) {
                                echo '<li><strong>' . esc_html($answer_key) . ':</strong>';
                                echo '<ul style="margin: 6px 0 6px 16px;">';
                                foreach ($answer as $sub_key => $sub_value) {
                                    // Handle nested arrays by converting to JSON or string representation
                                    if (is_array($sub_value)) {
                                        $sub_value = json_encode($sub_value);
                                    }
                                    echo '<li>' . wp_kses_post((string)$sub_value) . '</li>';
                                }
                                echo '</ul></li>';

                                // Case 3: Simple scalar (string, int, etc.)
                            } else {
                                echo '<li>' . esc_html($answer_key) . ': ' . wp_kses_post($answer) . '</li>';
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
        }

        $results_content = apply_filters('growtype_quiz_results_shortcode_content', $results_content, $quiz_results, $attr);

        return $results_content;
    }
}
