<?php
$post = get_post();
$quiz_data = growtype_quiz_get_quiz_data($post->ID);

if (!current_user_can('manage_options')) {
    if (!is_null($quiz_data['is_enabled']) && !$quiz_data['is_enabled']) {
        wp_redirect(get_home_url());
    }
}
?>

<?php get_header(); ?>

<div class="quiz-wrapper" data-current-question-type="">
    <?php
    $intro_content = apply_filters('the_content', get_the_content());
    $intro_f_img = isset(wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail')[0]) ? wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail')[0] : '';
    ?>
    <?php if ($intro_content) { ?>
        <section class="s-intro" style="background:url(<?php echo $intro_f_img; ?>);background-size: cover;background-position: center;background-repeat: no-repeat;">
            <div class="container">
                <?php echo apply_filters('the_content', get_the_content()); ?>
            </div>
        </section>
    <?php } ?>

    <section class="s-quiz">
        <div class="container">

            <div class="b-quiz" data-type="<?php echo $quiz_data['quiz_type'] ?>">
                <div class="b-quiz-inner">
                    <?php if ($quiz_data['progress_bar']) { ?>
                        <div class="b-quiz-progressbar mb-4">
                            <div class="b-quiz-progressbar-inner"></div>
                        </div>
                    <?php } ?>
                    <?php if ($quiz_data['slide_counter']) { ?>
                        <div class="b-quiz-header">
                            <?php echo growtype_quiz_include_view('partials.components.question-nr'); ?>
                        </div>
                    <?php } ?>
                    <?php if ($quiz_data['limited_time']) { ?>
                        <div class="b-quiz-timer" data-duration="<?php echo $quiz_data['duration'] ?>">
                            <span><?php echo __('Liko:', 'growtype-quiz') ?></span>
                            <div class="e-time-wrapper">
                                <span class="e-time"></span>
                            </div>
                            <span><?php echo __('min.', 'growtype-quiz') ?></span>
                        </div>
                    <?php } ?>

                    <?php $index = 0; ?>

                    <?php foreach ($quiz_data['questions'] as $question) { ?>

                        <?php $disabled = $question['disabled'] ?? false; ?>

                        <?php if (!$disabled) { ?>
                            <div class="b-quiz-question <?php echo $index === 0 ? 'first-question' : '' ?> <?php echo ($question['is_visible'] && $question['always_visible']) ? 'is-always-visible' : '' ?> <?php echo $question['is_visible'] ? 'is-visible' : '' ?> <?php echo $question['custom_class']; ?>"
                                 data-key="<?php echo !empty($question['key']) ? $question['key'] : 'question_' . ($index + 1) ?>"
                                 data-question-type="<?php echo $question['question_type'] ?>"
                                 data-question-style="<?php echo $question['question_style'] ?>"
                                 data-answer-type="<?php echo $question['answer_type'] ?>"
                                 data-answer-style="<?php echo $question['answer_style'] ?>"
                                 data-funnel="<?php echo $question['funnel'] ?>"
                                 data-hint="<?php echo $question['has_a_hint'] ?>"
                                 data-hide-footer="<?php echo $question['hide_footer'] ? 'true' : 'false' ?>"
                                 data-question-title="<?php echo $question['question_title'] ?>"
                            >
                                <div class="b-quiz-question-inner">
                                    <?php if (!empty($question['featured_image'])) { ?>
                                        <div class="b-img">
                                            <div class="e-img" style="background:url(<?php echo $question['featured_image'] ?>);background-position: center;background-size: cover;background-repeat: no-repeat;"></div>
                                        </div>
                                    <?php } ?>
                                    <div class="main-content-wrapper">
                                        <?php if ($question['has_intro']) { ?>
                                            <div class="b-quiz-question-intro">
                                                <?php echo $question['intro'] ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($question['question_type'] === 'open') { ?>
                                            <?php echo growtype_quiz_include_view('partials.question-types.open', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } else { ?>
                                            <?php echo growtype_quiz_include_view('partials.question-types.radio', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php $index++; ?>

                    <?php } ?>

                    <div class="b-quiz-nav" data-question-title-nav="<?php echo $quiz_data['use_question_title_nav'] ? 'true' : 'false' ?>">
                        <div class="b-quiz-nav-inner">
                            <button class="btn btn-secondary btn-go-back">
                                <span class="icon-arrow"><?php echo growtype_quiz_include_public('icons/arrow.svg') ?></span>
                                <span class="e-label" data-label="<?php echo __('Back', 'growtype-quiz') ?>"><?php echo __('Back', 'growtype-quiz') ?></span>
                            </button>

                            <?php echo growtype_quiz_include_view('partials.components.question-nr'); ?>

                            <button class="btn btn-primary btn-go-next">
                                <span class="e-label" data-label="<?php echo __('Next question', 'growtype-quiz') ?>" data-label-finish="<?php echo __('Finish', 'growtype-quiz') ?>"><?php echo __('Next question', 'growtype-quiz') ?></span>
                                <span class="icon-arrow"><?php echo growtype_quiz_include_public('icons/arrow.svg') ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php get_footer(); ?>

<script>
    let quizInTestMode = <?php echo $quiz_data['is_test_mode'] ? 'true' : 'false' ?>;
    let quizSaveAnswers = <?php echo $quiz_data['save_answers'] === false ? 'false' : 'true' ?>;
    let showCorrectAnswersInitially = <?php echo $quiz_data['show_correct_answers_initially'] === false ? 'false' : 'true' ?>;
    let quizId = <?php echo $post->ID ?>;
    window.quizCurrentFunnel = 'a';
    window.quizQuestionsAmount = $('.b-quiz-question:not(.b-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
    if (!$('.b-quiz-question:visible').hasClass('is-visible')) {
        window.quizQuestionsAmount++;
    }
    if ($('.b-quiz-question[data-question-type="success"][data-hide-footer="false"]')) {
        window.quizQuestionsAmount++;
    }
    window.quizCurrentQuestionNr = 1;
    window.quizQuestionsKeysAlreadyVisited = [];
    window.quizQuestionsFunnelsAlreadyVisited = [];
</script>
