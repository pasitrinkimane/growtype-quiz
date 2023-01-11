import {showFirstQuestion} from "../actions/question/showFirstQuestion";

export function restartQuizTrigger() {
    $('.growtype-quiz .btn-restart-quiz').click(function (event) {
        event.preventDefault();

        window.growtype_quiz_global.is_finished = false;

        $('.growtype-quiz-nav .btn').attr('disabled', false)

        showFirstQuestion();
    });
}
