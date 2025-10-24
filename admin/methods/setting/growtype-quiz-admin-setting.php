<?php

class Growtype_Quiz_Admin_Setting
{
    private $loader;

    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_menu', array ($this, 'add_options_page'));

            $this->load_settings_tabs();
        }
    }

    /**
     * Register the options page with the Wordpress menu.
     */
    function add_options_page()
    {
        add_options_page(
            'Growtype - Quiz',
            'Growtype - Quiz',
            'manage_options',
            'growtype-quiz-settings',
            array ($this, 'growtype_quiz_settings_content'),
            1
        );
    }

    /**
     * @return void
     */
    function growtype_quiz_settings_content()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'growtype-quiz-settings') {
            ?>

            <div class="wrap">

                <h1>Growtype - Quiz settings</h1>

                <?php
                if (isset($_GET['updated']) && 'true' == esc_attr($_GET['updated'])) {
                    echo '<div class="updated" ><p>Theme Settings updated.</p></div>';
                }

                if (isset ($_GET['tab'])) {
                    $this->settings_tabs($_GET['tab']);
                } else {
                    $this->settings_tabs();
                }
                ?>

                <form id="growtype_quiz_main_settings_form" method="post" action="options.php">
                    <?php

                    if (isset ($_GET['tab'])) {
                        $tab = $_GET['tab'];
                    } else {
                        $tab = 'general';
                    }

                    switch ($tab) {
                        case 'general':
                            settings_fields('growtype_quiz_settings_general');

                            echo '<table class="form-table">';
                            do_settings_fields('growtype-quiz-settings', 'growtype_quiz_settings_general');
                            echo '</table>';

                            break;
                    }

                    submit_button();

                    ?>
                </form>
            </div>

            <?php
        }
    }

    /**
     * @param $current
     * @return void
     */
    function settings_tabs($current = 'general')
    {
        $tabs['general'] = 'General';

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=growtype-form-settings&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }

    /**
     * @return void
     */
    private function load_settings_tabs()
    {
        require_once GROWTYPE_QUIZ_PATH . 'admin/methods/setting/tabs/growtype-quiz-admin-general-setting.php';
        new Growtype_Quiz_Admin_General_Setting();
    }
}


