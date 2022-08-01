export function hideProgressIndicators() {
    $('.b-quiz-progressbar').hide();
    $('.b-quiz-header').hide();
    $('.b-quiz-nav').hide();

    if($('.b-quiz-timer').length > 0){
        $('.b-quiz-timer').hide();
        clearInterval(window.countDownTimer);
    }
}

export function showProgressIndicators() {
    $('.b-quiz-progressbar').show();
    $('.b-quiz-header').show();
    $('.b-quiz-nav').show();

    if($('.b-quiz-timer').length > 0){
        $('.b-quiz-timer').show();
    }
}
