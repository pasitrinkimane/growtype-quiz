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
    if (question.length > 0) {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back').show();
    }

    if (question.attr('data-hide-back-button') === 'true') {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back:not(.show-initially)').hide();
    }

    /**
     * Next btn
     */
    if (question.length > 0) {
        if (question.attr('data-hide-next-button') === 'true') {
            $('.growtype-quiz-nav .growtype-quiz-btn-go-next').hide();
        } else {
            $('.growtype-quiz-nav .growtype-quiz-btn-go-next').show();
        }
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
        .attr('data-current-question-style', question.attr('data-question-style'))
        .attr('data-current-answer-type', question.attr('data-answer-type'))

    /**
     * Hide next btn on single instant question
     */
    if ($(question).attr('data-answer-type') === 'single_instant') {
        $('.growtype-quiz-nav .growtype-quiz-btn-go-back:not(.show-initially)').hide();
        $('.growtype-quiz-nav .growtype-quiz-btn-go-next').hide();
    }
}
