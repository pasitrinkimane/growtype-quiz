<?php
if ($iframe_hide_header_footer) {
    echo '<style>header { display: none; } footer { display: none; } .growtype-quiz-wrapper { margin: 0; }</style>';
}
?>

<div class="growtype-quiz-wrapper" data-current-question-type="" data-quiz-id="<?php echo $post->ID ?>">
    <?php
    $intro_content = apply_filters('the_content', get_the_content());
    $intro_f_img = isset(wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail')[0]) ? wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail')[0] : '';
    ?>
    <?php if ($intro_content || $intro_f_img) { ?>
        <section class="s-intro" style="background:url(<?php echo $intro_f_img; ?>);background-size: cover;background-position: center;background-repeat: no-repeat;">
            <div class="container">
                <?php echo apply_filters('the_content', get_the_content()); ?>
            </div>
        </section>
    <?php } ?>

    <section class="s-quiz">
        <div class="container">

            <div class="growtype-quiz" data-type="<?php echo $quiz_data['quiz_type'] ?>">
                <div class="growtype-quiz-inner">
                    <?php if ($quiz_data['progress_bar']) { ?>
                        <div class="growtype-quiz-progressbar mb-4">
                            <div class="growtype-quiz-progressbar-inner"></div>
                        </div>
                    <?php } ?>
                    <?php if ($quiz_data['slide_counter'] && ($quiz_data['slide_counter_position'] === 'top' || $quiz_data['slide_counter_position'] === 'both')) { ?>
                        <div class="growtype-quiz-header">
                            <?php echo growtype_quiz_include_view('quiz.partials.components.question-nr'); ?>
                        </div>
                    <?php } ?>
                    <?php if ($quiz_data['limited_time']) { ?>
                        <div class="growtype-quiz-timer" data-duration="<?php echo $quiz_data['duration'] ?>">
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
                            <div class="growtype-quiz-question <?php echo $index === 0 ? 'first-question' : '' ?> <?php echo ($question['is_visible'] && $question['always_visible']) ? 'is-always-visible' : '' ?> <?php echo $question['is_visible'] ? 'is-visible' : '' ?> <?php echo $question['custom_class']; ?>"
                                 data-key="<?php echo !empty($question['key']) ? $question['key'] : 'question_' . ($index + 1) ?>"
                                 data-question-type="<?php echo $question['question_type'] ?>"
                                 data-question-style="<?php echo $question['question_style'] ?>"
                                 data-answer-type="<?php echo !is_array($question['answer_type']) ? $question['answer_type'] : '' ?>"
                                 data-answer-style="<?php echo $question['answer_style'] ?>"
                                 data-funnel="<?php echo $question['funnel'] ?>"
                                 data-hint="<?php echo $question['has_a_hint'] ?>"
                                 data-hide-footer="<?php echo $question['hide_footer'] ? 'true' : 'false' ?>"
                                 data-question-title="<?php echo $question['question_title'] ?>"
                                 data-answer-required="<?php echo $question['not_required'] ? 'false' : 'true' ?>"
                                 data-hide-back-button="<?php echo $question['hide_back_button'] ? 'true' : 'false' ?>"
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
                                        <?php if ($question['has_intro']) { ?>
                                            <div class="growtype-quiz-question-intro">
                                                <?php echo $question['intro'] ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($question['question_type'] === 'open') { ?>
                                            <?php echo growtype_quiz_include_view('quiz.partials.question-types.open', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } else { ?>
                                            <?php echo growtype_quiz_include_view('quiz.partials.question-types.radio', ['question' => $question, 'quiz_data' => $quiz_data]) ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php $index++; ?>

                    <?php } ?>

                    <div class="growtype-quiz-nav" data-question-title-nav="<?php echo $quiz_data['use_question_title_nav'] ? 'true' : 'false' ?>">
                        <div class="growtype-quiz-nav-inner">
                            <button class="btn btn-secondary growtype-quiz-btn-go-back">
                                <span class="icon-arrow"><?php echo growtype_quiz_render_svg('images/arrow.svg') ?></span>
                                <span class="e-label" data-label="<?php echo __('Back', 'growtype-quiz') ?>"><?php echo __('Back', 'growtype-quiz') ?></span>
                            </button>

                            <?php if ($quiz_data['slide_counter'] && ($quiz_data['slide_counter_position'] === 'bottom' || $quiz_data['slide_counter_position'] === 'both')) { ?>
                                <?php echo growtype_quiz_include_view('quiz.partials.components.question-nr'); ?>
                            <?php } ?>

                            <button class="btn btn-primary growtype-quiz-btn-go-next">
                                <span class="e-label" data-label="<?php echo __('Next question', 'growtype-quiz') ?>" data-label-finish="<?php echo __('Finish', 'growtype-quiz') ?>" data-label-start="<?php echo __('Start', 'growtype-quiz') ?>"><?php echo __('Next question', 'growtype-quiz') ?></span>
                                <span class="icon-arrow"><?php echo growtype_quiz_render_svg('images/arrow.svg') ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
