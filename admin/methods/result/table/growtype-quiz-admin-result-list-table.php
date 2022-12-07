<?php
/**
 * Results List Table class.
 */

defined('ABSPATH') || exit;

/**
 * List table class
 *
 * @since 2.0.0
 */
class Growtype_Quiz_Admin_Result_List_Table extends WP_List_Table
{
    public $items_count = 0;

    /**
     * Constructor.
     *
     * @since 2.0.0
     */
    public function __construct()
    {
        parent::__construct(array (
            'ajax' => false,
            'plural' => 'results',
            'singular' => 'result',
            'screen' => get_current_screen()->id
        ));
    }

    /**
     * Set up items for display in the list table.
     *
     * Handles filtering of data, sorting, pagination, and any other data
     * manipulation required prior to rendering.
     *
     * @since 2.0.0
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();

        $hidden = array ();

        $search_value = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

        $items_per_page = $this->get_items_per_page('items_per_page', 20);

        $paged = $this->get_pagenum();

        $args = array (
            'offset' => ($paged - 1) * $items_per_page,
            'limit' => $items_per_page,
            'search' => $search_value
        );

        if (isset($_REQUEST['orderby'])) {
            $args['orderby'] = $_REQUEST['orderby'];
        }

        if (isset($_REQUEST['order'])) {
            $args['order'] = $_REQUEST['order'];
        }

        $growtype_quiz_admin_result_crud = new Growtype_Quiz_Admin_Result_Crud();

        $items = $growtype_quiz_admin_result_crud->get_quizes_results($args);

        $total_items = $growtype_quiz_admin_result_crud->get_total_results_amount();

        $this->items = $items;

        $this->items_count = $total_items;

        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array ($columns, $hidden, $sortable);

        $this->set_pagination_args(array (
            'total_items' => $this->items_count,
            "total_pages" => ceil($total_items / $items_per_page),
            'per_page' => $items_per_page,
        ));
    }

    /**
     * Specific columns.
     *
     * @return array
     * @since 2.0.0
     *
     */
    function get_columns()
    {
        return apply_filters('growtype_quiz_members_signup_columns', array (
            'cb' => '<input type="checkbox" />',
            'id' => __('ID', 'growtype-quiz'),
            'user_id' => __('User ID', 'growtype-quiz'),
            'quiz_id' => __('Quiz ID', 'growtype-quiz'),
            'answers' => __('Answers', 'growtype-quiz'),
            'duration' => __('Duration', 'growtype-quiz'),
            'questions_amount' => __('Questions amount', 'growtype-quiz'),
            'correct_answers_amount' => __('Correct answers amount', 'growtype-quiz'),
            'wrong_answers_amount' => __('Correct answers amount', 'growtype-quiz'),
            'evaluated' => __('Evaluated', 'growtype-quiz'),
            'created_at' => __('Created at', 'growtype-quiz'),
            'updated_at' => __('Updated at', 'growtype-quiz'),
        ));
    }

    /**
     * Specific bulk actions
     *
     * @since 2.0.0
     */
    public function get_bulk_actions()
    {
        $actions = array (
//            'activate' => _x('Evaluate', 'Registrations', 'growtype-quiz'),
//            'resend' => _x('Email', 'Registrations', 'growtype-quiz'),
//            'export_selected' => _x('Export selected', 'Registrations', 'growtype-quiz'),
//            'export_all' => _x('Export all', 'Registrations', 'growtype-quiz'),
        );

        if (current_user_can('delete_users')) {
            $actions['bulk_delete'] = __('Delete', 'growtype-quiz');
        }

        return $actions;
    }

    /**
     * @return void
     */
    public function no_items()
    {
        esc_html_e('No items found.', 'growtype-quiz');
    }

    /**
     * @return array[]
     */
    public function get_sortable_columns()
    {
        return array (
            'created_at' => array ('created_at', false),
            'updated_at' => array ('updated_at', false),
            'questions_amount' => array ('questions_amount', false),
        );
    }

    /**
     * @return void
     */
    public function display_rows()
    {
        $items = $this->items;

        $style = '';
        foreach ($items as $userid => $signup_object) {
            $style = (' class="alternate"' == $style) ? '' : ' class="alternate"';
            echo "\n\t" . $this->single_row($signup_object, $style);
        }
    }

    /**
     * @param $signup_object
     * @param $style
     * @param $role
     * @param $numposts
     * @return void
     */
    public function single_row($signup_object = null, $style = '', $role = '', $numposts = 0)
    {
        echo '<tr' . $style . ' id="signup-' . esc_attr($signup_object['id']) . '">';
        echo $this->single_row_columns($signup_object);
        echo '</tr>';
    }

    // Adding action links to column
    function column_id($item)
    {
        $actions = array (
            'edit' => sprintf('<a href="?post_type=%s&page=%s&action=%s&item=%s">' . __('Edit', 'growtype-quiz') . '</a>', Growtype_Quiz::get_growtype_quiz_post_type(), $_REQUEST['page'], 'edit', $item['id']),
            'delete' => sprintf('<a href="?post_type=%s&page=%s&action=%s&item=%s&_wpnonce=%s">' . __('Delete', 'growtype-quiz') . '</a>', Growtype_Quiz::get_growtype_quiz_post_type(), $_REQUEST['page'], 'delete', $item['id'], wp_create_nonce(Growtype_Quiz_Admin_Result::DELETE_NONCE)),
        );

        return sprintf('%1$s %2$s', $item['id'], $this->row_actions($actions));
    }


    /**
     * @param $row
     * @return void
     */
    public function column_cb($row = null)
    {
        ?>
        <input type="checkbox" id="result_<?php echo intval($row['id']) ?>" name="items[]" value="<?php echo esc_attr($row['id']) ?>"/>
        <?php
    }

    /**
     * @param $row
     * @return void
     */
    public function column_answers($row = null)
    {
        echo $row['answers'];
    }

    /**
     * @param $item
     * @param $column_name
     * @return mixed|void|null
     */
    function column_default($item = null, $column_name = '')
    {
        return apply_filters('growtype_quiz_result_custom_column', $item[$column_name], $column_name, $item);
    }
}
