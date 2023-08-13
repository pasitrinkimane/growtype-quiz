import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateQuestionsCounter} from "../../actions/progress/counter/updateQuestionsCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {saveQuizDataEvent} from "../../events/saveQuizDataEvent";
import {showProgressIndicators} from "../../actions/progress/general";
import {updateQuizComponents} from "./updateQuizComponents";
import {showSuccessQuestionEvent} from "../../events/showSuccessQuestionEvent";
import {showNextQuestionEvent} from "../../events/showNextQuestionEvent";

/**
 * Show next slide
 */
export function showNextQuestion(currentQuestion) {
    let nextFunnel = currentQuestion.find('.growtype-quiz-question-answer.is-active').attr('data-funnel');
    let submitDelay = 0;

    window.growtype_quiz_global.current_funnel = nextFunnel;

    if (nextFunnel === undefined) {
        nextFunnel = 'a';
    }

    let nextQuestion = currentQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first');

    window.growtype_quiz_global.already_visited_questions_keys.push(currentQuestion.attr('data-key'))
    window.growtype_quiz_global.already_visited_questions_funnels.push(currentQuestion.attr('data-funnel'))

    window.quizLastQuestion = currentQuestion;
    window.growtype_quiz_global.current_question_nr++;

    showProgressIndicators();

    /**
     * Show correct answer
     */
    if (growtype_quiz_local.show_correct_answer && growtype_quiz_local.correct_answer_trigger === 'after_submit') {
        submitDelay = 1000;
        currentQuestion.find('.growtype-quiz-question-answer').map(function (index, element) {
            if ($(element).attr('data-cor') !== '1') {
                $(element).addClass('is-wrong')
            } else {
                $(element).addClass('is-correct')
            }
        });
    }

    /**
     * Show new question
     */
    currentQuestion.delay(submitDelay).removeClass('is-active').not('.is-always-visible').fadeOut(300, function () {
    }).promise().done(function () {

        /**
         * Check if success page event was fired and quiz is finished
         */
        if (window.growtype_quiz_global.is_finished) {
            return;
        }

        /**
         * Change next label
         */
        let finishLabel = $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label-finish');

        if (window.growtype_quiz_global.current_question_nr === window.quizQuestionsAmount - 1 && finishLabel.length > 0) {
            $(this).closest('.growtype-quiz').find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(finishLabel);
        }

        /**
         * Reset next btn label
         */
        if (window.growtype_quiz_global.current_question_nr < window.quizQuestionsAmount - 1) {
            let nextLabel = $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label');

            let nextQuestionTitle = nextQuestion.nextAll('.growtype-quiz-question:first').attr('data-question-title');

            if (nextQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first').length > 0) {
                nextQuestionTitle = nextQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first').attr('data-question-title');
            }

            if ($('.growtype-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle.length > 0) {
                nextLabel = nextQuestionTitle;
            }

            $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label', nextLabel).text(nextLabel);
        }

        if (nextQuestion.length > 0) {
            updateQuestionsCounter();
            updateProgressCounter();
            updateProgressBar();
            nextQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
                $('.growtype-quiz-nav .btn').attr('disabled', false);
            });
            window.scrollTo(0, 0);
        }

        updateQuizComponents(nextQuestion);

        if (nextQuestion.length === 0 || nextQuestion.attr('data-question-type') === 'success') {
            $('.growtype-quiz-btn-go-back').attr('disabled', false);
            $('.growtype-quiz-btn-go-next').hide();
            document.dispatchEvent(saveQuizDataEvent());
            document.dispatchEvent(showSuccessQuestionEvent());
        } else {
            document.dispatchEvent(showNextQuestionEvent({
                currentQuestion: currentQuestion,
                nextQuestion: nextQuestion,
            }));
        }
    });
}
