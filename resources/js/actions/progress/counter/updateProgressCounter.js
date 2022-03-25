let slideNr = 1;

/**
 * Show current slide
 */
export function updateProgressCounter() {
    let slideTotalNrFormated = window.quizQuestionsAmount < 10 ? '0' + window.quizQuestionsAmount : window.quizQuestionsAmount;
    let slideNrFormated = window.quizCurrentQuestionNr < 10 ? '0' + window.quizCurrentQuestionNr : window.quizCurrentQuestionNr;

    $('.b-quiz-question-nr').text(slideNrFormated + ' / ' + slideTotalNrFormated);
}
