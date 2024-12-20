import {showPreviousQuestion} from "../actions/question/showPreviousQuestion";

window.quizBackBtnWasClicked = false;

export function previousQuestionTrigger() {
    $('.growtype-quiz-btn-go-back').click(function () {
        event.preventDefault();

        if (window.quizBackBtnWasClicked) {
            return false;
        }

        window.quizBackBtnWasClicked = true;

        if (window.growtype_quiz_global.already_visited_questions_keys.length === 0 && $(this).attr('data-back-url')) {
            return window.location.href = $(this).attr('data-back-url');
        }

        showPreviousQuestion()
    });
}
