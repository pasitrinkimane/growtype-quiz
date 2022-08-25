import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateQuestionsCounter} from "../../actions/progress/counter/updateQuestionsCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {saveQuizDataEvent} from "../../events/saveQuizData";
import {hideProgressIndicators} from "../../actions/progress/general";
import {showProgressIndicators} from "../../actions/progress/general";
import {evaluateQuizData} from "../../actions/crud/evaluateQuizData";

/**
 * Show next slide
 */
export function showNextQuestion(currentQuestion) {
    let nextFunnel = currentQuestion.find('.b-quiz-question-answer.is-active').attr('data-funnel');

    window.growtype_quiz.current_funnel = nextFunnel;

    if (nextFunnel === undefined) {
        nextFunnel = 'a';
    }

    let nextQuestion = currentQuestion.nextAll('.b-quiz-question[data-funnel="' + nextFunnel + '"]:first');

    window.growtype_quiz.already_visited_questions_keys.push(currentQuestion.attr('data-key'))
    window.growtype_quiz.already_visited_questions_funnels.push(currentQuestion.attr('data-funnel'))

    window.quizLastQuestion = currentQuestion;
    window.growtype_quiz.current_question_nr++;

    showProgressIndicators();

    currentQuestion.removeClass('is-active').not('.is-always-visible').fadeOut(300, function () {
    }).promise().done(function () {

        /**
         * Change next label
         */
        let finishLabel = $('.b-quiz-nav .btn-go-next .e-label').attr('data-label-finish');

        if (window.growtype_quiz.current_question_nr === window.quizQuestionsAmount - 1 && finishLabel.length > 0) {
            $(this).closest('.b-quiz').find('.b-quiz-nav .btn-go-next .e-label').text(finishLabel);
        }

        /**
         * Reset next btn label
         */
        if (window.growtype_quiz.current_question_nr < window.quizQuestionsAmount - 1) {
            let nextLabel = $('.b-quiz-nav .btn-go-next .e-label').attr('data-label');

            let nextQuestionTitle = nextQuestion.nextAll('.b-quiz-question:first').attr('data-question-title');

            if (nextQuestion.nextAll('.b-quiz-question[data-funnel="' + nextFunnel + '"]:first').length > 0) {
                nextQuestionTitle = nextQuestion.nextAll('.b-quiz-question[data-funnel="' + nextFunnel + '"]:first').attr('data-question-title');
            }

            if ($('.b-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle.length > 0) {
                nextLabel = nextQuestionTitle;
            }

            $('.b-quiz-nav .btn-go-next .e-label').attr('data-label', nextLabel).text(nextLabel);
        }

        $('.quiz-wrapper').attr('data-current-question-type', nextQuestion.attr('data-question-type'))

        if (nextQuestion.length > 0) {
            updateQuestionsCounter();
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
