import {saveQuizDataEvent} from "../../events/saveQuizData";

export function collectQuizData(currentQuestion) {
    let answers = saveQuizDataEvent().answers;
    let correctlyAnswered = saveQuizDataEvent().correctlyAnswered;
    let currentQuestionKey = currentQuestion.attr('data-key');
    let currentQuestionType = currentQuestion.attr('data-question-type');

    /**
     * Collect answers
     */
    answers[currentQuestionKey] = [];

    if (currentQuestionType === 'open') {
        answers[currentQuestionKey].push(currentQuestion.find('textarea').val())
    } else {
        currentQuestion.find('.b-quiz-question-answer.is-active').map(function (index, element) {
            answers[currentQuestionKey].push($(this).attr('data-value'))
        });
    }

    /**
     * Collect correctly answered
     */
    if ($('.b-quiz[data-type="scored"]').length > 0 && currentQuestionType !== 'open') {
        let correctAnswer = true;
        currentQuestion.find('.b-quiz-question-answer').map(function (index, element) {
            if ($(this).hasClass('is-active') && $(this).attr('data-cor').length === 0) {
                correctAnswer = false;
            }
        });

        correctlyAnswered[currentQuestion.attr('data-key')] = [];
        correctlyAnswered[currentQuestion.attr('data-key')].push(correctAnswer);
    }

    saveQuizDataEvent().answers = answers;
}
