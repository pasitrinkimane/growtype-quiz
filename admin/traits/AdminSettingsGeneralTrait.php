<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    growtype_quiz
 * @subpackage growtype_quiz/admin/partials
 */

trait AdminSettingsGeneralTrait
{
    public function general_content()
    {
        /**
         *
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_custom_post_type_label_name' // option name
        );

        add_settings_field(
            'growtype_quiz_custom_post_type_label_name',
            'Label Name (default: Quizes)',
            array ($this, 'growtype_quiz_custom_post_type_label_name_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         *
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_custom_post_type_label_singular_name' // option name
        );

        add_settings_field(
            'growtype_quiz_custom_post_type_label_singular_name',
            'Label Singular Name (default: Quiz)',
            array ($this, 'growtype_quiz_custom_post_type_label_singular_name_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         *
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_custom_post_type' // option name
        );

        add_settings_field(
            'growtype_quiz_custom_post_type',
            'Post Type (default: quiz)',
            array ($this, 'growtype_quiz_custom_post_type_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         *
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_theme' // option name
        );

        add_settings_field(
            'growtype_quiz_theme',
            'Quiz Theme',
            array ($this, 'growtype_quiz_theme_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );
    }

    /**
     *
     */
    function growtype_quiz_custom_post_type_label_name_callback()
    {
        $value = get_option('growtype_quiz_custom_post_type_label_name');
        ?>
        <input type="text" name="growtype_quiz_custom_post_type_label_name" value="<?php echo $value ?>"/>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_custom_post_type_label_singular_name_callback()
    {
        $value = get_option('growtype_quiz_custom_post_type_label_singular_name');
        ?>
        <input type="text" name="growtype_quiz_custom_post_type_label_singular_name" value="<?php echo $value ?>"/>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_custom_post_type_callback()
    {
        $value = get_option('growtype_quiz_custom_post_type');
        ?>
        <input type="text" name="growtype_quiz_custom_post_type" value="<?php echo $value ?>"/>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_theme_callback()
    {
        $selected = get_option('growtype_quiz_theme');
        ?>
        <select name='growtype_quiz_theme'>
            <option value='none' <?php selected($selected, 'none'); ?>>None</option>
            <option value='rekviem' <?php selected($selected, 'rekviem'); ?>>Rekviem</option>
        </select>
        <?php
    }
}


