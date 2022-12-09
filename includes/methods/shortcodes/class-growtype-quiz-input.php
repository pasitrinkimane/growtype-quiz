<?php

/**
 *
 */
class Growtype_Quiz_Input
{
    function __construct()
    {
        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_quiz_input_file', array ($this, 'growtype_quiz_input_file_shortcode'));
        }
    }

    /**
     * @param $attr
     * @return string
     * Posts shortcode
     */
    function growtype_quiz_input_file_shortcode($attr)
    {
        extract(shortcode_atts(array (
            'id' => '',
            'file_type' => 'image',
            'accept' => '*',
            'required' => 'true',
            'multiple' => 'false',
            'file_max_size' => '6000000',
            'placeholder' => __('Select an image', 'growtype-quiz'),
            'file_max_size_error_message' => __('Image :image_name size is too big. Allowed size :max_size.', 'growtype-quiz'),
            'selected_placeholder_single' => __(':nr image is selected', 'growtype-quiz'),
            'selected_placeholder_multiple' => __(':nr images are selected', 'growtype-quiz'),
        ), $attr));

        if (empty($id)) {
            $id = 'growtype-quiz-input-' . base64_encode(random_bytes(5));
        }

        ob_start();

        ?>
        <div class="growtype-quiz-file-input-wrapper" data-file-type="<?php echo $file_type ?>">
            <input
                id="<?php echo $id ?>"
                type="file"
                name="growtype-quiz-input-file"
                accept="<?php echo $accept ?>"
                <?php echo $required === 'true' ? 'required' : '' ?>
                <?php echo $multiple === 'true' ? 'multiple' : '' ?>
                max-size="<?php echo $file_max_size ?>"
                max-size-error-message="<?php echo $file_max_size_error_message ?>"
                data-selected-placeholder-single="<?php echo $selected_placeholder_single ?>"
                data-selected-placeholder-multiple="<?php echo $selected_placeholder_multiple ?>"
            />
            <label for="<?php echo $id ?>" class="growtype-quiz-input-label btn btn-primary"><?php echo $placeholder ?></label>
        </div>
        <?php

        return ob_get_clean();
    }
}
