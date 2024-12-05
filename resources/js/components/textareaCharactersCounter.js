export function textareaCharactersCounter() {
    let charactersLimit = $('.growtype-quiz-modal textarea').attr('maxlength');

    if (charactersLimit) {
        $('.growtype-quiz-modal .e-characters-amount .e-total').text(charactersLimit);

        $('.growtype-quiz-modal textarea[name="additional_question"]').on('input', function () {
            let charactersAmount = $('.growtype-quiz-modal textarea[name="additional_question"]').val().length;
            $('.growtype-quiz-modal .e-characters-amount .e-amount').text(charactersAmount);
        });
    }
}
