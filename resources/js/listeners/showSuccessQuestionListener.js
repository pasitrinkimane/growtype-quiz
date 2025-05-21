import {evaluateQuizData} from "../actions/crud/evaluateQuizData";
import {restartQuizTrigger} from "../components/restartQuizTrigger";

/**
 * Show success question
 */
document.addEventListener('growtypeQuizShowSuccessQuestion', showSuccessQuestionListener)

function showSuccessQuestionListener(params) {
    let quizWrapper = $(params['detail']['currentQuestion']).closest('.growtype-quiz-wrapper');
    let quizId = quizWrapper.attr('id');

    /**
     * Check if success page event was fired and quiz is finished
     */
    evaluateQuizData(quizWrapper);

    let question = quizWrapper.find('.growtype-quiz-question');

    if (params.detail && params.detail.currentQuestion) {
        question = $(params.detail.currentQuestion);
    }

    question
        .removeClass('is-active')
        .hide()
        .promise()
        .done(function () {
            $('body').attr('data-current-question-type', 'success')
            quizWrapper.find('.growtype-quiz-question[data-question-type="success"]').fadeIn().addClass('is-active');

            restartQuizTrigger(quizWrapper);
        });
}
