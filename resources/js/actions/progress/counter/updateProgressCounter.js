let slideNr = 1;

/**
 * Show current slide
 */
export function updateProgressCounter() {
    let slideTotalNrFormated = window.quizQuestionsAmount < 10 ? '0' + window.quizQuestionsAmount : window.quizQuestionsAmount;
    let slideNrFormated = window.quizCurrentQuestionNr < 10 ? '0' + window.quizCurrentQuestionNr : window.quizCurrentQuestionNr;

    if ($('.b-quiz-question-nr').attr('data-style') === 'steps') {
        slideTotalNrFormated = window.quizQuestionsAmount;
        slideNrFormated = window.quizCurrentQuestionNr;
    }

    $('.b-quiz-question-nr .b-quiz-question-nr-current-slide').text(slideNrFormated);
    $('.b-quiz-question-nr .b-quiz-question-nr-total-slide').text(slideTotalNrFormated);

    $('.quiz-wrapper').attr('data-current-question', window.quizCurrentQuestionNr);
}
