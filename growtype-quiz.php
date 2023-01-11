<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Growtype_Quiz
 *
 * @wordpress-plugin
 * Plugin Name:       Growtype - Quiz
 * Plugin URI:        http://newcoolstudio.com/
 * Description:       Creates CPT with advanced quiz functionality. Requires Advanced custom fields plugin.
 * Version:           1.0.0
 * Author:            Growtype
 * Author URI:        http://newcoolstudio.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       growtype-quiz
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('GROWTYPE_QUIZ_VERSION', '1.1.8');

/**
 * Plugin text domain
 */
define('GROWTYPE_QUIZ_TEXT_DOMAIN', 'growtype-quiz');

/**
 * Plugin post type.
 */
define('GROWTYPE_QUIZ_POST_TYPE', 'quiz');

/**
 * Plugin dir path
 */
define('GROWTYPE_QUIZ_PATH', plugin_dir_path(__FILE__));

/**
 * Plugin url
 */
define('GROWTYPE_QUIZ_URL', plugin_dir_url(__FILE__));

/**
 * Plugin url public
 */
define('GROWTYPE_QUIZ_URL_PUBLIC', plugin_dir_url(__FILE__) . 'public/');

/**
 * Plugin taxonomy.
 */
define('GROWTYPE_QUIZ_TAXONOMY', 'quiz_cat');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-growtype-quiz-activator.php
 */
function activate_growtype_quiz()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-growtype-quiz-activator.php';
    Growtype_Quiz_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-growtype-quiz-deactivator.php
 */
function deactivate_growtype_quiz()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-growtype-quiz-deactivator.php';
    Growtype_Quiz_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_growtype_quiz');
register_deactivation_hook(__FILE__, 'deactivate_growtype_quiz');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-growtype-quiz.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_growtype_quiz()
{
    $plugin = new Growtype_Quiz();
    $plugin->run();
}

run_growtype_quiz();
