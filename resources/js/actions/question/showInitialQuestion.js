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

        // Count excluded (info) questions strictly before this position.
        // This matches what showNextQuestion produces: counter++ fires when
        // *leaving* a non-excluded question, so arriving at question N the
        // counter equals N minus the number of excluded questions before it.
        const excludedBefore = quizWrapper.find('.growtype-quiz-question.exclude-questions-amount').filter(function () {
            return parseInt($(this).attr('data-question-nr')) < questionNr;
        }).length;

        // If the current question is itself an excluded info slide, it also
        // doesn't count — subtract one more so the counter stays at the same
        // value it had when leaving the previous real question.
        const currentIsExcluded = quizWrapper.find(
            '.growtype-quiz-question.exclude-questions-amount[data-question-nr="' + questionNr + '"]'
        ).length > 0 ? 1 : 0;

        let current_question_counter_nr = questionNr;

        if (questionNr > 1) {
            current_question_counter_nr = Math.max(1, questionNr - excludedBefore - currentIsExcluded);
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
