/**
 * Init listener
 */
import {showNextQuestion} from "../actions/question/showNextQuestion";

document.addEventListener('growtypeQuizLoaderFinished', loaderFinishedListener)

function loaderFinishedListener() {
    let redirect = $('.growtype-quiz-loader-wrapper:visible').attr('data-redirect');
    let redirectUrl = $('.growtype-quiz-loader-wrapper:visible').attr('data-redirect-url');

    $('.growtype-quiz-wrapper').addClass('is-valid');

    if (redirect === 'true' && redirectUrl !== null && redirectUrl.length > 0) {
        window.location.href = redirectUrl;
    } else {
        showNextQuestion($('.growtype-quiz-question.is-active'));
    }
}
