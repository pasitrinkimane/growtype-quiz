import {showProgressIndicators} from "../../actions/progress/general";
import {updateProgressCounter} from "../progress/counter/updateProgressCounter";
import {collectQuizData} from "../crud/collectQuizData";

/**
 * Show last slide
 */
export function showFirstQuestion(initialLoad = false) {
    let firstQuestion = $('.b-quiz-question.first-question');

    if (firstQuestion.hasClass('is-always-visible')) {
        firstQuestion = $('.b-quiz-question:not(.is-always-visible):first');
    }

    if (initialLoad) {
        firstQuestion.addClass('is-active');
    }

    /**
     * Set nav next arrow label
     */
    setTimeout(function () {
        let nextQuestionTitle = firstQuestion.nextAll('.b-quiz-question:first').attr('data-question-title');

        if ($('.b-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle !== undefined && nextQuestionTitle.length > 0) {
            $('.b-quiz-nav .btn-go-next .e-label').attr('data-label', nextQuestionTitle).text(nextQuestionTitle)
        }
    }, 500)

    if (!initialLoad && !firstQuestion.hasClass('is-active')) {
        $('.b-quiz-question').removeClass('is-active').fadeOut().promise().done(function () {
            window.growtype_quiz.current_question_nr = 1;
            updateProgressCounter();
            firstQuestion.addClass('is-active').fadeIn();
            showProgressIndicators();
            $('.btn-go-next').show();
        });
    }
}
