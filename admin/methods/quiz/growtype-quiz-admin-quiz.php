<?php

class Growtype_Quiz_Admin_Quiz
{
    public function __construct()
    {
        // Add the meta box
        add_action('add_meta_boxes', [$this, 'add_quiz_meta_box']);

        // Save the meta box data
        add_action('save_post_quiz', [$this, 'save_quiz_meta_box']);
    }

    public function add_quiz_meta_box()
    {
        add_meta_box(
            'quiz_options',                      // ID
            'Quiz Options',                      // Title
            [$this, 'render_quiz_meta_box'],     // Callback
            'quiz',                              // Post type
            'side',                              // Context (right side)
            'default'                            // Priority
        );
    }

    public function render_quiz_meta_box($post)
    {
        // Security nonce
        wp_nonce_field('save_quiz_meta_box', 'quiz_meta_box_nonce');

        self::render_quiz_theme_selector($post);
    }

    public static function render_quiz_theme_selector($post)
    {
        $selected_option = growtype_quiz_get_quiz_theme($post->ID);

        $default_options = [
            [
                'value' => '',
                'label' => 'Default',
                'description' => 'Default theme',
            ]
        ];

        // Define your select options
        $options = apply_filters('growtype_quiz_admin_meta_box_default_options', $default_options);

        ?>
        <label for="quiz_theme">theme:</label>
        <select name="quiz_theme" id="quiz_theme" style="width:100%;">
            <?php foreach ($options as $option): ?>
                <option value="<?= esc_attr($option['value']); ?>" <?= selected($selected_option, $option['value']); ?>>
                    <?= esc_html($option['label']); ?><?= isset($option['description']) && !empty($option['description']) ? ' - ' . esc_html($option['description']) : ''; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function save_quiz_meta_box($post_id)
    {
        if (!isset($_POST['quiz_meta_box_nonce']) ||
            !wp_verify_nonce($_POST['quiz_meta_box_nonce'], 'save_quiz_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['quiz_theme'])) {
            update_post_meta($post_id, '_quiz_theme', sanitize_text_field($_POST['quiz_theme']));
        }
    }
}
