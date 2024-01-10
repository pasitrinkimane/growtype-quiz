import {radio} from "./type/radio";
import {open} from "./type/open";
import {general} from "./type/general";
import {input} from "./type/input";

document.addEventListener('growtypeQuizValidateQuestion', validateQuestion)

export function validateQuestion() {
    let isValid = true;

    $('.growtype-quiz-question:visible').each(function (index, element) {
        let currentQuestion = $(element);

        /**
         * First Check general inputs
         */
        isValid = input(currentQuestion);

        if (isValid) {
            if (currentQuestion.attr('data-question-type') === 'radio') {
                isValid = radio(currentQuestion);
            } else if (currentQuestion.attr('data-question-type') === 'open') {
                isValid = open(currentQuestion);
            } else if (currentQuestion.attr('data-question-type') === 'general') {
                isValid = general(currentQuestion);
            }
        }

        let checkboxes = $(element).find('input[type="checkbox"]')
        let checkboxesAmount = checkboxes.length

        /**
         * Update validity class
         */
        if (checkboxesAmount > 0) {
            checkboxes.each(function (index, element) {
                if (!$(element).is(':checked')) {
                    isValid = false;
                }
            });
        }

        $('.growtype-quiz-wrapper').removeClass('is-valid is-half-valid');

        if ($(element).find('input:not([type="checkbox"])').val() !== undefined && $(element).find('input:not([type="checkbox"])').val().length > 0) {
            $('.growtype-quiz-wrapper').addClass(isValid ? 'is-valid' : 'is-half-valid');
        } else {
            $('.growtype-quiz-wrapper').addClass(isValid ? 'is-valid' : '');
        }

        if (!isValid) {
            $(element).find('.growtype-quiz-question-answers').addClass('anim-wrong-selection');

            setTimeout(function () {
                $(element).find('.growtype-quiz-question-answers').removeClass('anim-wrong-selection');
            }, 500);

            return false;
        }
    });

    window.growtype_quiz_global.is_valid = isValid;
}

