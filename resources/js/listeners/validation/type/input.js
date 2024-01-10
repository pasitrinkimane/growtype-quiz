export function input(currentQuestion) {
    const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    };

    currentQuestion.find('input:visible[required],textarea:visible[required]').removeClass('is-invalid');

    let isValid = true;
    currentQuestion.find('input:visible[required],textarea:visible[required]').each(function (index, element) {
        if (
            (
                $(element).attr('min') !== undefined && parseInt($(element).val()) < parseInt($(element).attr('min'))
            )
            ||
            (
                $(element).attr('max') !== undefined && parseInt($(element).val()) > parseInt($(element).attr('max'))
            )
            ||
            (
                $(element).val().length === 0
            )
            ||
            (
                $(element).attr('type') === 'email' && !validateEmail($(element).val())
            )
        ) {
            $(element).addClass('is-invalid');
            isValid = false;
        }
    })

    return isValid;
}
