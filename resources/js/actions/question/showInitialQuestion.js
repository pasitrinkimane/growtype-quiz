import {updateQuizComponents} from "./updateQuizComponents";

/**
 * Show last slide
 */
export function showInitialQuestion() {
    let questionNr = new URLSearchParams(window.location.search).get('question');
    questionNr = questionNr === null || growtype_quiz_local.show_question_nr_in_url !== 'true' ? 1 : parseInt(questionNr);

    let question = $('.growtype-quiz-question[data-question-nr="' + questionNr + '"]')

    if (questionNr > 1) {
        $('.growtype-quiz-question').hide();
        window.growtype_quiz_global.current_question_nr = questionNr;
    }

    question.addClass('is-active').show();

    updateQuizComponents(question);
}
