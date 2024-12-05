import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {updateQuestionsCounter} from "../progress/counter/updateQuestionsCounter";
import {getQuizData} from "../../helpers/getQuizData";
import {updateQuizComponents} from "./updateQuizComponents";
import {showQuestionEvent} from "../../events/showQuestionEvent";

/**
 * Show next slide
 */
export function showPreviousQuestion() {
    let currentQuestion = $('.growtype-quiz-question.is-active');
    let lastVisitedQuestionKey = window.growtype_quiz_global.already_visited_questions_keys.slice(-1)[0];
    let lastVisitedQuestionFunnel = window.growtype_quiz_global.already_visited_questions_funnels.slice(-1)[0];

    var previousQuestion = currentQuestion.prevAll(".growtype-quiz-question[data-key='" + lastVisitedQuestionKey + "'][data-funnel='" + lastVisitedQuestionFunnel + "']:first");

    if (previousQuestion.attr('data-answer-type') === 'multiple' && previousQuestion.attr('data-has-funnel') === 'true') {
        window.growtype_quiz_global.additional_questions_amount = 0;
        $('.growtype-quiz-question.is-conditionally-cloned').remove();
        $('.growtype-quiz-question.is-conditionally-skipped').removeClass('is-conditionally-skipped');
    }

    /**
     * If active class doesn't exist, get last question
     */
    if (currentQuestion.length === 0) {
        previousQuestion = $('.growtype-quiz-question:last');
    }

    /**
     * Skip loader
     */
    if (previousQuestion.find('.growtype-quiz-loader-wrapper').length > 0) {
        previousQuestion = previousQuestion.prev()
    }

    window.growtype_quiz_global.already_visited_questions_keys.splice(-1)
    window.growtype_quiz_global.already_visited_questions_funnels.splice(-1)

    Object.entries(getQuizData().answers).map(function (answer, key) {
        if (answer[0].includes(lastVisitedQuestionKey)) {
            delete getQuizData().answers[answer[0]]
        }
    });

    sessionStorage.setItem('growtype_quiz_answers', JSON.stringify(getQuizData().answers));

    window.quizLastQuestion = currentQuestion;
    window.growtype_quiz_global.current_question_nr = previousQuestion.attr('data-question-nr');

    if (previousQuestion.attr('data-question-type') !== 'info') {
        window.growtype_quiz_global.current_question_counter_nr--;
    }

    if (currentQuestion.length === 0) {
        initQuestion(currentQuestion, previousQuestion)
    } else {
        currentQuestion.removeClass('is-active').fadeOut(300, function () {
            $('.growtype-quiz-wrapper').addClass('is-valid');

            window.growtype_quiz_global.is_finished = false;

            initQuestion(currentQuestion, previousQuestion)
        });
    }
}

/**
 * @param currentQuestion
 * @param previousQuestion
 */
function initQuestion(currentQuestion, previousQuestion) {
    updateQuestionsCounter();
    updateProgressCounter();
    updateProgressBar();

    $('.growtype-quiz-btn-go-next').show().attr('disabled', false);

    let nextLabel = $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label');

    if (previousQuestion.hasClass('first-question')) {
        nextLabel = $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label-start');
    }

    /**
     * Remove skip additional question class
     */
    previousQuestion.find('.growtype-quiz-question-answer.is-active').removeClass('skip-additional-question');

    let nextQuestionTitle = currentQuestion.attr('data-question-title');

    if ($('.growtype-quiz-nav[data-type="footer"]').attr('data-question-title-nav') === 'true' && nextQuestionTitle && nextQuestionTitle.length > 0) {
        nextLabel = nextQuestionTitle;
    }

    updateQuizComponents(previousQuestion);

    /**
     * Reset next label
     */
    previousQuestion.closest('.growtype-quiz').find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(nextLabel);

    previousQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
        window.quizBackBtnWasClicked = false;
    });

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
