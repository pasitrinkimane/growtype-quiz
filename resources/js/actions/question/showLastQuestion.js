import {showProgressIndicators} from "../../actions/progress/general";

/**
 * Show last slide
 */
export function showLastQuestion(answers, showAlert = true) {

    if (showAlert) {
        alert('Something went wrong. Please try again.')
    }

    let lastQuestionKey = Object.keys(answers)[Object.keys(answers).length - 1];

    if (lastQuestionKey !== undefined) {
        showProgressIndicators();
        $('.b-quiz-question[data-key="' + lastQuestionKey + '"]').addClass('is-active').fadeIn(300).promise().done(function () {
        });
    }
}
