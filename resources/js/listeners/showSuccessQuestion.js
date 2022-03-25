import {hideProgressIndicators} from "../actions/progress/general";

/**
 * Show success question
 */

document.addEventListener('showSuccessQuestion', showSuccessQuestion)

function showSuccessQuestion() {
    hideProgressIndicators();
    $('.b-quiz-question').hide().promise().done(function (){
        $('.b-quiz-question[data-key="success"]').fadeIn();
    });
}
