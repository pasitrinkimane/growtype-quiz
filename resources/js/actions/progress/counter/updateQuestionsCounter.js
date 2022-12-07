export function updateQuestionsCounter() {
    window.quizQuestionsAmount = $('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;

    window.growtype_quiz.already_visited_questions_funnels.map(function (element) {
        if (element !== 'a') {
            let extraSlides = $('.growtype-quiz-question[data-funnel="' + element + '"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
            window.quizQuestionsAmount = window.quizQuestionsAmount + extraSlides;
        }
    });

    if ($('.growtype-quiz-question:visible').hasClass('is-visible')) {
        window.quizQuestionsAmount--;
    }

    if ($('.growtype-quiz-question[data-question-type="success"][data-hide-footer="false"]')) {
        window.quizQuestionsAmount++;
    }
}
