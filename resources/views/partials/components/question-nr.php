<div class="b-quiz-question-nr" data-style="<?php echo get_field('slide_counter_style') ?>">
    <?php if (get_field('slide_counter_style') === 'steps') { ?>
        <span class="b-quiz-question-nr-current-slide"></span>
        <span class="b-quiz-question-nr-separator"><?php echo __('from', 'growtype-quiz') ?></span>
        <span class="b-quiz-question-nr-total-slide"></span>
        <span class="b-quiz-question-text"><?php echo __('steps', 'growtype-quiz') ?></span>
    <?php } else { ?>
        <span class="b-quiz-question-nr-current-slide"></span>
        <span class="b-quiz-question-nr-separator">/</span>
        <span class="b-quiz-question-nr-total-slide"></span>
    <?php } ?>
</div>
