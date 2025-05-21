/**
 * Show current slide
 */
export function updateProgressCounter(quizWrapper) {
    let quizId = quizWrapper.attr('id');
    let slideTotalNrFormatted = window.growtype_quiz_global[quizId]['quiz_questions_amount'] < 10 ? '0' + window.growtype_quiz_global[quizId]['quiz_questions_amount'] : window.growtype_quiz_global[quizId]['quiz_questions_amount'];
    let slideNrFormatted = window.growtype_quiz_global[quizId]['current_question_counter_nr'] < 10 ? '0' + window.growtype_quiz_global[quizId]['current_question_counter_nr'] : window.growtype_quiz_global[quizId]['current_question_counter_nr'];

    if (quizWrapper.find('.growtype-quiz-question-nr').attr('data-counter-style') === 'steps' || quizWrapper.find('.growtype-quiz-question-nr').attr('data-counter-style') === 'outof') {
        slideTotalNrFormatted = window.growtype_quiz_global[quizId]['quiz_questions_amount'];
        slideNrFormatted = window.growtype_quiz_global[quizId]['current_question_counter_nr'];
    }

    if (quizWrapper.find('.growtype-quiz-question-nr').attr('data-counter-style') === 'answered_only') {
        slideTotalNrFormatted = window.growtype_quiz_global[quizId]['quiz_questions_amount'];
        slideNrFormatted = window.growtype_quiz_global[quizId]['current_question_counter_nr'] - 1;
    }

    quizWrapper.find('.growtype-quiz-question-nr .growtype-quiz-question-nr-current-slide').text(slideNrFormatted);
    quizWrapper.find('.growtype-quiz-question-nr .growtype-quiz-question-nr-total-slide').text(slideTotalNrFormatted);

    /**
     * Set question attribute to highest dom element
     */
    $('body').attr('data-current-question', window.growtype_quiz_global[quizId]['current_question_nr']);
}
