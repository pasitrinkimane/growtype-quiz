export function radio(currentQuestion) {
    let isValid = true;
    let activeBtns = currentQuestion.find('.growtype-quiz-question-answer.is-active');

    if (activeBtns.length === 0) {
        isValid = false;
    }

    if (isValid && $('.growtype-quiz-wrapper').attr('data-quiz-type') === 'scored' && growtype_quiz_local.show_correct_answer && growtype_quiz_local.correct_answer_trigger === 'on_submit') {
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
