import {saveQuizData} from './../actions/crud/saveQuizData';
import {showNextQuestion} from './../actions/question/showNextQuestion';
import {validateQuestionEvent} from './../events/validateQuestionEvent';
import {collectQuizData} from "../actions/crud/collectQuizData";

export function nextQuestionTrigger() {
    let defaultInitialQuestion = $('.growtype-quiz-question.first-question')

    $('.growtype-quiz .growtype-quiz-btn-go-next').click(function () {
        let currentQuestion = $('.growtype-quiz-question.is-active');

        if (currentQuestion.length === 0) {
            currentQuestion = defaultInitialQuestion;
        }

        let isValidQuestion = currentQuestion.attr('data-answer-required') === 'false';

        if (!isValidQuestion) {
            document.dispatchEvent(validateQuestionEvent())

            if (!window.growtype_quiz_global.is_valid) {
                return
            }
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
