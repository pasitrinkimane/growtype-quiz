import {radio} from "./type/radio";
import {open} from "./type/open";

export function validateQuestion() {
    let currentQuestion = $('.b-quiz-question.is-active');
    let isValid = true;

    if (currentQuestion.attr('data-type') === 'radio') {
        isValid = radio();
    } else if (currentQuestion.attr('data-type') === 'open') {
        isValid = open();
    }

    return isValid;
}

