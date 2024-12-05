<?php if ($question['question_type'] !== 'success') { ?>

    <?php if (!empty($question['options_all'])) { ?>
        <div class="growtype-quiz-question-answers-wrapper">
            <div class="growtype-quiz-question-answers">
                <?php foreach ($question['options_all'] as $option) {
                    $classes = ['growtype-quiz-question-answer'];

                    if (isset($quiz_data['has_default_values']) && $quiz_data['has_default_values'] && isset($option['default']) && $option['default']) {
                        $classes[] = 'is-active';
                    }

                    if (isset($option['class']) && !empty($option['class'])) {
                        $classes[] = $option['class'];
                    }

                    $classes = implode(' ', $classes);
                    ?>
                    <div class="growtype-quiz-question-answer-wrapper">
                        <div class="<?php echo $classes ?>"
                             data-value="<?php echo $option['value'] ?>"
                             data-extra-value="<?php echo isset($option['extra_value']) ? $option['extra_value'] : '' ?>"
                             data-cor="<?php echo isset($quiz_data['show_correct_answer']) && $quiz_data['show_correct_answer'] ? $option['correct'] : '' ?>"
                             data-default-belongs-to="<?php echo isset($option['default_belongs_to']) ? $option['default_belongs_to'] : '' ?>"
                             data-url="<?php echo isset($question['has_url']) && $question['has_url'] && !empty($option['url']) ? $option['url'] : '' ?>"
                             data-funnel="<?php echo isset($option['next_funnel']) ? $option['next_funnel'] : '' ?>"
                             data-img-url="<?php echo isset($option['featured_image']['sizes']['large']) ? $option['featured_image']['sizes']['large'] : '' ?>"
                             data-option-featured-img-main="<?php echo isset($question['option_featured_image_as_main']) && $question['option_featured_image_as_main'] ? 'true' : 'false' ?>"
                             data-additional-question="<?php echo isset($option['additional_question']) && $option['additional_question'] ? 'true' : 'false' ?>"
                             data-additional-question-content="<?php echo isset($option['additional_question_content']) && !empty($option['additional_question_content']) ? htmlentities(apply_filters('the_content', $option['additional_question_content']), ENT_QUOTES) : '' ?>"
                        >
                            <div class="growtype-quiz-question-answer-content">
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
                                <div class="e-label">
                                    <span><?php echo $option['label'] ?? $option['value'] ?? '' ?></span>
                                    <?php if (isset($option['sub_label']) && !empty($option['sub_label'])) { ?>
                                        <p class="e-sublabel"><?php echo $option['sub_label'] ?></p>
                                    <?php } ?>
                                </div>
                                <div class="e-icon-check">
                                    <span class="dashicons dashicons-saved"></span>
                                </div>
                            </div>
                            <?php if (isset($option['answer_input']) && !empty($option['answer_input'])) { ?>
                                <div class="input-wrapper input-other">
                                    <textarea
                                        class="input"
                                        type="<?php echo isset($option['answer_input']['type']) ? $option['answer_input']['type'] : 'text' ?>"
                                        name="<?php echo isset($option['answer_input']['name']) && !empty($option['answer_input']['name']) ? $option['answer_input']['name'] : $option['value'] ?>"
                                        placeholder="<?php echo isset($option['answer_input']['placeholder']) ? $option['answer_input']['placeholder'] : '' ?>"
                                        required
                                        rows="2"
                                        cols="10"
                                    ></textarea>
                                </div>
                            <?php } ?>
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
