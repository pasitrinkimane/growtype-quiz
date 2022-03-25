import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";

/**
 * Show next slide
 */
export function showPreviousQuestion() {
    let currentQuestion = $('.b-quiz-question.is-active');
    var previousQuestion = currentQuestion.prevAll(".b-quiz-question[data-key='" + window.quizQuestionsKeysAlreadyVisited.slice(-1)[0] + "'][data-funnel='" + window.quizQuestionsFunnelsAlreadyVisited.slice(-1)[0] + "']:first");

    window.quizQuestionsKeysAlreadyVisited.splice(-1)
    window.quizQuestionsFunnelsAlreadyVisited.splice(-1)

    window.quizLastQuestion = currentQuestion;
    window.quizCurrentQuestionNr--;

    currentQuestion.removeClass('is-active').fadeOut(300, function () {
        updateProgressCounter();
        updateProgressBar();

        previousQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
            window.quizBackBtnWasClicked = false;

        });
        window.scrollTo(0, 0);
    });
}
