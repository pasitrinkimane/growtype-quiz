export function open(currentQuestion) {
    let isValid = true;
    let textarea = currentQuestion.find('textarea').val();

    if (textarea.length === 0) {
        isValid = false;
    }

    if (!isValid) {
        currentQuestion.find('.growtype-quiz-question-answers-wrapper').addClass('anim-wrong-selection');
        setTimeout(function () {
            currentQuestion.find('.growtype-quiz-question-answers-wrapper').removeClass('anim-wrong-selection');
        }, 500);
    }

    return isValid;
}
