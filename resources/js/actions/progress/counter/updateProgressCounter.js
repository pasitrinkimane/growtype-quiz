let slideNr = 1;

/**
 * Show current slide
 */
export function updateProgressCounter() {
    let slideTotalNrFormatted = window.quizQuestionsAmount < 10 ? '0' + window.quizQuestionsAmount : window.quizQuestionsAmount;
    let slideNrFormatted = window.growtype_quiz.current_question_nr < 10 ? '0' + window.growtype_quiz.current_question_nr : window.growtype_quiz.current_question_nr;

    if ($('.b-quiz-question-nr').attr('data-style') === 'steps') {
        slideTotalNrFormatted = window.quizQuestionsAmount;
        slideNrFormatted = window.growtype_quiz.current_question_nr;
    }

    $('.b-quiz-question-nr .b-quiz-question-nr-current-slide').text(slideNrFormatted);
    $('.b-quiz-question-nr .b-quiz-question-nr-total-slide').text(slideTotalNrFormatted);

    $('.quiz-wrapper').attr('data-current-question', window.growtype_quiz.current_question_nr);
}
