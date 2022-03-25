export function hideProgressIndicators() {
    $('.b-quiz-progressbar').hide();
    $('.b-quiz-header').hide();
    $('.b-quiz-footer').hide();

    if($('.b-quiz-timer').length > 0){
        $('.b-quiz-timer').hide();
        clearInterval(window.countDownTimer);
    }
}

export function showProgressIndicators() {
    $('.b-quiz-progressbar').show();
    $('.b-quiz-header').show();
    $('.b-quiz-footer').show();

    if($('.b-quiz-timer').length > 0){
        $('.b-quiz-timer').show();
    }
}
