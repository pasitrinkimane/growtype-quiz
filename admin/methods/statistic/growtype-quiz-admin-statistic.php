<?php

class Growtype_Quiz_Admin_Statistic
{
    const PAGE_NAME = 'growtype-quiz-statistics';

    public function __construct()
    {
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
            __('Statistics', 'growtype-quiz'),
            __('Statistics', 'growtype-quiz'),
            'manage_options',
            self::PAGE_NAME,
            array ($this, 'growtype_quiz_statistics_callback')
        );

        add_action("load-$hook", [$this, 'process_actions']);
    }

    /**
     * Display callback for the submenu page.
     */
    function growtype_quiz_statistics_callback()
    {
        $message = $this->get_message();

        $quizes = get_posts([
            'post_type' => Growtype_Quiz::get_growtype_quiz_post_type(),
            'numberposts' => -1,
            'post_status' => 'any',
        ]);

        $results_types = [
            'most_popular_answers' => __('Most popular answers', 'growtype-quiz'),
        ]

        ?>
        <div class="wrap">
            <h2><?php esc_html_e('Statistics', 'growtype-quiz'); ?></h2>

            <?php echo $message ?>

            <form class="tablenav" method="post">
                <div class="alignleft actions">
                    <div style="display: flex;flex-direction: column;">
                        <label for="">Quiz name</label>
                        <select name="quiz_id">
                            <?php foreach ($quizes as $quiz): ?>
                                <option value="<?php echo $quiz->ID ?>"><?php echo $quiz->post_title ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="alignleft actions">
                    <div style="display: flex;flex-direction: column;">
                        <label for="">Results type</label>
                        <select name="results_type">
                            <?php foreach ($results_types as $key => $results_type): ?>
                                <option value="<?php echo $key ?>"><?php echo $results_type ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="alignleft actions">
                    <div style="display: flex;flex-direction: column;">
                        <label for="">Answers limit (how many answers are taken for evaluation)</label>
                        <input type="number" name="answers_limit" value="10000">
                    </div>
                </div>

                <input type="hidden" name="most_popular_answers">
                <input type="hidden" name="evaluate_results" value="true">

                <div class="alignleft actions">
                    <div style="display:flex;">
                        <input type="submit" value="Evaluate" class="button" style="margin-top: 17px;">
                    </div>
                </div>
            </form>

            <?php if (isset($_GET['results']) && !empty($_GET['results'])) {
                foreach ($_GET['results'] as $key => $result) { ?>
                    <h3 style="text-transform: uppercase;margin-top: 40px;"><?php echo $key ?></h3>

                    <table>
                        <thead>
                        <tr>
                            <th>Question key</th>
                            <th>Answer</th>
                            <th>Count</th>
                        </tr>
                        </thead>
                        <?php
                        foreach ($result as $key => $value) {
                            $question_answer = explode('_#_', $key)[0] ?? '';
                            $question_key = explode('_#_', $key)[1] ?? '';
                            ?>
                            <tr>
                                <th><?php echo $question_key ?></th>
                                <th><?php echo $question_answer ?></th>
                                <th><?php echo $value ?></th>
                            </tr>
                        <?php } ?>
                    </table>

                    <?php
                }
            } ?>
        </div>
        <?php
    }

    function get_message()
    {
        $message = '';

        return $message;
    }

    /**
     * Init record related actions
     */
    function process_actions()
    {
        if (isset($_POST['evaluate_results']) && $_POST['evaluate_results']) {
            $quiz_id = $_POST['quiz_id'];
            $results_type = $_POST['results_type'];
            $answers_limit = isset($_POST['answers_limit']) && !empty($_POST['answers_limit']) ? $_POST['answers_limit'] : -1;

            /**
             * MOST_POPULAR_ANSWERS
             */
            if ($results_type === 'most_popular_answers') {
                $growtype_quiz_result_crud = new Growtype_Quiz_Result_Crud();
                $quiz_answers = $growtype_quiz_result_crud->get_single_quiz_results($quiz_id, $answers_limit);

                $total_answers = [
                    'correct' => [],
                    'wrong' => [],
                ];

                foreach ($quiz_answers as $quiz_answer) {
                    if (empty($quiz_answer['answers'])) {
                        continue;
                    }

                    $evaluation = $growtype_quiz_result_crud->evaluate_specific_quiz_answers($quiz_answer['quiz_id'], json_decode($quiz_answer['answers'], true));

                    $total_answers['correct'] = array_merge($total_answers['correct'], $evaluation['correct_answers']);
                    $total_answers['wrong'] = array_merge($total_answers['wrong'], $evaluation['wrong_answers']);
                }

                $wrong_answers = array_count_values($total_answers['wrong']);
                $correct_answers = array_count_values($total_answers['correct']);

                arsort($wrong_answers);
                arsort($correct_answers);

                $_GET['results'] = [
                    'wrong_answers' => !empty($wrong_answers) ? $wrong_answers : [],
                    'correct_answers' => !empty($correct_answers) ? $correct_answers : [],
                ];
            }
        }
    }
}


