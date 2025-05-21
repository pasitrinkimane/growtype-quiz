import {showPreviousQuestion} from "../actions/question/showPreviousQuestion";

export function previousQuestionTrigger(quizWrapper) {
    let quizId = quizWrapper.attr('id');

    quizWrapper.find('.growtype-quiz-btn-go-back').click(function () {
        event.preventDefault();

        if (window.growtype_quiz_global[quizId]['quiz_back_btn_was_clicked']) {
            return false;
        }

        window.growtype_quiz_global[quizId]['quiz_back_btn_was_clicked'] = true;

        if (window.growtype_quiz_global[quizId]['already_visited_questions_keys'].length === 0 && $(this).attr('data-back-url')) {
            return window.location.href = $(this).attr('data-back-url');
        }

        showPreviousQuestion(quizWrapper)
    });
}
