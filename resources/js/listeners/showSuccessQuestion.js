import {hideProgressIndicators} from "../actions/progress/general";

/**
 * Show success question
 */
document.addEventListener('growtypeQuizShowSuccessQuestion', showSuccessQuestion)

function showSuccessQuestion() {
    hideProgressIndicators();
    $('.growtype-quiz-question').hide().promise().done(function () {
        $('body').attr('data-current-question-type', 'success')
        $('.growtype-quiz-question[data-question-type="success"]').fadeIn();
    });
}
