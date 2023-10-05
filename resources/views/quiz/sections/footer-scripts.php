<script>
    if (new URLSearchParams(window.location.search).get('question') === '1' || new URLSearchParams(window.location.search).get('question') === null) {
        sessionStorage.setItem('growtype_quiz_global', JSON.stringify({}));

        window.growtype_quiz_data.answers = {};
        sessionStorage.setItem('growtype_quiz_answers', JSON.stringify(window.growtype_quiz_data.answers));
    }

    window.growtype_quiz_global = sessionStorage.getItem('growtype_quiz_global') !== null ? JSON.parse(sessionStorage.getItem('growtype_quiz_global')) : {};

    if (Object.entries(window.growtype_quiz_global).length === 0) {
        window.growtype_quiz_global.initial_funnel = 'a';
        window.growtype_quiz_global.current_funnel = window.growtype_quiz_global.initial_funnel;
        window.growtype_quiz_global.current_question_nr = 1;
        window.growtype_quiz_global.additional_questions_amount = 0;
        window.growtype_quiz_global.current_question_counter_nr = 0;
        window.growtype_quiz_global.already_visited_questions_keys = [];
        window.growtype_quiz_global.already_visited_questions_funnels = [];
    }
</script>
