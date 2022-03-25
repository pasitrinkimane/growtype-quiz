import {saveQuizData} from './../actions/crud/saveQuizData.js';
import {showNextQuestion} from './../actions/question/showNextQuestion.js';
import {validateQuestion} from './../actions/validation/validateQuestion.js';
import {collectQuizData} from "../actions/crud/collectQuizData";

export function nextQuestionTrigger() {
    $('.b-quiz .btn-next').click(function () {
        event.preventDefault();

        let currentQuestion = $('.b-quiz-question.is-active');

        let isValidQuestion = validateQuestion();

        if (!isValidQuestion) {
            return false;
        }

        $('.b-quiz-footer .btn').attr('disabled', true)
        collectQuizData(currentQuestion);
        showNextQuestion();
    });
}
