import {validateQuestion} from "../listeners/validation/validateQuestion";

export function input() {

    /**
     * File
     */
    $('.growtype-quiz input[type=file]').change(function (e) {
        let maxFileSize = $(this).attr('max-size');
        let maxSizeErrorMessage = $(this).attr('max-size-error-message');
        if (maxFileSize !== undefined) {
            $(e.target.files).each(function (index, element) {
                if (element.size > maxFileSize) {
                    if (maxSizeErrorMessage.length > 0) {
                        maxSizeErrorMessage = maxSizeErrorMessage.replace(':image_name', element.name).replace(':max_size', (maxFileSize / 1000000) + 'mb')
                        alert(maxSizeErrorMessage)
                    } else {
                        alert(element.name + " is too big! Max file size allowed - " + (maxFileSize / 1000000) + 'mb')
                    }
                    e.target.value = "";
                }
            });
        }

        let selectedPlaceholderSingle = $(this).attr('data-selected-placeholder-single');
        let selectedPlaceholderMultiple = $(this).attr('data-selected-placeholder-multiple');
        let filesAmount = e.target.files.length;

        $(this).closest('.growtype-quiz-input-wrapper')
            .find('.growtype-quiz-input-label')
            .removeClass('is-active')
            .text($(this).attr('data-placeholder'))

        if (filesAmount > 0 && (selectedPlaceholderSingle.length > 0 || selectedPlaceholderMultiple.length > 0)) {

            let selectedPlaceholder = selectedPlaceholderSingle.replace(':nr', filesAmount)

            if (filesAmount > 1) {
                selectedPlaceholder = selectedPlaceholderMultiple.replace(':nr', filesAmount)
            }

            $(this).closest('.growtype-quiz-input-wrapper')
                .find('.growtype-quiz-input-label')
                .addClass('is-active')
                .text(selectedPlaceholder)
        }
    });

    $('.growtype-quiz-input-wrapper input, .growtype-quiz-input-wrapper textarea').on('keyup', function () {
        validateQuestion();
    });

    $('.growtype-quiz-input-wrapper input[type="checkbox"]').on('click', function () {
        validateQuestion();
    });
}
