export function shortcode(currentQuestion) {
    let isValid = true;

    currentQuestion.find('input[type="file"][required]').each(function (index, element) {
        if ($(element).val().length === 0) {
            $(element).closest('.growtype_quiz_input_wrapper').addClass('anim-wrong-selection');
            setTimeout(function () {
                $(element).closest('.growtype_quiz_input_wrapper').removeClass('anim-wrong-selection');
            }, 500);

            isValid = false;
        }
    })

    return isValid;
}
