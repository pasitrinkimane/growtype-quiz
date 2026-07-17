<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

class Growtype_Quiz_Admin_Result_List_Table_Record
{
    /** @var Growtype_Quiz_Result_Crud */
    private Growtype_Quiz_Result_Crud $crud;

    /** @var string */
    private string $redirect;

    public function __construct()
    {
        $this->crud = new Growtype_Quiz_Result_Crud();

        $this->redirect = admin_url(
            'edit.php?post_type=' . Growtype_Quiz::get_growtype_quiz_post_type() .
            '&page=' . Growtype_Quiz_Admin_Result::PAGE_NAME
        );
    }

    /**
     * Handle delete actions.
     */
    public function process_delete_action(): void
    {
        $post_type = $_REQUEST['post_type'] ?? '';
        $action = $_REQUEST['action'] ?? '';
        $action2 = $_REQUEST['action2'] ?? '';

        if ($post_type !== Growtype_Quiz::get_growtype_quiz_post_type()) {
            return;
        }

        // Single delete
        if ('delete' === $action || 'delete' === $action2) {
            $nonce = $_REQUEST['_wpnonce'] ?? '';

            if (!wp_verify_nonce($nonce, Growtype_Quiz_Admin_Result::DELETE_NONCE)) {
                wp_die('Security check failed');
            }

            $item_id = absint($_GET['item'] ?? 0);
            if ($item_id) {
                $this->delete_item($item_id);
            }

            $this->update_url();
        }

        // Bulk delete
        if ('bulk_delete' === $action || 'bulk_delete' === $action2) {
            $delete_ids = $_POST['items'] ?? [];

            if (is_array($delete_ids)) {
                foreach ($delete_ids as $id) {
                    $id = absint($id);
                    if ($id) {
                        $this->delete_item($id);
                    }
                }
            }

            $this->update_url();
        }
    }

    public function delete_item(int $id): bool
    {
        return $this->crud->delete_record($id);
    }

    public function update_url(): void
    {
        $redirect = esc_url($this->redirect);

        add_action('admin_footer', function () use ($redirect) {
            ?>
            <script type="text/javascript">
                history.pushState({}, null, '<?php echo $redirect; ?>');
            </script>
            <?php
        });
    }
}
