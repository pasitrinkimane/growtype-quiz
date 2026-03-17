<?php

class Growtype_Quiz_Admin_User
{
    public function __construct()
    {
        add_filter('manage_users_columns', [$this, 'add_quizzes_solved_column']);
        add_filter('manage_users_custom_column', [$this, 'show_quizzes_solved_column_content'], 10, 3);
        add_filter('manage_edit-users_sortable_columns', [$this, 'make_quizzes_solved_column_sortable']);
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function add_quizzes_solved_column($columns)
    {
        $columns['quizzes_solved'] = __('Quizzes Solved', 'growtype-quiz');

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $user_id
     * @return string
     */
    public function show_quizzes_solved_column_content($value, $column_name, $user_id)
    {
        if ('quizzes_solved' === $column_name) {
            $count = Growtype_Quiz_Result_Crud::get_user_results_count($user_id);

            if ($count > 0) {
                $url = admin_url('edit.php?post_type=' . Growtype_Quiz::get_growtype_quiz_post_type() . '&page=growtype-quiz-results&user_id=' . $user_id);
                return '<a href="' . esc_url($url) . '">' . $count . '</a>';
            }

            return '0';
        }

        return $value;
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function make_quizzes_solved_column_sortable($columns)
    {
        $columns['quizzes_solved'] = 'quizzes_solved';

        return $columns;
    }
}
