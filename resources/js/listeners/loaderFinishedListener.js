/**
 * Init listener
 */
import {showNextQuestion} from "../actions/question/showNextQuestion";

document.addEventListener('growtypeQuizLoaderFinished', loaderFinishedListener)

function loaderFinishedListener(params) {
    let quizLoaderWrapper = params['detail']['loader'];
    let redirect = quizLoaderWrapper.attr('data-redirect');
    let redirectUrl = quizLoaderWrapper.attr('data-redirect-url');
    let quizWrapper = quizLoaderWrapper.closest('.growtype-quiz-wrapper');

    quizWrapper.addClass('is-valid');

    /**
     * Show continue button
     */
    if (redirect === 'true' && redirectUrl !== null && redirectUrl.length > 0) {
        window.location.href = redirectUrl;
    } else {
        if (quizWrapper.find('.growtype-quiz-question.is-active').next('.growtype-quiz-question').length > 0) {
            showNextQuestion(quizWrapper.find('.growtype-quiz-question.is-active'));
        } else {
            quizWrapper.find('.btn-continue').fadeIn();
        }
    }
}
