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

        $('.btn-go-next').show();

        $('.quiz-wrapper').attr('data-current-question-type', previousQuestion.attr('data-question-type'))

        let nextLabel = $('.b-quiz-nav .btn-go-next .e-label').attr('data-label');

        let nextQuestionTitle = currentQuestion.attr('data-question-title');

        if ($('.b-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle.length > 0) {
            nextLabel = nextQuestionTitle;
        }

        /**
         * Reset next label
         */
        if (window.quizCurrentQuestionNr < window.quizQuestionsAmount - 1) {
            $(this).closest('.b-quiz').find('.b-quiz-nav .btn-go-next .e-label').text(nextLabel);
        }

        previousQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
            window.quizBackBtnWasClicked = false;
        });

        window.scrollTo(0, 0);
    });
}
