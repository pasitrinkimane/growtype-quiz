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

    currentQuestion.removeClass('is-active').fadeOut(300, function () {
        /**
         * Reset next btn label
         */
        $('.b-quiz-footer .btn-go-next .e-label').text($('.b-quiz-footer .btn-next .e-label').attr('data-label'));

        if (nextQuestion.length > 0) {
            updateProgressCounter();
            updateProgressBar();
            nextQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
                $('.b-quiz-footer .btn').attr('disabled', false);
                if (window.quizCurrentQuestionNr === window.quizQuestionsAmount && window.quizLastQuestionNextLabel.length > 0) {
                    $(this).closest('.b-quiz').find('.b-quiz-footer .btn-go-next .e-label').text(window.quizLastQuestionNextLabel);
                }
            });
            window.scrollTo(0, 0);
        }

        if (nextQuestion.length === 0 || nextQuestion.attr('data-key') === 'success') {
            hideProgressIndicators();
            document.dispatchEvent(saveQuizDataEvent());
        }

        if (nextQuestion.attr('data-key') === 'success') {
            evaluateQuizData();
        }
    });
}
