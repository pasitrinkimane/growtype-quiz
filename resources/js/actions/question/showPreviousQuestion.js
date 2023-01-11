import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {updateQuestionsCounter} from "../progress/counter/updateQuestionsCounter";
import {saveQuizDataEvent} from "../../events/saveQuizDataEvent";
import {updateQuizComponents} from "./updateQuizComponents";

/**
 * Show next slide
 */
export function showPreviousQuestion() {
    let currentQuestion = $('.growtype-quiz-question.is-active');
    let lastVisitedQuestionKey = window.growtype_quiz_global.already_visited_questions_keys.slice(-1)[0];
    let lastVisitedQuestionFunnel = window.growtype_quiz_global.already_visited_questions_funnels.slice(-1)[0];

    var previousQuestion = currentQuestion.prevAll(".growtype-quiz-question[data-key='" + lastVisitedQuestionKey + "'][data-funnel='" + lastVisitedQuestionFunnel + "']:first");

    /**
     * If active class doesn't exist, get last question
     */
    if (currentQuestion.length === 0) {
        previousQuestion = $('.growtype-quiz-question:last');
    }

    window.growtype_quiz_global.already_visited_questions_keys.splice(-1)
    window.growtype_quiz_global.already_visited_questions_funnels.splice(-1)

    delete saveQuizDataEvent().answers[lastVisitedQuestionKey]

    window.quizLastQuestion = currentQuestion;
    window.growtype_quiz_global.current_question_nr--;

    if (currentQuestion.length === 0) {
        initQuestion(currentQuestion, previousQuestion)
    } else {
        currentQuestion.removeClass('is-active').fadeOut(300, function () {
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

    let nextQuestionTitle = currentQuestion.attr('data-question-title');

    if ($('.growtype-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle.length > 0) {
        nextLabel = nextQuestionTitle;
    }

    updateQuizComponents(previousQuestion);

    /**
     * Reset next label
     */
    if (window.growtype_quiz_global.current_question_nr < window.quizQuestionsAmount - 1) {
        $(this).closest('.growtype-quiz').find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(nextLabel);
    }

    previousQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
        window.quizBackBtnWasClicked = false;
    });

    window.scrollTo(0, 0);
}
