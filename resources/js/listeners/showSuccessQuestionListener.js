import {hideProgressIndicators} from "../actions/progress/general";
import {evaluateQuizData} from "../actions/crud/evaluateQuizData";
import {restartQuizTrigger} from "../components/restartQuizTrigger";
import {loader} from "../actions/progress/loader/loader";

/**
 * Show success question
 */
document.addEventListener('growtypeQuizShowSuccessQuestion', showSuccessQuestionListener)

function showSuccessQuestionListener(params) {
    /**
     * Check if success page event was fired and quiz is finished
     */
    // window.growtype_quiz_global.is_finished = true;

    // hideProgressIndicators();
    evaluateQuizData();

    let question = $('.growtype-quiz-question');

    if (params.detail && params.detail.currentQuestion) {
        question = $(params.detail.currentQuestion);
    }

    question
        .removeClass('is-active')
        .hide()
        .promise()
        .done(function () {
            $('body').attr('data-current-question-type', 'success')
            $('.growtype-quiz-question[data-question-type="success"]').fadeIn().addClass('is-active');

            restartQuizTrigger();
        });
}
