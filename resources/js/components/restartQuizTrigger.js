import {showFirstQuestion} from './../actions/question/showFirstQuestion.js';

export function restartQuizTrigger() {
    $('.growtype-quiz .btn-restart-quiz').click(function () {
        event.preventDefault();
        if ($(this).attr('data-show-correct-answers') === 'true') {
            $('.growtype-quiz').addClass('show-correct-answers')
        }
        showFirstQuestion();
    });
}
