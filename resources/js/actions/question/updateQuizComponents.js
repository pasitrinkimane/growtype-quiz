import {hideProgressIndicators} from "../progress/general";

/**
 * Update attributes of quiz components
 */
export function updateQuizComponents(question) {
    let quizWrapper = question.closest('.growtype-quiz-wrapper');

    /**
     * Hide progress indicators
     */
    if (question.attr('data-hide-footer') === 'true') {
        hideProgressIndicators(quizWrapper);
    }

    /**
     * Back btn
     */
    if (question.length > 0) {
        if (parseInt($(question).attr('data-question-nr')) === 1 && quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-back').hasClass('hide-initially')) {
            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-back').fadeOut();
        } else {
            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-back').fadeIn();
        }
    }

    if (question.attr('data-hide-back-button') === 'true') {
        quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-back:not(.show-initially)')
            .filter(function () {
                return !$(this).closest('.growtype-quiz-header').length;
            }).hide();
    }

    /**
     * Next btn
     */
    if (question.length > 0) {
        if (question.attr('data-hide-next-button') === 'true') {
            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next').hide();
        } else {
            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next').show();
        }
    }

    /**
     * Progress bar
     */
    if (question.attr('data-hide-progressbar') === 'true') {
        quizWrapper.find('.growtype-quiz-progressbar').fadeOut(200);
    } else {
        quizWrapper.find('.growtype-quiz-progressbar').fadeIn();
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
        quizWrapper.find('.growtype-quiz-nav[data-type="footer"] .growtype-quiz-btn-go-back:not(.show-initially)').hide();
        quizWrapper.find('.growtype-quiz-nav[data-type="footer"] .growtype-quiz-btn-go-next').hide();
    }
}
