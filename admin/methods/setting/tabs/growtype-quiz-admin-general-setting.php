<?php

class Growtype_Quiz_Admin_General_Setting
{
    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_init', array ($this, 'growtype_quiz_general_admin_settings'));
        }
    }

    public function growtype_quiz_general_admin_settings()
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
         * main post type
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_custom_post_type' // option name
        );

        add_settings_field(
            'growtype_quiz_custom_post_type',
            'Main Post Type (default: quiz)',
            array ($this, 'growtype_quiz_custom_post_type_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         * extra post type
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_extra_post_types' // option name
        );

        add_settings_field(
            'growtype_quiz_extra_post_types',
            'Extra Post Types',
            array ($this, 'growtype_quiz_extra_post_types_callback'),
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
            'Default Quiz Theme',
            array ($this, 'growtype_quiz_theme_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         * Theme header
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_theme_header' // option name
        );

        add_settings_field(
            'growtype_quiz_theme_header',
            'Theme Header File Name',
            array ($this, 'growtype_quiz_theme_header_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         * Theme footer
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_theme_footer' // option name
        );

        add_settings_field(
            'growtype_quiz_theme_footer',
            'Theme Footer File Name',
            array ($this, 'growtype_quiz_theme_footer_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         *
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_iframe_hide_header_footer' // option name
        );

        add_settings_field(
            'growtype_quiz_iframe_hide_header_footer',
            'If Iframe hide Header and Hooter',
            array ($this, 'growtype_quiz_iframe_hide_header_footer_callback'),
            'growtype-quiz-settings',
            'growtype_quiz_settings_general'
        );

        /**
         *
         */
        register_setting(
            'growtype_quiz_settings_general', // settings group name
            'growtype_quiz_results_page' // option name
        );

        add_settings_field(
            'growtype_quiz_results_page',
            'Results Page',
            array ($this, 'growtype_quiz_results_page_callback'),
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
    function growtype_quiz_extra_post_types_callback()
    {
        $value = get_option('growtype_quiz_extra_post_types');
        ?>
        <input type="text" name="growtype_quiz_extra_post_types" value="<?php echo $value ?>"/>
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
            <option value='theme-1' <?php selected($selected, 'theme-1'); ?>>Theme 1</option>
        </select>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_theme_header_callback()
    {
        $value = get_option('growtype_quiz_theme_header');
        ?>
        <input type="text" name="growtype_quiz_theme_header" value="<?php echo $value ?>"/>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_theme_footer_callback()
    {
        $value = get_option('growtype_quiz_theme_footer');
        ?>
        <input type="text" name="growtype_quiz_theme_footer" value="<?php echo $value ?>"/>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_iframe_hide_header_footer_callback()
    {
        $value = get_option('growtype_quiz_iframe_hide_header_footer');
        ?>
        <input type="checkbox" id="growtype_quiz_iframe_hide_header_footer" name="growtype_quiz_iframe_hide_header_footer" value="1" <?php echo checked(1, $value, false) ?>/>
        <?php
    }

    /**
     *
     */
    function growtype_quiz_results_page_callback()
    {
        $pages = get_pages();
        $selected = get_option('growtype_quiz_results_page');
        ?>
        <select name='growtype_quiz_results_page'>
            <?php foreach ($pages as $page) { ?>
                <option value='<?php echo $page->ID ?>' <?php selected($page->ID === (int)$selected); ?>><?php echo $page->post_title ?> - <?php echo $page->ID ?></option>
            <?php } ?>
        </select>
        <?php
    }
}


