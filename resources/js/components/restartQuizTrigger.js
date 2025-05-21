import {showFirstQuestion} from "../actions/question/showFirstQuestion";

export function restartQuizTrigger(quizWrapper) {
    quizWrapper.find('.growtype-quiz .btn-restart-quiz').click(function (event) {
        let quizId = quizWrapper.attr('id');

        event.preventDefault();

        window.growtype_quiz_global[quizId]['is_finished'] = false;
        window.growtype_quiz_global[quizId]['was_restarted'] = true;

        /**
         * Show correct answers
         */
        if (quizWrapper.find('.growtype-quiz').attr('data-show-correct-answer') && quizWrapper.find('.growtype-quiz').attr('data-correct-answers-trigger') === 'on_restart') {
            quizWrapper.find('.growtype-quiz-question').map(function (index, element) {
                $(element).find('.growtype-quiz-question-answer').map(function (index, element) {
                    if ($(element).attr('data-cor') !== '1') {
                        $(element).addClass('is-wrong')
                    } else {
                        $(element).addClass('is-correct')
                    }
                });
            })
        }

        quizWrapper.find('.growtype-quiz-nav .btn').attr('disabled', false)

        showFirstQuestion(quizWrapper);
    });
}
