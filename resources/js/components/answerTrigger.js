export function answerTrigger() {
    $('.b-quiz-question-answers .b-quiz-question-answer').click(function () {
        if ($(this).closest('.b-quiz-question').attr('data-answer-type') !== 'multiple') {
            $(this).closest('.b-quiz-question-answers').find('.b-quiz-question-answer').removeClass('is-active');
        }
        if (!$(this).hasClass('is-active')) {
            $(this).addClass('is-active');
        } else {
            if ($(this).closest('.b-quiz-question').attr('data-answer-type') === 'multiple') {
                $(this).removeClass('is-active');
            }
        }
    });
}
