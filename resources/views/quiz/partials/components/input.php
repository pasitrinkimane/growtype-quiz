<?php
$input_classes = ['growtype-quiz-input-wrapper'];
if (!empty($input_details['class'])) {
    $input_classes = array_merge($input_classes, explode(' ', $input_details['class']));
}
?>

<div class="<?php echo implode(' ', $input_classes) ?>" data-type="<?php echo $input_details['type'] ?>" data-style="<?php echo $input_details['style'] ?>">
    <?php
    if ($input_details['type'] === 'file') { ?>
        <input
            id="<?php echo $input_details['id'] ?>"
            class="input input-file"
            type="<?php echo $input_details['type'] ?>"
            name="<?php echo $input_details['name'] ?>"
            accept="<?php echo $input_details['accept'] ?>"
            <?php echo $input_details['required'] === 'true' ? 'required' : '' ?>
            <?php echo $input_details['multiple'] === 'true' ? 'multiple' : '' ?>
            max-size="<?php echo $input_details['file_max_size'] ?>"
            max-size-error-message="<?php echo $input_details['file_max_size_error_message'] ?>"
            data-selected-placeholder-single="<?php echo $input_details['selected_placeholder_single'] ?>"
            data-selected-placeholder-multiple="<?php echo $input_details['selected_placeholder_multiple'] ?>"
        />
    <?php } else { ?>
        <input
            id="<?php echo $input_details['id'] ?>"
            class="input"
            type="<?php echo $input_details['type'] ?>"
            name="<?php echo $input_details['name'] ?>"
            <?php echo $input_details['required'] === 'true' ? 'required' : '' ?>
            placeholder="<?php echo $input_details['placeholder'] ?>"
            <?php echo isset($input_details['min']) ? 'min="' . $input_details['min'] . '"' : '' ?>
            <?php echo isset($input_details['max']) ? 'max="' . $input_details['max'] . '"' : '' ?>
        />
    <?php } ?>
    <?php if (!empty($input_details['label'])) { ?>
        <label for="<?php echo $input_details['id'] ?>" class="growtype-quiz-input-label"><?php echo $input_details['label'] ?></label>
    <?php } ?>
    <?php echo do_action('growtype_quiz_input_before_close', $input_details) ?>
</div>
