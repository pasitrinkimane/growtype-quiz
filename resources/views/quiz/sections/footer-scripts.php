<script>
    let quizInTestMode = <?php echo $quiz_data['is_test_mode'] ? 'true' : 'false' ?>;
    let quizSaveAnswers = <?php echo $quiz_data['save_answers'] === false ? 'false' : 'true' ?>;
    let showCorrectAnswersInitially = <?php echo $quiz_data['show_correct_answers_initially'] === false ? 'false' : 'true' ?>;
    window.growtype_quiz = {}
    window.growtype_quiz.current_funnel = 'a';
    window.growtype_quiz.current_question_nr = 1;
    window.growtype_quiz.already_visited_questions_keys = [];
    window.growtype_quiz.already_visited_questions_funnels = [];
</script>
