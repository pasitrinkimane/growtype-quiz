import { updateProgressCounter } from "../../actions/progress/counter/updateProgressCounter.js";
import { updateProgressBar } from "../../actions/progress/bar/updateProgressBar";
import { updateQuestionsCounter } from "../progress/counter/updateQuestionsCounter";
import { getQuizData } from "../../helpers/data";
import { updateQuizComponents } from "./updateQuizComponents";
import { showQuestionEvent } from "../../events/showQuestionEvent";
import { quizStepShouldBeSkipped } from "../../helpers/progress";

import { storage } from "../../helpers/storage";

/**
 * Show next slide
 */
export function showPreviousQuestion(quizWrapper) {
    let quizId = quizWrapper.attr('id');
    let currentQuestion = quizWrapper.find('.growtype-quiz-question.is-active');
    let lastVisitedQuestionKey = window.growtype_quiz_global[quizId]['already_visited_questions_keys'].slice(-1)[0];
    let lastVisitedQuestionFunnel = window.growtype_quiz_global[quizId]['already_visited_questions_funnels'].slice(-1)[0];
    let previousQuestion = currentQuestion.prevAll(".growtype-quiz-question[data-key='" + lastVisitedQuestionKey + "'][data-funnel='" + lastVisitedQuestionFunnel + "']:first");

    if (previousQuestion.attr('data-answer-type') === 'multiple' && previousQuestion.attr('data-has-funnel') === 'true') {
        window.growtype_quiz_global[quizId]['additional_questions_amount'] = 0;
        quizWrapper.find('.growtype-quiz-question.is-conditionally-cloned').remove();
        quizWrapper.find('.growtype-quiz-question.is-conditionally-skipped').removeClass('is-conditionally-skipped');
    }

    /**
     * If active class doesn't exist, get last question
     */
    if (currentQuestion.length === 0) {
        previousQuestion = quizWrapper.find('.growtype-quiz-question:last');
    }

    /**
     * Skip loader
     */
    if (previousQuestion.find('.growtype-quiz-loader-wrapper').length > 0) {
        previousQuestion = previousQuestion.prev()
    }

    window.growtype_quiz_global[quizId]['already_visited_questions_keys'].splice(-1)
    window.growtype_quiz_global[quizId]['already_visited_questions_funnels'].splice(-1)

    Object.entries(getQuizData(quizId)['answers']).map(function (answer, key) {
        if (typeof answer[0] === 'string' && answer[0].includes(lastVisitedQuestionKey)) {
            delete getQuizData(quizId)['answers'][answer[0]]
        }
    });

    storage.set('growtype_quiz_answers', JSON.stringify(getQuizData(quizId)['answers']), 'session');

    window.quizLastQuestion = currentQuestion;
    window.growtype_quiz_global[quizId]['current_question_nr'] = previousQuestion.attr('data-question-nr');

    if (quizStepShouldBeSkipped(previousQuestion)) {
        window.growtype_quiz_global[quizId]['current_question_counter_nr']--;
    }

    if (currentQuestion.length === 0) {
        initQuestion(previousQuestion, previousQuestion)
    } else {
        currentQuestion.removeClass('is-active').fadeOut(300, function () {
            quizWrapper.addClass('is-valid');

            window.growtype_quiz_global[quizId]['is_finished'] = false;

            initQuestion(currentQuestion, previousQuestion)
        });
    }
}

/**
 * @param currentQuestion
 * @param previousQuestion
 */
function initQuestion(currentQuestion, previousQuestion) {
    let quizWrapper = currentQuestion.closest('.growtype-quiz-wrapper');
    let quizId = quizWrapper.attr('id');

    updateQuestionsCounter(quizWrapper);
    updateProgressCounter(quizWrapper);
    updateProgressBar(quizWrapper);

    quizWrapper.find('.growtype-quiz-btn-go-next').show().attr('disabled', false);

    let nextLabel = quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label');

    if (previousQuestion.hasClass('first-question')) {
        nextLabel = quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label-start');
    }

    /**
     * Remove skip additional question class
     */
    previousQuestion.find('.growtype-quiz-question-answer.is-active').removeClass('skip-additional-question');

    let nextQuestionTitle = currentQuestion.attr('data-question-title');

    if (quizWrapper.find('.growtype-quiz-nav[data-type="footer"]').attr('data-question-title-nav') === 'true' && nextQuestionTitle && nextQuestionTitle.length > 0) {
        nextLabel = nextQuestionTitle;
    }

    updateQuizComponents(previousQuestion);

    /**
     * Reset next label
     */
    previousQuestion.closest('.growtype-quiz').find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(nextLabel);

    previousQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
        window.growtype_quiz_global[quizId]['quiz_back_btn_was_clicked'] = false;
    });

    // console.log(previousQuestion,'previousQuestion')
    //
    // $([document.documentElement, document.body]).animate({
    //     scrollTop: $(".growtype-quiz").offset().top
    // }, 100);

    /**
     * Show question general event
     */
    document.dispatchEvent(showQuestionEvent({
        currentQuestion: previousQuestion,
        nextQuestion: currentQuestion,
    }));
}
