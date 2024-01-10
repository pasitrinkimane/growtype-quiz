import {updateQuizComponents} from "./updateQuizComponents";
import {showQuestionEvent} from "../../events/showQuestionEvent";

/**
 * Show last slide
 */
export function showInitialQuestion() {
    let questionNr = 1;

    if (growtype_quiz_local.show_question_nr_in_url) {
        questionNr = new URLSearchParams(window.location.search).get('question');
        questionNr = growtype_quiz_local.show_question_nr_in_url && questionNr !== 'undefined' ? parseInt(questionNr) : 1;
        questionNr = !isNaN(questionNr) ? questionNr : 1;
    }

    let question = $('.growtype-quiz-question[data-question-nr="' + questionNr + '"]')

    if (questionNr > 1) {
        $('.growtype-quiz-question').hide();
    }

    window.growtype_quiz_global.current_question_nr = questionNr;

    question.addClass('is-active').show();

    updateQuizComponents(question);

    /**
     * Show question general event
     */
    document.dispatchEvent(showQuestionEvent({
        currentQuestion: question
    }));
}
