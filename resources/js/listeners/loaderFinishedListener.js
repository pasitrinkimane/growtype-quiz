/**
 * Init listener
 */
import {showNextQuestion} from "../actions/question/showNextQuestion";

document.addEventListener('growtypeQuizLoaderFinished', loaderFinishedListener)

function loaderFinishedListener() {
    let quizWrapper = $('.growtype-quiz-loader-wrapper:visible');
    let redirect = quizWrapper.attr('data-redirect');
    let redirectUrl = quizWrapper.attr('data-redirect-url');

    $('.growtype-quiz-wrapper').addClass('is-valid');

    /**
     * Show continue button
     */
    if (redirect === 'true' && redirectUrl !== null && redirectUrl.length > 0) {
        window.location.href = redirectUrl;
    } else {
        if ($('.growtype-quiz-question.is-active').next('.growtype-quiz-question').length > 0) {
            showNextQuestion($('.growtype-quiz-question.is-active'));
        } else {
            quizWrapper.find('.btn-continue').fadeIn();
        }
    }
}
