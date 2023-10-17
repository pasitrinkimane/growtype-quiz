<?php if ($question['question_type'] !== 'success') { ?>

    <?php if (!empty($question['options_all'])) { ?>
        <div class="growtype-quiz-question-answers-wrapper">
            <div class="growtype-quiz-question-answers">
                <?php foreach ($question['options_all'] as $option) { ?>
                    <div class="growtype-quiz-question-answer-wrapper">
                        <div class="growtype-quiz-question-answer <?php echo isset($option['default']) && $option['default'] ? 'is-active' : '' ?>"
                             data-value="<?php echo isset($option['value']) && !empty($option['value']) ? $option['value'] : growtype_quiz_format_option_value($option['label']) ?>"
                             data-extra-value="<?php echo isset($option['extra_value']) ? $option['extra_value'] : '' ?>"
                             data-cor="<?php echo isset($quiz_data['show_correct_answer']) && $quiz_data['show_correct_answer'] ? $option['correct'] : '' ?>"
                             data-cor-trigger="<?php echo $quiz_data['correct_answer_trigger'] ?>"
                             data-default-belongs-to="<?php echo isset($option['default_belongs_to']) ? $option['default_belongs_to'] : '' ?>"
                             data-url="<?php echo isset($question['has_url']) && $question['has_url'] && !empty($option['url']) ? $option['url'] : '' ?>"
                             data-funnel="<?php echo isset($option['next_funnel']) ? $option['next_funnel'] : '' ?>"
                             data-img-url="<?php echo isset($option['featured_image']['sizes']['large']) ? $option['featured_image']['sizes']['large'] : '' ?>"
                             data-option-featured-img-main="<?php echo isset($question['option_featured_image_as_main']) && $question['option_featured_image_as_main'] ? 'true' : 'false' ?>"
                        >
                            <?php if (isset($question['option_featured_image_as_main']) && !$question['option_featured_image_as_main'] && !empty($option['featured_image'])) { ?>
                                <?php
                                $f_img = $question['options_has_featured_images'] && isset($option['featured_image']['url']) ? $option['featured_image']['url'] : '';
                                if (!empty($f_img)) {
                                    $ext = pathinfo($f_img, PATHINFO_EXTENSION);
                                    if ($ext === 'svg') { ?>
                                        <div class="e-img">
                                            <?php echo growtype_quiz_render_svg($f_img); ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="e-img" style="background:url(<?php echo $f_img ?>);background-position: center;background-size: cover;background-repeat: no-repeat;"></div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <div class="e-radio-wrapper">
                                <div class="e-radio"></div>
                            </div>
                            <label><?php echo $option['label'] ?></label>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($question['hint'])) { ?>
        <div class="growtype-quiz-hint" style="display: none;">
            <?php echo $question['hint'] ?>
        </div>
    <?php } ?>
<?php } ?>
