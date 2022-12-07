<?php
$max_characters_amount = $question['max_characters_amount'];
$max_characters_amount_tag = !empty($max_characters_amount) ? 'maxlength="' . $max_characters_amount . '"' : '';
?>

<?php if ($question['question_type'] !== 'success') { ?>
    <div class="growtype-quiz-question-answers-wrapper">
        <div class="growtype-quiz-question-answer">
            <textarea name="answer-open" cols="30" rows="10" <?php echo $max_characters_amount_tag ?> required></textarea>
            <?php if (!empty($max_characters_amount)) { ?>
                <div class="e-explanation">
                    <?php echo __('Maximum characters amount', 'growtype-quiz') ?> - <?php echo $max_characters_amount ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
