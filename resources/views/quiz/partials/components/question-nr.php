<div class="growtype-quiz-question-nr" data-style="<?php echo get_field('slide_counter_style') ?>">
    <?php if (get_field('slide_counter_style') === 'steps') { ?>
        <span class="growtype-quiz-question-nr-current-slide"></span>
        <span class="growtype-quiz-question-nr-separator"><?php echo __('from', 'growtype-quiz') ?></span>
        <span class="growtype-quiz-question-nr-total-slide"></span>
        <span class="growtype-quiz-question-text"><?php echo __('steps', 'growtype-quiz') ?></span>
    <?php } elseif (get_field('slide_counter_style') === 'outof') { ?>
        <span class="growtype-quiz-question-nr-current-slide"></span>
        <span class="growtype-quiz-question-nr-separator"><?php echo __('from', 'growtype-quiz') ?></span>
        <span class="growtype-quiz-question-nr-total-slide"></span>
    <?php } else { ?>
        <span class="growtype-quiz-question-nr-current-slide"></span>
        <span class="growtype-quiz-question-nr-separator">/</span>
        <span class="growtype-quiz-question-nr-total-slide"></span>
    <?php } ?>
</div>
