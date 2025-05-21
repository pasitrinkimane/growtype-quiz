import {showProgressIndicators} from "../../actions/progress/general";
import {updateProgressCounter} from "../progress/counter/updateProgressCounter";
import {updateQuizComponents} from "./updateQuizComponents";

/**
 * Show last slide
 */
export function showFirstQuestion(quizWrapper) {
    let quizId = quizWrapper.attr('id');
    let firstQuestion = quizWrapper.find('.growtype-quiz-question.first-question');

    if (firstQuestion.hasClass('is-always-visible')) {
        firstQuestion = quizWrapper.find('.growtype-quiz-question:not(.is-always-visible):first');
    }

    updateQuizComponents(firstQuestion);

    /**
     * Set nav next arrow label
     */
    setTimeout(function () {
        let nextQuestionTitle = quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label-start');

        if (nextQuestionTitle !== undefined && nextQuestionTitle.length > 0) {
            if (quizWrapper.find('.growtype-quiz-nav[data-type="footer"]').attr('data-question-title-nav') === 'true') {
                nextQuestionTitle = firstQuestion.nextAll('.growtype-quiz-question:first').attr('data-question-title');
                quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label', nextQuestionTitle).text(nextQuestionTitle)
            } else {
                quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(nextQuestionTitle)
            }
        }
    }, 500)

    if (!firstQuestion.hasClass('is-active')) {
        quizWrapper.find('.growtype-quiz-question').removeClass('is-active').fadeOut().promise().done(function () {
            let quizWrapper = $(this).closest('.growtype-quiz-wrapper');

            window.growtype_quiz_global[quizId]['current_question_nr'] = 1;
            window.growtype_quiz_global[quizId]['current_question_counter_nr'] = 1;

            updateProgressCounter(quizWrapper);

            firstQuestion.addClass('is-active').fadeIn();

            showProgressIndicators(quizWrapper);

            quizWrapper.find('.growtype-quiz-btn-go-next').show();
        });
    }
}
