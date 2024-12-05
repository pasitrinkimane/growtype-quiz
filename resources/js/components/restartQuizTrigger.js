import {showFirstQuestion} from "../actions/question/showFirstQuestion";

export function restartQuizTrigger() {
    $('.growtype-quiz .btn-restart-quiz').click(function (event) {
        event.preventDefault();

        window.growtype_quiz_global.is_finished = false;
        window.growtype_quiz_global.was_restarted = true;

        /**
         * Show correct answers
         */
        if ($('.growtype-quiz').attr('data-show-correct-answer') && $('.growtype-quiz').attr('data-correct-answers-trigger') === 'on_restart') {
            $('.growtype-quiz-question').map(function (index, element) {
                $(element).find('.growtype-quiz-question-answer').map(function (index, element) {
                    if ($(element).attr('data-cor') !== '1') {
                        $(element).addClass('is-wrong')
                    } else {
                        $(element).addClass('is-correct')
                    }
                });
            })
        }

        $('.growtype-quiz-nav .btn').attr('disabled', false)

        showFirstQuestion();
    });
}
