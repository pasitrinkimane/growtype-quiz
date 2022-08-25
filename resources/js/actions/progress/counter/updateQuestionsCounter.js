export function updateQuestionsCounter() {
    window.quizQuestionsAmount = $('.b-quiz-question[data-funnel="a"]:not(.b-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;

    window.growtype_quiz.already_visited_questions_funnels.map(function (element) {
        if (element !== 'a') {
            let extraSlides = $('.b-quiz-question[data-funnel="' + element + '"]:not(.b-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
            window.quizQuestionsAmount = window.quizQuestionsAmount + extraSlides;
        }
    });

    if (!$('.b-quiz-question:visible').hasClass('is-visible')) {
        window.quizQuestionsAmount++;
    }

    if ($('.b-quiz-question[data-question-type="success"][data-hide-footer="false"]')) {
        window.quizQuestionsAmount++;
    }
}
