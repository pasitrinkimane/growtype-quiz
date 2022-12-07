<?php

class Growtype_Quiz_Admin_Result
{
    const DELETE_NONCE = 'growtype_quiz_delete_item';
    const PAGE_NAME = 'growtype-quiz-results';

    public $items_obj;

    public function __construct()
    {
        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_action('admin_menu', array ($this, 'items_tab_init'));
    }

    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    /**
     * Create the All Users / Profile > Edit Profile and All Users Signups submenus.
     *
     * @since 2.0.0
     *
     */
    public function items_tab_init()
    {
        $hook = add_submenu_page(
            'edit.php?post_type=' . Growtype_Quiz::get_growtype_quiz_post_type(),
            __('Results', 'growtype-quiz'),
            __('Results', 'growtype-quiz'),
            'manage_options',
            self::PAGE_NAME,
            array ($this, 'growtype_quiz_result_callback')
        );

        add_action("load-$hook", [$this, 'screen_option']);
        add_action("load-$hook", [$this, 'process_actions']);
    }

    /**
     * Screen options
     */
    public function screen_option()
    {
        $option = 'per_page';

        $args = [
            'label' => 'Items',
            'default' => 20,
            'option' => 'items_per_page'
        ];

        add_screen_option($option, $args);

        require_once GROWTYPE_QUIZ_PATH . 'admin/methods/result/table/growtype-quiz-admin-result-list-table.php';
        $this->items_obj = new Growtype_Quiz_Admin_Result_List_Table();
    }

    /**
     * Display callback for the submenu page.
     */
    function growtype_quiz_result_callback()
    {
        $message = $this->get_message();

        ?>
        <div class="wrap">
            <h2><?php esc_html_e('Results', 'growtype-quiz'); ?></h2>

            <?php echo $message ?>

            <form method="post">
                <?php
                $this->items_obj->prepare_items();
                $this->items_obj->search_box('Search', 'search');
                $this->items_obj->display();
                ?>
            </form>
        </div>
        <?php
    }

    function get_message()
    {
        $message = '';

        if ('delete' === $this->items_obj->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('ID %d was deleted.', 'growtype-quiz'), $_REQUEST['item']) . '</p></div>';
        } elseif ('bulk_delete' === $this->items_obj->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d items were deleted.', 'growtype-quiz'), count($_POST['items'])) . '</p></div>';
        }

        return $message;
    }

    /**
     * Init record related actions
     */
    function process_actions()
    {
        require_once GROWTYPE_QUIZ_PATH . 'admin/methods/result/table/growtype-quiz-admin-result-list-table-record.php';

        $growtype_quiz_admin_result_list_table_record = new Growtype_Quiz_Admin_Result_List_Table_Record();

        $growtype_quiz_admin_result_list_table_record->process_delete_action();
    }
}


