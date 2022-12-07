import {saveQuizData} from './../actions/crud/saveQuizData.js';
import {showNextQuestion} from './../actions/question/showNextQuestion.js';
import {validateQuestion} from './../actions/validation/validateQuestion.js';
import {collectQuizData} from "../actions/crud/collectQuizData";

export function nextQuestionTrigger() {
    $('.growtype-quiz .growtype-quiz-btn-go-next').click(function () {
        event.preventDefault();

        let currentQuestion = $('.growtype-quiz-question.is-active');

        let isValidQuestion = currentQuestion.attr('data-answer-required') === 'false' ? true : validateQuestion();

        if (!isValidQuestion) {
            return false;
        }

        $('.growtype-quiz-nav .btn').attr('disabled', true)

        /**
         * Colect answers for existing questions
         */
        if ($('.growtype-quiz-question.is-visible').length > 0) {
            $('.growtype-quiz-question.is-visible').each(function (index, element) {
                collectQuizData($(element));
            });
        }

        collectQuizData(currentQuestion);

        showNextQuestion(currentQuestion);
    });
}
