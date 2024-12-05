import {updateQuizComponents} from "./updateQuizComponents";
import {showQuestionEvent} from "../../events/showQuestionEvent";

/**
 * Show last slide
 */
export function showInitialQuestion() {
    let questionNr = $('.growtype-quiz').find('.growtype-quiz-question').not(".is-always-visible").first().attr('data-question-nr');

    if (questionNr === undefined) {
        questionNr = 1;
    }

    if ($('.growtype-quiz').attr('data-show-question-nr-in-url')) {
        questionNr = new URLSearchParams(window.location.search).get('question');
        questionNr = $('.growtype-quiz').attr('data-show-question-nr-in-url') && questionNr !== 'undefined' ? parseInt(questionNr) : 1;
        questionNr = !isNaN(questionNr) ? questionNr : 1;
    }

    let question = $('.growtype-quiz-question[data-question-nr="' + questionNr + '"]')

    if (questionNr > 1) {
        $('.growtype-quiz-question').hide();
    }

    window.growtype_quiz_global.current_question_nr = questionNr;

    question.addClass('is-active').show();

    /**
     * Set nav next arrow label
     */
    if ($('.growtype-quiz-nav[data-type="footer"]').attr('data-question-title-nav') === 'true') {
        setTimeout(function () {
            let nextQuestionTitle = question.nextAll('.growtype-quiz-question:first').attr('data-question-title');
            if (nextQuestionTitle) {
                $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(nextQuestionTitle);
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
