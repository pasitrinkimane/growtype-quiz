<?php
$input_wrapper_classes = ['growtype-quiz-input-wrapper'];
if (isset($input_details['class']) && !empty($input_details['class'])) {
    $input_wrapper_classes = array_merge($input_wrapper_classes, explode(' ', $input_details['class']));
}

$input_classes = ['input'];

if ($input_details['type'] === 'file') {
    $input_classes[] = 'input-file';
}

if (isset($input_details['field_class']) && !empty($input_details['field_class'])) {
    $input_classes = array_merge($input_classes, explode(' ', $input_details['field_class']));
}
?>

<div class="<?php echo implode(' ', $input_wrapper_classes) ?>" data-type="<?php echo $input_details['type'] ?>" data-style="<?php echo $input_details['style'] ?>">
    <input
        <?php echo isset($input_details['id']) ? 'id="' . $input_details['id'] . '"' : '' ?>
        class="<?php echo implode(' ', $input_classes) ?>"
        type="<?php echo $input_details['type'] ?>"
        name="<?php echo $input_details['name'] ?>"
        <?php echo isset($input_details['accept']) ? 'accept="' . $input_details['accept'] . '"' : '' ?>
        <?php echo isset($input_details['placeholder']) ? 'placeholder="' . $input_details['placeholder'] . '"' : '' ?>
        <?php echo isset($input_details['required']) && $input_details['required'] === 'true' ? 'required' : '' ?>
        <?php echo isset($input_details['multiple']) && $input_details['multiple'] === 'true' ? 'multiple' : '' ?>
        <?php echo isset($input_details['file_max_size']) ? 'max-size="' . $input_details['file_max_size'] . '"' : '' ?>
        <?php echo isset($input_details['file_max_size_error_message']) ? 'max-size-error-message="' . $input_details['file_max_size_error_message'] . '"' : '' ?>
        <?php echo isset($input_details['selected_placeholder_single']) ? 'data-selected-placeholder-single="' . $input_details['selected_placeholder_single'] . '"' : '' ?>
        <?php echo isset($input_details['selected_placeholder_multiple']) ? 'data-selected-placeholder-multiple="' . $input_details['selected_placeholder_multiple'] . '"' : '' ?>
        <?php echo isset($input_details['min']) ? 'min="' . $input_details['min'] . '"' : '' ?>
        <?php echo isset($input_details['max']) ? 'max="' . $input_details['max'] . '"' : '' ?>
    />

    <?php if (!empty($input_details['label'])) { ?>
        <label for="<?php echo $input_details['id'] ?>" class="growtype-quiz-input-label"><?php echo $input_details['label'] ?></label>
    <?php } ?>

    <?php echo do_action('growtype_quiz_input_before_close', $input_details) ?>
</div>
