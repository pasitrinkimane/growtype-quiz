<?php

/**
 *
 */
class Growtype_Quiz_Input_Shortcode
{
    function __construct()
    {
        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_quiz_input', array ($this, 'growtype_quiz_input_shortcode'));
        }
    }

    /**
     * @param $attr
     * @return string
     * Posts shortcode
     */
    function growtype_quiz_input_shortcode($attr)
    {
        $input_details = [
            'id' => isset($attr['id']) ? $attr['id'] : 'growtype-quiz-input-' . base64_encode(random_bytes(5)),
            'accept' => isset($attr['accept']) ? $attr['accept'] : '*',
            'required' => isset($attr['required']) ? $attr['required'] : 'false',
            'multiple' => isset($attr['multiple']) ? $attr['multiple'] : 'false',
            'file_max_size' => isset($attr['file_max_size']) ? $attr['file_max_size'] : '6000000',
            'placeholder' => isset($attr['placeholder']) ? $attr['placeholder'] : '',
            'file_max_size_error_message' => isset($attr['file_max_size_error_message']) ? $attr['file_max_size_error_message'] : __('Image :image_name size is too big. Allowed size :max_size.', 'growtype-quiz'),
            'selected_placeholder_single' => isset($attr['selected_placeholder_single']) ? $attr['selected_placeholder_single'] : __(':nr image is selected', 'growtype-quiz'),
            'selected_placeholder_multiple' => isset($attr['selected_placeholder_multiple']) ? $attr['selected_placeholder_multiple'] : __(':nr images are selected', 'growtype-quiz'),
            'type' => isset($attr['type']) ? $attr['type'] : 'text',
            'name' => isset($attr['name']) ? $attr['name'] : '',
            'label' => isset($attr['label']) ? $attr['label'] : '',
            'min' => isset($attr['min']) ? $attr['min'] : '',
            'max' => isset($attr['max']) ? $attr['max'] : '',
        ];

        ob_start();

        echo growtype_quiz_include_view('quiz.partials.components.input', ['input_details' => $input_details]);

        return ob_get_clean();
    }
}
