import {disabledValueIsIncluded} from "../general";

export function updateQuestionsCounter(nextQuestion = null) {

    /**
     * Update url quiz nr
     */
    if ($('.growtype-quiz').attr('data-show-question-nr-in-url')) {
        let updateUrlNr = true;
        if (nextQuestion && nextQuestion.attr('data-key') === 'final') {
            updateUrlNr = false;
        }

        if (updateUrlNr) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.set('question', window.growtype_quiz_global.current_question_nr);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({path: newurl}, '', newurl);
        }
    }

    window.quizQuestionsAmount = $('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible):not(.exclude-questions-amount)').length;
    window.quizCountedQuestionsAmount = $('.growtype-quiz-question[data-funnel="a"]:not(.growtype-quiz-question[data-question-type="success"]):not(.growtype-quiz-question[data-question-type="info"]):not([class*="skipped"]):not(.is-always-visible)').length + window.growtype_quiz_global.additional_questions_amount;

    if (window.growtype_quiz_global.already_visited_questions_funnels) {
        window.growtype_quiz_global.already_visited_questions_funnels.map(function (element) {
            if (element !== window.growtype_quiz_global.initial_funnel) {
                let extraSlides = $('.growtype-quiz-question[data-funnel="' + element + '"]:not(.growtype-quiz-question[data-question-type="success"]):not(.is-always-visible)').length;
                window.quizQuestionsAmount = window.quizQuestionsAmount + extraSlides;
            }
        });
    }

    /**
     * Check conditionally disabled questions and subtract them from questions amount
     */
    if (Object.entries(window.growtype_quiz_data.answers).length > 0) {
        $('.growtype-quiz-question').each(function (index, element) {
            if ($(element).attr('data-disabled-if').length > 0) {
                if (disabledValueIsIncluded($(element).attr('data-disabled-if'))) {
                    window.quizQuestionsAmount--;
                    window.quizCountedQuestionsAmount--;
                }
            }
        });
    }

    if ($('.growtype-quiz-question.is-active:visible').hasClass('is-visible') && !$('.growtype-quiz-question.is-active:visible').prev().hasClass('is-always-visible')) {
        window.quizQuestionsAmount--;
    }
}
