<?php if ($question['question_type'] !== 'success') { ?>
    <div class="b-quiz-question-answers-wrapper">
        <div class="b-quiz-question-answers">
            <?php if (!empty($question['options_all'])) { ?>
                <?php foreach ($question['options_all'] as $option) { ?>
                    <div class="b-quiz-question-answer <?php echo $option['default'] ? 'is-active' : '' ?>"
                         data-value="<?php echo $option['value'] ?>"
                         data-extra-value="<?php echo $option['extra_value'] ?>"
                         data-cor="<?php echo $quiz_data['is_test_mode'] ? $option['correct'] : '' ?>"
                         data-default-belongs-to="<?php echo $option['default_belongs_to'] ?>"
                    >
                        <?php if (!empty($option['featured_image'])) { ?>
                            <div class="e-img" style="background:url(<?php echo $option['featured_image']['sizes']['medium'] ?>);background-position: center;background-size: cover;background-repeat: no-repeat;"></div>
                        <?php } ?>
                        <div class="e-radio-wrapper">
                            <div class="e-radio"></div>
                        </div>
                        <label><?php echo $option['label'] ?></label>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <?php if (!empty($question['hint'])) { ?>
        <div class="b-quiz-hint" style="display: none;">
            <?php echo $question['hint'] ?>
        </div>
    <?php } ?>
<?php } ?>
