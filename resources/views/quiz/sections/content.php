<?php
if ($quiz_data['iframe_hide_header_footer']) {
    echo '<style>header { display: none; } footer { display: none; } .growtype-quiz-wrapper { margin: 0; } .growtype-quiz-wrapper .s-quiz {padding: 0;} </style>';
}
?>

<div class="growtype-quiz-wrapper"
     data-quiz-id="<?php echo $quiz_data['quiz_id'] ?>"
     data-quiz-type="<?php echo $quiz_data['quiz_type'] ?>"
     data-cor-trigger="<?php echo $quiz_data['correct_answer_trigger'] ?>"
>
    <?php if (!empty($quiz_data['intro_content']) || !empty($quiz_data['intro_f_img'])) { ?>
        <section class="s-intro" style="background:url(<?php echo $quiz_data['intro_f_img']; ?>);background-size: cover;background-position: center;background-repeat: no-repeat;">
            <div class="container">
                <?php echo apply_filters('the_content', get_the_content()); ?>
            </div>
        </section>
    <?php } ?>

    <section class="s-quiz">
        <div class="container">
            <div class="growtype-quiz">
                <div class="growtype-quiz-inner">
                    <?php if ($quiz_data['progress_bar']) { ?>
                        <div class="growtype-quiz-progressbar mb-4">
                            <div class="growtype-quiz-progressbar-inner"></div>
                        </div>
                    <?php } ?>
                    <?php if ($quiz_data['slide_counter'] && ($quiz_data['slide_counter_position'] === 'top' || $quiz_data['slide_counter_position'] === 'both')) { ?>
                        <div class="growtype-quiz-header">
                            <?php echo growtype_quiz_include_view('quiz.partials.components.question-nr', ['quiz_data' => $quiz_data]); ?>
                        </div>
                    <?php } ?>

                    <?php if ($quiz_data['limited_time']) { ?>
                        <div class="growtype-quiz-timer" data-duration="<?php echo $quiz_data['duration'] ?>">
                            <span class="e-time-label-timeleft"><?php echo __('Liko:', 'growtype-quiz') ?></span>
                            <div class="e-time-wrapper">
                                <span class="e-time"></span>
                            </div>
                            <span class="e-time-label-duration"><?php echo __('min.', 'growtype-quiz') ?></span>
                        </div>
                    <?php } ?>

                    <?php $index = 0; ?>

                    <?php foreach ($quiz_data['questions'] as $key => $question) { ?>

                        <?php $disabled = growtype_quiz_question_is_disabled($question); ?>

                        <?php if (!$disabled) { ?>
                            <div class="growtype-quiz-question <?php echo $index === 0 ? 'first-question' : '' ?> <?php echo isset($question['is_visible']) && $question['is_visible'] && $question['always_visible'] ? 'is-always-visible' : '' ?> <?php echo isset($question['is_visible']) && $question['is_visible'] ? 'is-visible' : '' ?> <?php echo isset($question['has_custom_class']) && $question['has_custom_class'] ? $question['custom_class'] : '' ?>"
                                 data-key="<?php echo $question['key'] ?>"
                                 data-question-nr="<?php echo $key + 1 ?>"
                                 data-question-type="<?php echo $question['question_type'] ?>"
                                 data-question-style="<?php echo isset($question['question_style']) ? $question['question_style'] : '' ?>"
                                 data-answer-type="<?php echo !is_array($question['answer_type']) ? $question['answer_type'] : '' ?>"
                                 data-answers-limit="<?php echo isset($question['answers_limit']) && !empty($question['answers_limit']) ? $question['answers_limit'] : '' ?>"
                                 data-answer-style="<?php echo isset($question['answer_style']) ? $question['answer_style'] : '' ?>"
                                 data-funnel="<?php echo $question['funnel'] ?>"
                                 data-funnel-conditional="<?php echo isset($question['funnel_conditional']) ? $question['funnel_conditional'] : '' ?>"
                                 data-hint="<?php echo $question['has_a_hint'] ?>"
                                 data-hide-footer="<?php echo isset($question['hide_footer']) && $question['hide_footer'] ? 'true' : 'false' ?>"
                                 data-question-title="<?php echo isset($question['question_title']) ? $question['question_title'] : '' ?>"
                                 data-answer-required="<?php echo isset($question['not_required']) && $question['not_required'] ? 'false' : 'true' ?>"
                                 data-hide-back-button="<?php echo isset($question['hide_back_button']) && $question['hide_back_button'] ? 'true' : 'false' ?>"
                                 data-hide-next-button="<?php echo isset($question['hide_next_button']) && $question['hide_next_button'] ? 'true' : 'false' ?>"
                                 data-hide-progressbar="<?php echo isset($question['hide_progress_bar']) && $question['hide_progress_bar'] ? 'true' : 'false' ?>"
                                 data-disabled-if="<?php echo isset($question['disabled_if']) && $question['disabled_if'] ?>"
                            >
                                <div class="growtype-quiz-question-inner">
                                    <?php if (!empty($question['featured_image'])) { ?>
                                        <div class="b-img">
                                            <div class="e-img"
                                                 style="background:url(<?php echo $question['featured_image'] ?>);background-position: center;background-size: cover;background-repeat: no-repeat;"
                                                 data-img-url="<?php echo isset($question['featured_image']) ? $question['featured_image'] : '' ?>"
                                            ></div>
                                        </div>
                                    <?php } ?>
                                    <div class="growtype-quiz-main-content-wrapper">
                                        <?php
                                        $has_intro = isset($question['has_intro']) ? $question['has_intro'] : true;
                                        if ($has_intro && isset($question['intro'])) { ?>
                                            <div class="growtype-quiz-question-intro">
                                                <?php echo $question['intro'] ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($question['question_type'] === 'open') { ?>
                                            <?php echo growtype_quiz_include_view('quiz.partials.question-types.open', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } elseif ($question['question_type'] === 'info') { ?>
                                            <?php echo growtype_quiz_include_view('quiz.partials.question-types.info', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } else { ?>
                                            <?php echo growtype_quiz_include_view('quiz.partials.question-types.radio', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php $index++; ?>

                    <?php } ?>

                    <?php echo growtype_quiz_include_view('quiz.partials.components.quiz-nav', ['quiz_data' => $quiz_data]); ?>
                </div>
            </div>
        </div>
    </section>
</div>
