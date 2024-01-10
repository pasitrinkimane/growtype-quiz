export function hideProgressIndicators() {
    $('.growtype-quiz-progressbar').hide();
    $('.growtype-quiz-header').hide();
    $('.growtype-quiz-nav').hide();

    if ($('.growtype-quiz-timer').length > 0) {
        $('.growtype-quiz-timer').hide();
        clearInterval(window.countdown_timer);
    }
}

export function showProgressIndicators() {
    $('.growtype-quiz-header').show();
    $('.growtype-quiz-nav').show();

    if ($('.growtype-quiz-timer').length > 0) {
        $('.growtype-quiz-timer').show();
    }
}

export function disabledValueIsIncluded(disabledIf) {
    let key = disabledIf.split(":")[0];
    let values = disabledIf.split(":")[1].split("|");

    if (window.growtype_quiz_data.answers[key] !== undefined) {
        let includes = false;
        values.map(function (value) {
            if (window.growtype_quiz_data.answers[key].includes(value)) {
                includes = true;
            }
        })

        if (includes) {
            return true;
        }
    } else {
        return false;
    }

    return false;
}
