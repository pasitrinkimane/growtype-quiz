export function general(currentQuestion) {
    let isValid = true;
    let answers = currentQuestion.find('.growtype-quiz-question-answer');
    let answer = currentQuestion.find('.growtype-quiz-question-answer.is-active');

    if (answers.length > 0 && answer.length === 0) {
        isValid = false;
    }

    if (!isValid) {
        currentQuestion.find('.growtype-quiz-question-answers').addClass('anim-wrong-selection');
        setTimeout(function () {
            currentQuestion.find('.growtype-quiz-question-answers').removeClass('anim-wrong-selection');
        }, 500);
    }

    return isValid;
}
