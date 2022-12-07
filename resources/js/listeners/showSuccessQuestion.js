import {hideProgressIndicators} from "../actions/progress/general";

/**
 * Show success question
 */

document.addEventListener('showSuccessQuestion', showSuccessQuestion)

function showSuccessQuestion() {
    hideProgressIndicators();
    $('.growtype-quiz-question').hide().promise().done(function (){
        $('.growtype-quiz-question[data-question-type="success"]').fadeIn();
    });
}
