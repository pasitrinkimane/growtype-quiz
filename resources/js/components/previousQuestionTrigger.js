import {showPreviousQuestion} from "../actions/question/showPreviousQuestion";

export function previousQuestionTrigger(quizWrapper) {
    let quizId = quizWrapper.attr('id');

    quizWrapper.find('.growtype-quiz-btn-go-back').click(function () {
        event.preventDefault();

        if (window.growtype_quiz_global[quizId]['quiz_back_btn_was_clicked']) {
            return false;
        }

        window.growtype_quiz_global[quizId]['quiz_back_btn_was_clicked'] = true;

        /**
         * Only redirect to data-back-url when the user is genuinely on the
         * first question. When the page is loaded directly via ?question=N,
         * already_visited_questions_keys is empty even mid-quiz, so we must
         * also check that the active question is actually Q1 before bailing.
         */
        const activeQ = quizWrapper.find('.growtype-quiz-question.is-active');
        const isOnFirstQuestion = activeQ.length === 0
            || activeQ.hasClass('first-question')
            || parseInt(activeQ.attr('data-question-nr')) <= 1;

        if (isOnFirstQuestion && $(this).attr('data-back-url')) {
            return window.location.href = $(this).attr('data-back-url');
        }


        showPreviousQuestion(quizWrapper)
    });
}
