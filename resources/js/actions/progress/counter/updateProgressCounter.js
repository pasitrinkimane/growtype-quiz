let slideNr = 1;

/**
 * Show current slide
 */
export function updateProgressCounter() {
    let slideTotalNrFormatted = window.quizQuestionsAmount < 10 ? '0' + window.quizQuestionsAmount : window.quizQuestionsAmount;
    let slideNrFormatted = window.growtype_quiz_global.current_question_counter_nr < 10 ? '0' + window.growtype_quiz_global.current_question_counter_nr : window.growtype_quiz_global.current_question_counter_nr;

    if ($('.growtype-quiz-question-nr').attr('data-counter-style') === 'steps' || $('.growtype-quiz-question-nr').attr('data-counter-style') === 'outof') {
        slideTotalNrFormatted = window.quizQuestionsAmount;
        slideNrFormatted = window.growtype_quiz_global.current_question_counter_nr;
    }

    if ($('.growtype-quiz-question-nr').attr('data-counter-style') === 'answered_only') {
        slideTotalNrFormatted = window.quizQuestionsAmount;
        slideNrFormatted = window.growtype_quiz_global.current_question_counter_nr - 1;
    }

    $('.growtype-quiz-question-nr .growtype-quiz-question-nr-current-slide').text(slideNrFormatted);
    $('.growtype-quiz-question-nr .growtype-quiz-question-nr-total-slide').text(slideTotalNrFormatted);

    /**
     * Set question attribute to highest dom element
     */
    $('body').attr('data-current-question', window.growtype_quiz_global.current_question_nr);
}
