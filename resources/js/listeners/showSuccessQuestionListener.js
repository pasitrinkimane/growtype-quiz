import {hideProgressIndicators} from "../actions/progress/general";
import {evaluateQuizData} from "../actions/crud/evaluateQuizData";

/**
 * Show success question
 */
document.addEventListener('growtypeQuizShowSuccessQuestion', showSuccessQuestionListener)

function showSuccessQuestionListener() {
    hideProgressIndicators();
    evaluateQuizData();

    $('.growtype-quiz-question').hide().promise().done(function () {
        $('body').attr('data-current-question-type', 'success')
        $('.growtype-quiz-question[data-question-type="success"]').fadeIn();
    });
}
