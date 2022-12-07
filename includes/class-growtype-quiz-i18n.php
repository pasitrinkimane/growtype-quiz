<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Growtype_Quiz
 * @subpackage Growtype_Quiz/includes
 * @author     Your Name <email@example.com>
 */
class Growtype_Quiz_i18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            GROWTYPE_QUIZ_TEXT_DOMAIN,
            false,
            GROWTYPE_QUIZ_TEXT_DOMAIN . '/languages/'
        );
    }
}
