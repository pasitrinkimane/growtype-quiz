export function radio(currentQuestion) {
    let btnIsValid = true;
    let activeBtns = currentQuestion.find('.b-quiz-question-answer.is-active');

    if (activeBtns.length === 0) {
        btnIsValid = false;
    }

    if (btnIsValid && $('.b-quiz').attr('data-type') === 'scored' && showCorrectAnswersInitially && quizInTestMode) {

        activeBtns.map(function (index, element) {
            if ($(element).attr('data-cor') !== '1') {
                btnIsValid = false;
                $(element).addClass('is-wrong')
            }
        });

        let correctAnswersSelected = true;
        currentQuestion.find('.b-quiz-question-answer[data-cor="1"]').map(function (index, element) {
            if (!$(element).hasClass('is-active')) {
                btnIsValid = false;
                correctAnswersSelected = false;
            }
        });

        if (correctAnswersSelected) {
            btnIsValid = true;
            currentQuestion.find('.b-quiz-question-answer.is-wrong').removeClass('is-active');
        }
    }

    if (!btnIsValid) {
        if (currentQuestion.attr('data-hint')) {
            currentQuestion.find('.b-quiz-hint').fadeIn();
        }

        currentQuestion.find('.b-quiz-question-answers').addClass('anim-wrong-selection');

        setTimeout(function () {
            currentQuestion.find('.b-quiz-question-answers').removeClass('anim-wrong-selection');
        }, 500);
    }

    return btnIsValid;
}
