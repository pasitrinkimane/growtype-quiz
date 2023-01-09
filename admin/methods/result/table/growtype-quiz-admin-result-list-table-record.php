<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Growtype_Quiz_Admin_Result_List_Table_Record
{
    public function __construct()
    {
        $this->crud = new Growtype_Quiz_Result_Crud();

        $this->redirect = admin_url('edit.php?post_type=' . Growtype_Quiz::get_growtype_quiz_post_type() . '&page=' . Growtype_Quiz_Admin_Result::PAGE_NAME);
    }

    /**
     * Set up the results admin page.
     *
     * Loaded before the page is rendered, this function does all initial
     * setup, including: processing form requests, registering contextual
     * help, and setting up screen options.
     *
     * @since 2.0.0
     *
     * @global $bp_members_signup_list_table
     */
    public function process_delete_action()
    {
        if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === Growtype_Quiz::get_growtype_quiz_post_type() && isset($_REQUEST['action'])) {
            $action = $_REQUEST['action'];
            $action2 = $_REQUEST['action2'] ?? '';

            if ('delete' === $action || 'delete' === $action2) {
                $nonce = esc_attr($_REQUEST['_wpnonce']);

                if (!wp_verify_nonce($nonce, Growtype_Quiz_Admin_Result::DELETE_NONCE)) {
                    die('Go get a life script kiddies');
                } else {
                    self::delete_item(absint($_GET['item']));

                    $this->update_url();
                }
            }

            if ($action === 'bulk_delete' || $action2 === 'bulk_delete') {
                $delete_ids = esc_sql($_POST['items']);

                foreach ($delete_ids as $id) {
                    self::delete_item($id);
                }

                $this->update_url();
            }
        }
    }

    public function delete_item($id)
    {
        return $this->crud->delete_record($id);
    }

    public function update_url()
    {
        $redirect = $this->redirect;

        add_action('admin_footer', function () use ($redirect) {
            ?>
            <script>
                history.pushState({}, null, '<?php echo $redirect ?>');
            </script>
            <?php
        });
    }
}

