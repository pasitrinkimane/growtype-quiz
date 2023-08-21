export function input(currentQuestion) {
    const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    };

    let isValid = true;
    currentQuestion.find('input[required]').each(function (index, element) {
        if (
            $(element).val().length === 0 ||
            ($(element).attr('type') === 'email' && !validateEmail($(element).val()))
        ) {
            isValid = false;
        }
    })

    return isValid;
}
