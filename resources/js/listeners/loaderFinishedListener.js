/**
 * Init listener
 */
document.addEventListener('loaderFinished', loaderFinishedListener)

function loaderFinishedListener() {
    let redirect = $('.growtype-quiz-loader-wrapper').attr('data-redirect');

    $('.growtype-quiz-wrapper').addClass('is-valid');

    if (redirect === 'true') {
        window.location.href = $('.growtype-quiz-loader-wrapper').attr('data-redirect-url');
    }
}
