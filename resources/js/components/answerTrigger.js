export function answerTrigger() {
    $('.b-quiz-question-answers .b-quiz-question-answer').click(function () {
        $(this).closest('.b-quiz-question-answers').find('.b-quiz-question-answer').removeClass('is-active');
        if (!$(this).hasClass('is-active')) {
            $(this).addClass('is-active');
        }
    });
}
