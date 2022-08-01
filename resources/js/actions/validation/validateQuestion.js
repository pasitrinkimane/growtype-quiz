import {radio} from "./type/radio";
import {open} from "./type/open";

export function validateQuestion() {
    let isValid = true;

    $('.b-quiz-question:visible').each(function (index, element) {
        let currentQuestion = $(element);

        if (currentQuestion.attr('data-question-type') === 'radio') {
            isValid = radio(currentQuestion);
        } else if (currentQuestion.attr('data-question-type') === 'open') {
            isValid = open(currentQuestion);
        }

        if (!isValid) {
            return false;
        }
    });

    return isValid;
}

