import {showFirstQuestion} from './../actions/question/showFirstQuestion.js';

export function restartQuizTrigger() {
    $('.b-quiz .btn-restart-quiz').click(function () {
        event.preventDefault();
        if ($(this).attr('data-show-correct-answers') === 'true') {
            $('.b-quiz').addClass('show-correct-answers')
        }
        showFirstQuestion();
    });
}
