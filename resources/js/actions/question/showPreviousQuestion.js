import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {updateQuestionsCounter} from "../progress/counter/updateQuestionsCounter";
import {saveQuizDataEvent} from "../../events/saveQuizData";

/**
 * Show next slide
 */
export function showPreviousQuestion() {
    let currentQuestion = $('.b-quiz-question.is-active');
    let lastVisitedQuestionKey = window.growtype_quiz.already_visited_questions_keys.slice(-1)[0];
    let lastVisitedQuestionFunnel = window.growtype_quiz.already_visited_questions_funnels.slice(-1)[0];

    var previousQuestion = currentQuestion.prevAll(".b-quiz-question[data-key='" + lastVisitedQuestionKey + "'][data-funnel='" + lastVisitedQuestionFunnel + "']:first");

    window.growtype_quiz.already_visited_questions_keys.splice(-1)
    window.growtype_quiz.already_visited_questions_funnels.splice(-1)

    delete saveQuizDataEvent().answers[lastVisitedQuestionKey]

    window.quizLastQuestion = currentQuestion;
    window.growtype_quiz.current_question_nr--;

    currentQuestion.removeClass('is-active').fadeOut(300, function () {
        updateQuestionsCounter();
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
        if (window.growtype_quiz.current_question_nr < window.quizQuestionsAmount - 1) {
            $(this).closest('.b-quiz').find('.b-quiz-nav .btn-go-next .e-label').text(nextLabel);
        }

        previousQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
            window.quizBackBtnWasClicked = false;
        });

        window.scrollTo(0, 0);
    });
}
