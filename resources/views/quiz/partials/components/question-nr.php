<div class="growtype-quiz-question-nr <?= $quiz_data['quiz_header_slide_counter_hide_initially'] ? 'hide-initially' : 'show-initially' ?>" data-counter-style="<?php echo $quiz_data['slide_counter_style'] ?>">
    <?php if ($quiz_data['slide_counter_style'] === 'steps') { ?>
        <span class="growtype-quiz-question-nr-current-slide"></span>
        <span class="growtype-quiz-question-nr-separator"><?php echo __('from', 'growtype-quiz') ?></span>
        <span class="growtype-quiz-question-nr-total-slide"></span>
        <span class="growtype-quiz-question-text"><?php echo __('steps', 'growtype-quiz') ?></span>
    <?php } elseif ($quiz_data['slide_counter_style'] === 'outof') { ?>
        <span class="growtype-quiz-question-nr-current-slide"></span>
        <span class="growtype-quiz-question-nr-separator"><?php echo __('from', 'growtype-quiz') ?></span>
        <span class="growtype-quiz-question-nr-total-slide"></span>
    <?php } elseif ($quiz_data['slide_counter_style'] === 'answered_only') { ?>
        <span class="growtype-quiz-question-nr-separator"><?php echo __('Answered:', 'growtype-quiz') ?></span>
        <span class="growtype-quiz-question-nr-current-slide"></span>
    <?php } else { ?>
        <span class="growtype-quiz-question-nr-current-slide"></span>
        <span class="growtype-quiz-question-nr-separator">/</span>
        <span class="growtype-quiz-question-nr-total-slide"></span>
    <?php } ?>
</div>
