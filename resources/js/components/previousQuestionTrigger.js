import {showPreviousQuestion} from "../actions/question/showPreviousQuestion";

window.quizBackBtnWasClicked = false;

export function previousQuestionTrigger() {
    $('.b-quiz .btn-back').click(function () {
        event.preventDefault();

        if (window.quizBackBtnWasClicked) {
            return false;
        }

        window.quizBackBtnWasClicked = true;

        if (window.quizQuestionsKeysAlreadyVisited.length === 0) {
            return window.location.replace("/");
        }

        showPreviousQuestion()
    });
}
