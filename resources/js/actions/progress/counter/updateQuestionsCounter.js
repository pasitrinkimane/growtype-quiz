import {disabledValueIsIncluded} from "../general";

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
            window.history.pushState({path: newurl}, '', newurl);
        }
    }

    window.growtype_quiz_global[quizId]['quiz_questions_amount'] = quizWrapper.find('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible):not(.exclude-questions-amount)').length;
    window.growtype_quiz_global[quizId]['quiz_counted_questions_amount'] = quizWrapper.find('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.exclude-questions-amount):not([class*="skipped"]):not(.is-always-visible)').length + window.growtype_quiz_global[quizId]['additional_questions_amount'];

    if (window.growtype_quiz_global[quizId]['already_visited_questions_funnels']) {
        window.growtype_quiz_global[quizId]['already_visited_questions_funnels'].map(function (element) {
            if (element !== window.growtype_quiz_global[quizId]['initial_funnel']) {
                let extraSlides = quizWrapper.find('.growtype-quiz-question[data-funnel="' + element + '"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
                window.growtype_quiz_global[quizId]['quiz_questions_amount'] = window.growtype_quiz_global[quizId]['quiz_questions_amount'] + extraSlides;
            }
        });
    }

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
