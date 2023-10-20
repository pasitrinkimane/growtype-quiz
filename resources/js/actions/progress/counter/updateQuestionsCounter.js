export function updateQuestionsCounter() {
    /**
     * Update url quiz nr
     */
    if (growtype_quiz_local.show_question_nr_in_url) {
        let searchParams = new URLSearchParams(window.location.search);
        searchParams.set('question', window.growtype_quiz_global.current_question_nr);
        let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
        window.history.pushState({path: newurl}, '', newurl);
    }

    window.quizQuestionsAmount = $('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
    window.quizCountedQuestionsAmount = $('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.growtype-quiz-question[data-question-type="info"]):not([class*="skipped"]):not(.is-always-visible)').length + window.growtype_quiz_global.additional_questions_amount;

    window.growtype_quiz_global.already_visited_questions_funnels.map(function (element) {
        if (element !== window.growtype_quiz_global.initial_funnel) {
            let extraSlides = $('.growtype-quiz-question[data-funnel="' + element + '"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
            window.quizQuestionsAmount = window.quizQuestionsAmount + extraSlides;
        }
    });

    if ($('.growtype-quiz-question:visible').hasClass('is-visible')) {
        window.quizQuestionsAmount--;
    }
}
