import {hideProgressIndicators} from "../actions/progress/general";
import {evaluateQuizData} from "../actions/crud/evaluateQuizData";
import {restartQuizTrigger} from "../components/restartQuizTrigger";

/**
 * Show success question
 */
document.addEventListener('growtypeQuizShowSuccessQuestion', showSuccessQuestionListener)

function showSuccessQuestionListener() {
    /**
     * Check if success page event was fired and quiz is finished
     */
    window.growtype_quiz_global.is_finished = true;

    hideProgressIndicators();
    evaluateQuizData();

    $('.growtype-quiz-question')
        .removeClass('is-active')
        .hide()
        .promise()
        .done(function () {
            $('body').attr('data-current-question-type', 'success')
            $('.growtype-quiz-question[data-question-type="success"]').fadeIn();

            restartQuizTrigger();
        });
}
