import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {saveQuizDataEvent} from "../../events/saveQuizData";
import {hideProgressIndicators} from "../../actions/progress/general";
import {showProgressIndicators} from "../../actions/progress/general";
import {evaluateQuizData} from "../../actions/crud/evaluateQuizData";

/**
 * Show next slide
 */
export function showNextQuestion() {
    let currentQuestion = $('.b-quiz-question.is-active');
    var nextQuestion = currentQuestion.nextAll('.b-quiz-question:first');

    window.quizQuestionsKeysAlreadyVisited.push(currentQuestion.attr('data-key'))
    window.quizQuestionsFunnelsAlreadyVisited.push(currentQuestion.attr('data-funnel'))

    window.quizLastQuestion = currentQuestion;
    window.quizCurrentQuestionNr++;

    showProgressIndicators();

    currentQuestion.removeClass('is-active').not('.is-always-visible').fadeOut(300, function () {
    }).promise().done(function () {

        /**
         * Change next label
         */
        let finishLabel = $('.b-quiz-nav .btn-go-next .e-label').attr('data-label-finish');

        if (window.quizCurrentQuestionNr === window.quizQuestionsAmount - 1 && finishLabel.length > 0) {
            $(this).closest('.b-quiz').find('.b-quiz-nav .btn-go-next .e-label').text(finishLabel);
        }

        /**
         * Reset next btn label
         */
        if (window.quizCurrentQuestionNr < window.quizQuestionsAmount - 1) {
            let nextLabel = $('.b-quiz-nav .btn-go-next .e-label').attr('data-label');
            let nextQuestionTitle = nextQuestion.nextAll('.b-quiz-question:first').attr('data-question-title');

            if ($('.b-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle.length > 0) {
                nextLabel = nextQuestionTitle;
            }

            $('.b-quiz-nav .btn-go-next .e-label').attr('data-label', nextLabel).text(nextLabel);
        }

        $('.quiz-wrapper').attr('data-current-question-type', nextQuestion.attr('data-question-type'))

        if (nextQuestion.length > 0) {
            updateProgressCounter();
            updateProgressBar();
            nextQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
                $('.b-quiz-nav .btn').attr('disabled', false);
            });

            window.scrollTo(0, 0);
        }

        if (nextQuestion.attr('data-hide-footer') === 'true') {
            hideProgressIndicators();
        }

        if (nextQuestion.length === 0 || nextQuestion.attr('data-question-type') === 'success') {
            $('.btn-go-next').hide();
            document.dispatchEvent(saveQuizDataEvent());
            evaluateQuizData();
        }
    });
}
