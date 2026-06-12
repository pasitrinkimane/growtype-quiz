import { disabledValueIsIncluded } from "../general";

export function updateQuestionsCounter(quizWrapper, nextQuestion = null) {
    let quizId = quizWrapper.attr('id');

    /**
     * Update url quiz nr
     */
    if (quizWrapper.find('.growtype-quiz').attr('data-show-question-nr-in-url')) {
        let updateUrlNr = true;
        if (nextQuestion && nextQuestion.attr('data-key') === 'final') {
            updateUrlNr = false;
        }

        if (updateUrlNr) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.set('question', window.growtype_quiz_global[quizId]['current_question_nr']);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({ path: newurl }, '', newurl);
        }
    }

    const initialFunnel = window.growtype_quiz_global[quizId]['initial_funnel'] || 'a';
    const baseExclude = ':not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible):not(.exclude-questions-amount)';
    const countedExclude = ':not(.growtype-quiz-question[data-question-type="success"]):not(.exclude-questions-amount):not([class*="skipped"]):not(.is-always-visible)';

    /**
     * Determine the active (non-initial) funnel to count.
     * Priority: current question element → sessionStorage current_funnel → configured default funnel.
     * Using the DOM element avoids stale sessionStorage values from a previous quiz run.
     */
    let activeFunnel = null;

    if (nextQuestion) {
        // Most reliable: read directly from the question being shown
        const qFunnel = nextQuestion.attr('data-funnel');
        if (qFunnel && qFunnel !== initialFunnel) {
            activeFunnel = qFunnel;
        }
    }

    if (!activeFunnel) {
        // Fallback: check already-visited funnels for a non-initial funnel (user has branched before)
        const visited = window.growtype_quiz_global[quizId]['already_visited_questions_funnels'] || [];
        const nonInitialVisited = [...new Set(visited)].filter(f => f !== initialFunnel);
        if (nonInitialVisited.length > 0) {
            activeFunnel = nonInitialVisited[nonInitialVisited.length - 1]; // most recently visited
        }
    }

    if (!activeFunnel) {
        // Final fallback: use the configured default expected funnel (for initial questions before branching)
        activeFunnel = quizWrapper.find('.growtype-quiz').attr('data-default-funnel') || 'licensed';
    }

    // Count questions for initial funnel + active funnel
    window.growtype_quiz_global[quizId]['quiz_questions_amount'] =
        quizWrapper.find('.growtype-quiz-question[data-funnel="' + initialFunnel + '"]' + baseExclude).length +
        quizWrapper.find('.growtype-quiz-question[data-funnel="' + activeFunnel + '"]' + baseExclude).length;

    window.growtype_quiz_global[quizId]['quiz_counted_questions_amount'] =
        quizWrapper.find('.growtype-quiz-question[data-funnel="' + initialFunnel + '"]' + countedExclude).length +
        quizWrapper.find('.growtype-quiz-question[data-funnel="' + activeFunnel + '"]' + countedExclude).length;


    /**
     * Check conditionally disabled questions and subtract them from questions amount
     */
    if (window.growtype_quiz_data[quizId] && Object.entries(window.growtype_quiz_data[quizId]['answers']).length > 0) {
        quizWrapper.find('.growtype-quiz-question').each(function (index, element) {
            if ($(element).attr('data-disabled-if').length > 0) {
                if (disabledValueIsIncluded(quizWrapper, $(element).attr('data-disabled-if'))) {
                    window.growtype_quiz_global[quizId]['quiz_questions_amount']--;
                    window.growtype_quiz_global[quizId]['quiz_counted_questions_amount']--;
                }
            }
        });
    }

    if (quizWrapper.find('.growtype-quiz-question.is-active:visible').hasClass('is-visible') && !quizWrapper.find('.growtype-quiz-question.is-active:visible').prev().hasClass('is-always-visible')) {
        window.growtype_quiz_global[quizId]['quiz_questions_amount']--;
    }
}
