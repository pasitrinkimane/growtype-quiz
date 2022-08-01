export function open(currentQuestion) {
    let btnIsValid = true;
    let textarea = currentQuestion.find('textarea').val();

    if (textarea.length === 0) {
        btnIsValid = false;
    }

    if (!btnIsValid) {
        currentQuestion.find('.b-quiz-question-answers-wrapper').addClass('anim-wrong-selection');
        setTimeout(function () {
            currentQuestion.find('.b-quiz-question-answers-wrapper').removeClass('anim-wrong-selection');
        }, 500);
    }

    return btnIsValid;
}
