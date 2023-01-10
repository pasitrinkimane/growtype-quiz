<script>
    window.growtype_quiz = {}
    window.growtype_quiz.show_correct_answer = <?php echo $quiz_data['show_correct_answer'] === false ? 'false' : 'true' ?>;
    window.growtype_quiz.correct_answer_trigger = '<?php echo $quiz_data['correct_answer_trigger'] ?>';
    window.growtype_quiz.save_answers = <?php echo $quiz_data['save_answers'] === false ? 'false' : 'true' ?>;
    window.growtype_quiz.current_funnel = 'a';
    window.growtype_quiz.current_question_nr = 1;
    window.growtype_quiz.already_visited_questions_keys = [];
    window.growtype_quiz.already_visited_questions_funnels = [];
</script>
