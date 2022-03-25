export function open() {
    let btnIsValid = true;
    let currentQuestion = $('.b-quiz-question.is-active');
    let textarea = currentQuestion.find('textarea').val();

    if (textarea.length === 0) {
        btnIsValid = false;
    }

    if (!btnIsValid) {
        currentQuestion.find('.b-quiz-question-answers-wrapper').addClass('anim-shake');
        setTimeout(function () {
            currentQuestion.find('.b-quiz-question-answers-wrapper').removeClass('anim-shake');
        }, 500);
    }

    return btnIsValid;
}
