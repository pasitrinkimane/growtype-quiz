export function hideProgressIndicators() {
    $('.growtype-quiz-progressbar').hide();
    $('.growtype-quiz-header').hide();
    $('.growtype-quiz-nav').hide();

    if($('.growtype-quiz-timer').length > 0){
        $('.growtype-quiz-timer').hide();
        clearInterval(window.countdown_timer);
    }
}

export function showProgressIndicators() {
    $('.growtype-quiz-header').show();
    $('.growtype-quiz-nav').show();

    if($('.growtype-quiz-timer').length > 0){
        $('.growtype-quiz-timer').show();
    }
}
