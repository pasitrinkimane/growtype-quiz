<?php

/**
 *
 */
class Growtype_Quiz_Input_Shortcode
{
    function __construct()
    {
        if (!wp_is_json_request()) {
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
            'cat' => isset($attr['cat']) ? $attr['cat'] : '', //height,weight
            'style' => isset($attr['style']) ? $attr['style'] : 'general', //height,weight
            'class' => isset($attr['class']) ? $attr['class'] : '', //height,weight
            'group_label' => isset($attr['group_label']) ? $attr['group_label'] : '',
            'unit_system' => isset($attr['unitsystem']) ? $attr['unitsystem'] : Growtype_Quiz_Public::DEFAULT_UNIT_SYSTEM,
            'show_next_btn' => filter_var($attr['show_next_btn'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        ob_start();

        if (in_array($input_details['cat'], ['height', 'weight'])) {
            $measurements_default = [
                'height' => [
                    'imperial' => [
                        'nav' => [
                            'label' => __('Imperial', 'growtype-quiz'),
                        ],
                        'inputs' => [
                            [
                                'nav_label' => 'ft',
                                'id' => 'input-ft',
                                'label' => !empty($input_details['label']) ? $input_details['label'] : 'ft',
                                'name' => 'ft',
                                'min' => 2,
                                'max' => 10,
                                'placeholder' => 'Feet',
                            ],
                            [
                                'id' => 'input-in',
                                'label' => !empty($input_details['label']) ? $input_details['label'] : 'in',
                                'name' => 'in',
                                'min' => 0,
                                'max' => 11,
                                'placeholder' => 'Inches',
                            ]
                        ],
                    ],
                    'metric' => [
                        'nav' => [
                            'label' => __('Metric', 'growtype-quiz'),
                        ],
                        'inputs' => [
                            [
                                'id' => 'input-cm',
                                'label' => !empty($input_details['label']) ? $input_details['label'] : 'cm',
                                'name' => 'cm',
                                'min' => 50,
                                'max' => 230,
                                'placeholder' => 'Centimetres',
                            ]
                        ]
                    ],
                ],
                'weight' => [
                    'imperial' => [
                        'nav' => [
                            'label' => __('lb', 'growtype-quiz'),
                        ],
                        'inputs' => [
                            [
                                'id' => 'input-lb',
                                'label' => !empty($input_details['label']) ? $input_details['label'] : 'lb',
                                'name' => 'lb',
                                'min' => 90,
                                'max' => 550,
                                'placeholder' => 'Pounds',
                            ]
                        ],
                    ],
                    'metric' => [
                        'nav' => [
                            'label' => __('kg', 'growtype-quiz'),
                        ],
                        'inputs' => [
                            [
                                'id' => 'input-kg',
                                'label' => !empty($input_details['label']) ? $input_details['label'] : 'kg',
                                'name' => 'kg',
                                'min' => 40,
                                'max' => 250,
                                'placeholder' => 'Kilograms',
                            ]
                        ]
                    ],
                ],
            ];

            $measurements = apply_filters('growtype_quiz_measurements', $measurements_default);

            ?>
            <div class="growtype-quiz-measurements-form <?php echo $input_details['class'] ?>">
                <div class="unitsystem-selector">
                    <span class="unitsystem-selector-item <?php echo $input_details['unit_system'] === 'imperial' ? 'is-active' : '' ?>" data-type="imperial"><?php echo $measurements[$input_details['cat']]['imperial']['nav']['label'] ?></span>
                    <span class="unitsystem-selector-item <?php echo $input_details['unit_system'] === 'metric' ? 'is-active' : '' ?>" data-type="metric"><?php echo $measurements[$input_details['cat']]['metric']['nav']['label'] ?></span>
                </div>
                <?php if (isset($input_details['group_label']) && !empty($input_details['group_label'])) { ?>
                    <p class="e-group-label"><?php echo $input_details['group_label'] ?></p>
                <?php } ?>
                <div class="unitsystem-group <?php echo $input_details['unit_system'] === 'imperial' ? 'is-active' : '' ?>" data-type="imperial">
                    <div class="unitsystem-group-inner">
                        <?php
                        $input_details['class'] = '';

                        foreach ($measurements[$input_details['cat']]['imperial']['inputs'] as $input) {
                            $input_details['id'] = $input['id'];
                            $input_details['label'] = $input['label'];
                            $input_details['min'] = $input['min'];
                            $input_details['max'] = $input['max'];
                            $input_details['name'] = $input['name'];
                            $input_details['placeholder'] = $input['placeholder'];
                            echo growtype_quiz_include_view('quiz.partials.components.input', ['input_details' => $input_details]);
                        }
                        ?>
                    </div>
                </div>
                <div class="unitsystem-group <?php echo $input_details['unit_system'] === 'metric' ? 'is-active' : '' ?>" data-type="metric">
                    <div class="unitsystem-group-inner">
                        <?php
                        foreach ($measurements[$input_details['cat']]['metric']['inputs'] as $input) {
                            $input_details['id'] = $input['id'];
                            $input_details['label'] = $input['label'];
                            $input_details['min'] = $input['min'];
                            $input_details['max'] = $input['max'];
                            $input_details['name'] = $input['name'];
                            $input_details['placeholder'] = $input['placeholder'];
                            echo growtype_quiz_include_view('quiz.partials.components.input', ['input_details' => $input_details]);
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } else {
            echo growtype_quiz_include_view('quiz.partials.components.input', ['input_details' => $input_details]);
        }

        return ob_get_clean();
    }
}
