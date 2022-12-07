import {hideProgressIndicators} from "../progress/general";

/**
 * Update attributes of quiz components
 */
export function updateQuizComponents(question) {
    if (question.attr('data-hide-footer') === 'true') {
        hideProgressIndicators();
    }

    if (question.attr('data-hide-back-button') === 'true') {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back').hide();
    } else {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back').show();
    }

    $('.growtype-quiz-wrapper').attr('data-current-question-type', question.attr('data-question-type'))
}
