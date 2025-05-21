export function hideProgressIndicators(quizWrapper) {
    let quizId = quizWrapper.attr('id');
    quizWrapper.find('.growtype-quiz-progressbar').hide();
    quizWrapper.find('.growtype-quiz-header').hide();
    quizWrapper.find('.growtype-quiz-nav').hide();

    if (quizWrapper.find('.growtype-quiz-timer').length > 0) {
        quizWrapper.find('.growtype-quiz-timer').hide();
        clearInterval(window.growtype_quiz_global[quizId]['countdown_timer']);
    }
}

export function showProgressIndicators(quizWrapper) {
    quizWrapper.find('.growtype-quiz-header').show();
    quizWrapper.find('.growtype-quiz-nav').show();

    if (quizWrapper.find('.growtype-quiz-timer').length > 0) {
        quizWrapper.find('.growtype-quiz-timer').show();
    }
}

export function disabledValueIsIncluded(quizWrapper, disabledIf) {
    let quizId = quizWrapper.attr('id');
    let key = disabledIf.split(":")[0];
    let values = disabledIf.split(":")[1].split("|");

    if (window.growtype_quiz_data[quizId]['answers'][key] !== undefined) {
        let includes = false;
        values.map(function (value) {
            if (window.growtype_quiz_data[quizId]['answers'][key].includes(value)) {
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
