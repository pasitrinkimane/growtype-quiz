export function quizStepShouldBeSkipped(element) {
    // return $(element).attr('data-question-type') !== 'info' && !$(element).hasClass('exclude-questions-amount');
    return !$(element).hasClass('exclude-questions-amount');
}
