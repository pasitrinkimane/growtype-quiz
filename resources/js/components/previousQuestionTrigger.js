import {showPreviousQuestion} from "../actions/question/showPreviousQuestion";

window.quizBackBtnWasClicked = false;

export function previousQuestionTrigger() {
    $('.growtype-quiz .growtype-quiz-btn-go-back').click(function () {
        event.preventDefault();

        if (window.quizBackBtnWasClicked) {
            return false;
        }

        window.quizBackBtnWasClicked = true;

        if (window.growtype_quiz.already_visited_questions_keys.length === 0) {
            return window.location.href = "/";
        }

        showPreviousQuestion()
    });
}
