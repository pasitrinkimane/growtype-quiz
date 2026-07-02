<?php if ($question['question_type'] !== 'success') { ?>

    <?php if (!empty($question['options_all'])) { ?>
        <div class="growtype-quiz-question-answers-wrapper growtype-quiz-rating-wrapper">
            <div class="growtype-quiz-question-answers growtype-quiz-rating-answers">
                <?php foreach ($question['options_all'] as $option) {
                    $classes = ['growtype-quiz-question-answer', 'growtype-quiz-rating-answer'];

                    if (isset($quiz_data['has_default_values']) && $quiz_data['has_default_values'] && isset($option['default']) && $option['default']) {
                        $classes[] = 'is-active';
                    }

                    $classes = implode(' ', $classes);
                    ?>
                    <div class="growtype-quiz-question-answer-wrapper growtype-quiz-rating-answer-wrapper">
                        <div class="<?php echo $classes ?>"
                             data-value="<?php echo esc_attr($option['value']) ?>"
                             data-extra-value=""
                             data-cor=""
                             data-default-belongs-to=""
                             data-url=""
                             data-funnel=""
                             data-img-url=""
                             data-option-featured-img-main="false"
                             data-additional-question="false"
                             data-additional-question-content=""
                        >
                            <div class="growtype-quiz-question-answer-content">
                                <div class="e-rating-label"><?php echo esc_html($option['label'] ?? $option['value'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <?php
            $min_label = $question['options_labels']['min'] ?? '';
            $max_label = $question['options_labels']['max'] ?? '';
            if ($min_label || $max_label) { ?>
                <div class="growtype-quiz-rating-scale-labels">
                    <span class="e-label-min"><?php echo esc_html($min_label) ?></span>
                    <span class="e-label-max"><?php echo esc_html($max_label) ?></span>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

<?php } ?>
