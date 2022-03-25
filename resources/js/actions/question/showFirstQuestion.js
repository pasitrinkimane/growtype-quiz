import {showProgressIndicators} from "../../actions/progress/general";
import {updateProgressCounter} from "../progress/counter/updateProgressCounter";

/**
 * Show last slide
 */
export function showFirstQuestion() {
    let firstQuestion = $('.b-quiz-question.first-question');

    $('.b-quiz-question').removeClass('is-active').fadeOut().promise().done(function () {
        window.quizCurrentQuestionNr = 1;
        updateProgressCounter();
        firstQuestion.addClass('is-active').fadeIn();
        showProgressIndicators();
    });
}
