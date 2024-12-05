<?php
if (isset($quiz_data['iframe_hide_header_footer']) && $quiz_data['iframe_hide_header_footer']) {
    echo '<style>header { display: none; } footer { display: none; } .growtype-quiz-wrapper { margin: 0; } .growtype-quiz-wrapper .s-quiz {padding: 0;} </style>';
}

$quiz_wrapper_classes = ['growtype-quiz-wrapper'];

if (isset($quiz_data['quiz_wrapper_class']) && !empty($quiz_data['quiz_wrapper_class'])) {
    $quiz_wrapper_classes[] = $quiz_data['quiz_wrapper_class'];
}

$quiz_wrapper_classes = implode(' ', $quiz_wrapper_classes);

?>

<div class="<?php echo $quiz_wrapper_classes ?>"
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

    <div class="s-quiz">
        <div class="container">
            <div class="growtype-quiz"
                 data-save-answers="<?php echo isset($quiz_data['save_answers']) && $quiz_data['save_answers'] ? true : false ?>"
                 data-save-on-load="<?php echo isset($quiz_data['save_data_on_load']) && $quiz_data['save_data_on_load'] ? true : false ?>"
                 data-correct-answers-trigger="<?php echo isset($quiz_data['correct_answer_trigger']) && $quiz_data['correct_answer_trigger'] ? true : false ?>"
                 data-show-question-nr-in-url="<?php echo isset($quiz_data['show_question_nr_in_url']) && $quiz_data['show_question_nr_in_url'] ? true : false ?>"
                 data-show-correct-answer="<?php echo isset($quiz_data['show_correct_answer']) && $quiz_data['show_correct_answer'] ? true : false ?>"
            >
                <div class="growtype-quiz-inner">
                    <?php do_action('growtype_quiz_inner_after_open') ?>

                    <?php if (isset($quiz_data['show_quiz_header']) && $quiz_data['show_quiz_header']) { ?>
                        <div class="growtype-quiz-header">
                            <?php do_action('growtype_quiz_header_after_open') ?>
                            <div class="growtype-quiz-nav show-initially" data-type="header">
                                <div class="growtype-quiz-nav-inner">
                                    <div class="growtype-quiz-btn-go-back show-initially" data-back-url="<?php echo growtype_quiz_get_back_url() ?>">
                                        <svg class="icon-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 7C0.447715 7 4.82823e-08 7.44772 0 8C-4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM15.7071 8.70711C16.0976 8.31658 16.0976 7.68342 15.7071 7.29289L9.34315 0.928933C8.95262 0.538409 8.31946 0.538409 7.92893 0.928933C7.53841 1.31946 7.53841 1.95262 7.92893 2.34315L13.5858 8L7.92893 13.6569C7.53841 14.0474 7.53841 14.6805 7.92893 15.0711C8.31946 15.4616 8.95262 15.4616 9.34315 15.0711L15.7071 8.70711ZM1 9L15 9L15 7L1 7L1 9Z" fill="black"/>
                                        </svg>
                                        <span class="e-text"><?php echo __('Back', 'growtype-quiz') ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($quiz_data['slide_counter']) && $quiz_data['slide_counter'] && ($quiz_data['slide_counter_position'] === 'top' || $quiz_data['slide_counter_position'] === 'both')) { ?>
                                <?php echo growtype_quiz_include_view('quiz.partials.components.question-nr', ['quiz_data' => $quiz_data]); ?>
                            <?php } ?>
                            <?php if (isset($quiz_data['progress_bar']) && $quiz_data['progress_bar']) { ?>
                                <div class="growtype-quiz-progressbar">
                                    <div class="growtype-quiz-progressbar-inner"></div>
                                </div>
                            <?php } ?>
                            <?php if (isset($quiz_data['limited_time']) && $quiz_data['limited_time']) { ?>
                                <div class="growtype-quiz-timer" data-duration="<?php echo $quiz_data['duration'] ?>">
                                    <span class="e-time-label-timeleft"><?php echo __('Liko:', 'growtype-quiz') ?></span>
                                    <div class="e-time-wrapper">
                                        <span class="e-time"></span>
                                    </div>
                                    <span class="e-time-label-duration"><?php echo __('min.', 'growtype-quiz') ?></span>
                                </div>
                            <?php } ?>
                            <?php do_action('growtype_quiz_header_before_close') ?>
                        </div>
                    <?php } ?>

                    <?php
                    if (isset($quiz_data['questions']) && !empty($quiz_data['questions'])) {
                        $index = 0;
                        foreach ($quiz_data['questions'] as $key => $question) {
                            $question_classes = ['growtype-quiz-question'];

                            if ($index === 0) {
                                $question_classes[] = 'first-question';
                            }

                            if (isset($question['is_visible']) && $question['is_visible']) {
                                $question_classes[] = 'is-visible';

                                if ($question['always_visible']) {
                                    $question_classes[] = 'is-always-visible';
                                }
                            }

                            if (isset($question['has_custom_class']) && $question['has_custom_class']) {
                                $question_classes[] = $question['custom_class'];
                            }

                            $question_classes = implode(' ', $question_classes);

                            $disabled = growtype_quiz_question_is_disabled($question);

                            if (!$disabled) { ?>
                                <div class="<?php echo $question_classes ?>"
                                     data-key="<?php echo $question['key'] ?>"
                                     data-question-nr="<?php echo $key + 1 ?>"
                                     data-question-type="<?php echo isset($question['question_type']) ? $question['question_type'] : 'general' ?>"
                                     data-question-style="<?php echo isset($question['question_style']) ? ($question['question_style'] !== 'custom' ? $question['question_style'] : $question['custom_style']) : '' ?>"
                                     data-answer-type="<?php echo isset($question['answer_type']) && !is_array($question['answer_type']) ? $question['answer_type'] : 'single' ?>"
                                     data-answers-limit="<?php echo isset($question['answers_limit']) && !empty($question['answers_limit']) ? $question['answers_limit'] : '' ?>"
                                     data-answer-style="<?php echo isset($question['answer_style']) && !empty($question['answer_style']) ? $question['answer_style'] : '' ?>"
                                     data-funnel="<?php echo isset($question['funnel']) ? $question['funnel'] : 'a' ?>"
                                     data-funnel-conditional="<?php echo isset($question['funnel_conditional']) ? $question['funnel_conditional'] : '' ?>"
                                     data-hint="<?php echo isset($question['has_a_hint']) ? $question['has_a_hint'] : 'false' ?>"
                                     data-hide-footer="<?php echo isset($question['hide_footer']) && $question['hide_footer'] ? 'true' : 'false' ?>"
                                     data-question-title="<?php echo isset($question['question_title']) ? $question['question_title'] : '' ?>"
                                     data-answer-required="<?php echo isset($question['not_required']) && $question['not_required'] ? 'false' : 'true' ?>"
                                     data-hide-back-button="<?php echo isset($question['hide_back_button']) && $question['hide_back_button'] ? 'true' : 'false' ?>"
                                     data-hide-next-button="<?php echo isset($question['hide_next_button']) && $question['hide_next_button'] ? 'true' : 'false' ?>"
                                     data-hide-progressbar="<?php echo isset($question['hide_progress_bar']) && $question['hide_progress_bar'] ? 'true' : 'false' ?>"
                                     data-disabled-if="<?php echo isset($question['disabled_if']) ? $question['disabled_if'] : '' ?>"
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
                                            <?php if (isset($question['question_type']) && $question['question_type'] === 'open') { ?>
                                                <?php echo growtype_quiz_include_view('quiz.partials.question-types.open', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                            <?php } elseif (isset($question['question_type']) && $question['question_type'] === 'info') { ?>
                                                <?php echo growtype_quiz_include_view('quiz.partials.question-types.info', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                            <?php } else { ?>
                                                <?php echo growtype_quiz_include_view('quiz.partials.question-types.radio', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php $index++; ?>

                        <?php }
                    }
                    ?>

                    <?php if (isset($quiz_data['show_quiz_footer']) && $quiz_data['show_quiz_footer']) { ?>
                        <?php
                        if ($quiz_data) {
                            echo growtype_quiz_include_view('quiz.partials.components.quiz-nav-footer', ['quiz_data' => $quiz_data]);
                        }
                        ?>
                    <?php } ?>

                    <?php do_action('growtype_quiz_inner_before_close') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($quiz_data['has_additional_open_question']) && $quiz_data['has_additional_open_question']) {
    $characters_limit = 100;
    ?>
    <div class="growtype-quiz-modal" data-type="additional_question">
        <div class="growtype-quiz-modal-inner">
            <div class="growtype-quiz-modal-content-wrapper">
                <div class="e-close">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="50px" height="50px">
                        <path d="M 9.15625 6.3125 L 6.3125 9.15625 L 22.15625 25 L 6.21875 40.96875 L 9.03125 43.78125 L 25 27.84375 L 40.9375 43.78125 L 43.78125 40.9375 L 27.84375 25 L 43.6875 9.15625 L 40.84375 6.3125 L 25 22.15625 Z"/>
                    </svg>
                </div>
                <div class="growtype-quiz-modal-content">
                    <div class="growtype-quiz-modal-content-form"></div>
                    <div class="growtype-quiz-modal-actions">
                        <button class="btn btn-primary btn-continue"><?php echo apply_filters('growtype_quiz_additional_open_question_cta_text', __('Continue', 'growtype-quiz')) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
