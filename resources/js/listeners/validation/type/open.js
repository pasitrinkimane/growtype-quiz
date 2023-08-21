export function open(currentQuestion) {
    let isValid = true;
    let textarea = currentQuestion.find('textarea').val();

    if (textarea.length === 0) {
        isValid = false;
    }

    return isValid;
}
