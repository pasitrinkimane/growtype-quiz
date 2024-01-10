import {saveQuizDataEvent} from "../../events/saveQuizDataEvent";

export function collectQuizData(currentQuestion) {
    let answers = saveQuizDataEvent().answers;
    let correctlyAnswered = saveQuizDataEvent().correctlyAnswered;
    let currentQuestionKey = currentQuestion.attr('data-key');
    let currentQuestionType = currentQuestion.attr('data-question-type');

    /**
     * Skip info questions
     */
    if (currentQuestionType === 'info') {
        return;
    }

    /**
     * Init answers array
     */
    answers[currentQuestionKey] = [];

    /**
     * Collect answers
     */
    if (currentQuestionType === 'open') {
        answers[currentQuestionKey].push(currentQuestion.find('textarea').val())
    } else {
        currentQuestion.find('.growtype-quiz-question-answer.is-active').map(function (index, element) {
            answers[currentQuestionKey].push($(this).attr('data-value'))
        });
    }

    /**
     * Collect correct answers
     */
    if ($('.growtype-quiz-wrapper[data-quiz-type="scored"]').length > 0 && currentQuestionType !== 'open') {
        let correctAnswer = true;
        currentQuestion.find('.growtype-quiz-question-answer').map(function (index, element) {
            if ($(this).hasClass('is-active') && $(this).attr('data-cor').length === 0) {
                correctAnswer = false;
            }
        });

        if (!correctlyAnswered[currentQuestionKey]) {
            correctlyAnswered[currentQuestionKey] = [];
        }

        correctlyAnswered[currentQuestionKey].push(correctAnswer);
    }

    /**
     * Collect inputs data
     */
    if (currentQuestion.find('input:visible,textarea:visible').length > 0) {
        let formData = new FormData();
        currentQuestion.find('input:visible,textarea:visible').each(function (index, element) {
            if ($(element).attr('type') === 'file') {
                formData.append($(element).attr('name') + '-' + currentQuestionKey + '-' + index, $(element)[0].files[0]);
                window.growtype_quiz_global.files = formData
            } else {
                answers[currentQuestionKey].push({
                    name: $(element).attr('name'),
                    value: $(element).val()
                });
            }
        })
    }

    let keyExists = false;
    Object.entries(answers).map(function (element) {
        if (keyExists) {
            delete answers[element[0]];
        }

        if (element[0] === currentQuestionKey) {
            keyExists = true;
        }
    })

    /**
     * Save unit system
     */
    if (currentQuestion.find('.unitsystem-selector-item.is-active:visible').length > 0) {
        answers['unit_system'] = currentQuestion.find('.unitsystem-selector-item.is-active').attr('data-type');
    }

    sessionStorage.setItem('growtype_quiz_answers', JSON.stringify(answers));

    saveQuizDataEvent().answers = answers;
}
