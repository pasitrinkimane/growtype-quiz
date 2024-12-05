export function radio(currentQuestion) {
    let isValid = true;
    let activeBtns = currentQuestion.find('.growtype-quiz-question-answer.is-active');

    if (activeBtns.length === 0) {
        isValid = false;
    }

    if (isValid && $('.growtype-quiz-wrapper').attr('data-quiz-type') === 'scored' && $('.growtype-quiz-wrapper').attr('data-show-correct-answer') && $('.growtype-quiz-wrapper').attr('data-correct-answers-trigger') === 'on_submit') {
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
            currentQuestion.find('.growtype-quiz-question-answer').removeClass('is-wrong');
            currentQuestion.find('.growtype-quiz-question-answer[data-cor="1"]').addClass('is-correct');
            currentQuestion.find('.growtype-quiz-question-answer.is-wrong').removeClass('is-active');
        }
    }

    if (!isValid) {
        if (currentQuestion.attr('data-hint')) {
            currentQuestion.find('.growtype-quiz-hint').fadeIn();
        }
    }

    return isValid;
}
