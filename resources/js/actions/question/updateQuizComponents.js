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
    if (question.attr('data-hide-back-button') === 'true') {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back').hide();
    } else {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back').show();
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
     * Set question type
     */
    $('.growtype-quiz-wrapper').attr('data-current-question-type', question.attr('data-question-type'))
}
