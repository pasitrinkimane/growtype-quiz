export function shortcode(currentQuestion) {
    let isValid = true;
    currentQuestion.find('input[type="file"][required]').each(function (index, element) {
        if ($(element).val().length === 0) {
            $(element).closest('.growtype-quiz-file-input-wrapper').addClass('anim-wrong-selection');
            setTimeout(function () {
                $(element).closest('.growtype-quiz-file-input-wrapper').removeClass('anim-wrong-selection');
            }, 500);

            isValid = false;
        }
    })

    return isValid;
}
