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

        let previousQuestion = $('.growtype-quiz-question[data-question-nr="' + (questionNr - 1) + '"]')
        window.growtype_quiz_global.current_question_nr = questionNr;
        window.growtype_quiz_global.already_visited_questions_keys = [previousQuestion.attr('data-key')];
        window.growtype_quiz_global.already_visited_questions_funnels = [window.growtype_quiz_global.initial_funnel];
    }

    question.addClass('is-active').show();

    updateQuizComponents(question);
}
