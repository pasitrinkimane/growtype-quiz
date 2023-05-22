import {radio} from "./type/radio";
import {open} from "./type/open";
import {general} from "./type/general";
import {shortcode} from "./type/shortcode";

document.addEventListener('validateQuestion', validateQuestion)

export function validateQuestion() {
    let isValid = true;

    $('.growtype-quiz-question:visible').each(function (index, element) {
        let currentQuestion = $(element);

        /**
         * First Check shortcodes
         */
        isValid = shortcode(currentQuestion);

        if (isValid) {
            if (currentQuestion.attr('data-question-type') === 'radio') {
                isValid = radio(currentQuestion);
            } else if (currentQuestion.attr('data-question-type') === 'open') {
                isValid = open(currentQuestion);
            } else if (currentQuestion.attr('data-question-type') === 'general') {
                isValid = general(currentQuestion);
            }
        }

        if (!isValid) {
            return false;
        }
    });

    window.growtype_quiz_global.is_valid = isValid;
}

