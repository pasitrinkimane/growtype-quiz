import {hideProgressIndicators} from "../progress/general";

/**
 * Update attributes of quiz components
 */
export function updateQuizComponents(question) {
    /**
     * Hide progress indicators
     */
    if (question.attr('data-hide-footer') === 'true') {
        hideProgressIndicators();
    }

    /**
     * Back btn
     */
    $('.growtype-quiz-nav .growtype-quiz-btn-go-back').show();

    if (question.attr('data-hide-back-button') === 'true') {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back').hide();
    }

    /**
     * Next btn
     */
    if (question.attr('data-hide-next-button') === 'true') {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-next').hide();
    } else {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-next').show();
    }

    /**
     * Progress bar
     */
    if (question.attr('data-hide-progressbar') === 'true') {
        $('.growtype-quiz-progressbar').fadeOut(200);
    } else {
        $('.growtype-quiz-progressbar').fadeIn();
    }

    /**
     * Set question type attribute to highest dom element
     */
    $('body')
        .attr('data-current-question-type', question.attr('data-question-type'))
        .attr('data-current-answer-type', question.attr('data-answer-type'))
}
