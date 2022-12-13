export function radio(currentQuestion) {
    let isValid = true;
    let activeBtns = currentQuestion.find('.growtype-quiz-question-answer.is-active');

    if (activeBtns.length === 0) {
        isValid = false;
    }

    if (isValid && $('.growtype-quiz-wrapper').attr('data-quiz-type') === 'scored' && showCorrectAnswersInitially && quizInTestMode) {
        activeBtns.map(function (index, element) {
            if ($(element).attr('data-cor') !== '1') {
                isValid = false;
                $(element).addClass('is-wrong')
            }
        });

        let correctAnswersSelected = true;
        currentQuestion.find('.growtype-quiz-question-answer[data-cor="1"]').map(function (index, element) {
            if (!$(element).hasClass('is-active')) {
                isValid = false;
                correctAnswersSelected = false;
            }
        });

        if (correctAnswersSelected) {
            isValid = true;
            currentQuestion.find('.growtype-quiz-question-answer.is-wrong').removeClass('is-active');
        }
    }

    if (!isValid) {
        if (currentQuestion.attr('data-hint')) {
            currentQuestion.find('.growtype-quiz-hint').fadeIn();
        }

        currentQuestion.find('.growtype-quiz-question-answers').addClass('anim-wrong-selection');

        setTimeout(function () {
            currentQuestion.find('.growtype-quiz-question-answers').removeClass('anim-wrong-selection');
        }, 500);
    }

    return isValid;
}
