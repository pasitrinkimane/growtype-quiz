import { updateQuizComponents } from "./updateQuizComponents";
import { showQuestionEvent } from "../../events/showQuestionEvent";

/**
 * Show last slide
 */
export function showInitialQuestion(quizWrapper) {
    if (quizWrapper.length === 0) {
        return;
    }

    let quizId = quizWrapper.attr('id');

    // Ensure the global object for this quiz exists
    window.growtype_quiz_global = window.growtype_quiz_global || {};
    window.growtype_quiz_global[quizId] = window.growtype_quiz_global[quizId] || {};

    let questionNr = quizWrapper.find('.growtype-quiz').find('.growtype-quiz-question').not(".is-always-visible").first().attr('data-question-nr');

    if (questionNr === undefined) {
        questionNr = 1;
    }

    if (quizWrapper.find('.growtype-quiz').attr('data-show-question-nr-in-url')) {
        questionNr = new URLSearchParams(window.location.search).get('question');
        questionNr = quizWrapper.find('.growtype-quiz').attr('data-show-question-nr-in-url') && questionNr !== 'undefined' ? parseInt(questionNr) : 1;
        questionNr = !isNaN(questionNr) ? questionNr : 1;

        let excludedFromCountingQuestionsAmount = $('.exclude-questions-amount').length ?? 0;
        let current_question_counter_nr = questionNr;

        if (questionNr > 1) {
            current_question_counter_nr = questionNr - excludedFromCountingQuestionsAmount;
        }

        window.growtype_quiz_global[quizId]['current_question_counter_nr'] = current_question_counter_nr;
    }

    let question = quizWrapper.find('.growtype-quiz-question[data-question-nr="' + questionNr + '"]');

    if (questionNr > 1) {
        quizWrapper.find('.growtype-quiz-question').hide();
    }

    window.growtype_quiz_global[quizId]['current_question_nr'] = questionNr;

    question.addClass('is-active').show();

    /**
     * Set nav next arrow label
     */
    if (quizWrapper.find('.growtype-quiz-nav[data-type="footer"]').attr('data-question-title-nav') === 'true') {
        setTimeout(function () {
            let nextQuestionTitle = question.nextAll('.growtype-quiz-question:first').attr('data-question-title');
            if (nextQuestionTitle) {
                quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(nextQuestionTitle);
            }
        }, 100);
    }

    /**
     * Update quiz components
     */
    updateQuizComponents(question);

    /**
     * Show question general event
     */
    document.dispatchEvent(showQuestionEvent({
        currentQuestion: question
    }));
}
