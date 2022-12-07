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
            'accept' => 'post',
            'required' => 'true',
        ), $attr));


        return '<div class="growtype_quiz_input_wrapper"><input type="file" name="growtype_quiz_input_file" accept="' . $accept . '" ' . ($required === 'true' ? 'required' : '') . ' /></div>';
    }
}
