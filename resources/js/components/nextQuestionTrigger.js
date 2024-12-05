import {showNextQuestion} from './../actions/question/showNextQuestion';
import {validateQuestionEvent} from './../events/validateQuestionEvent';
import {collectQuizData} from "../actions/crud/collectQuizData";
import {nextQuestionTriggerEvent} from "../events/nextQuestionTriggerEvent";

export function nextQuestionTrigger(quizContainer = $('.growtype-quiz')) {
    let defaultInitialQuestion = quizContainer.find('.growtype-quiz-question.first-question');

    quizContainer.find('.growtype-quiz-btn-go-next').click(function () {
        let currentQuestion = $(this).closest('.growtype-quiz').find('.growtype-quiz-question.is-active');

        /**
         * Show next question event
         */
        document.dispatchEvent(nextQuestionTriggerEvent({
            currentQuestion: currentQuestion,
        }));

        /**
         * Prevent execution
         */
        if (window.showNextQuestionWasFired) {
            return;
        }

        if (currentQuestion.length === 0) {
            currentQuestion = defaultInitialQuestion;
        }

        let isValidQuestion = currentQuestion.attr('data-answer-required') === 'false';

        if (!isValidQuestion) {
            document.dispatchEvent(validateQuestionEvent({
                currentQuestion: currentQuestion,
            }))

            if (!window.growtype_quiz_global.is_valid) {
                return
            }
        }

        $('.growtype-quiz-nav .btn').attr('disabled', true)

        /**
         * Colect answers for existing questions
         */
        if ($('.growtype-quiz-question.is-always-visible').length > 0) {
            $('.growtype-quiz-question.is-always-visible').each(function (index, element) {
                collectQuizData($(element));
            });
        }

        /**
         *
         */
        collectQuizData(currentQuestion);

        /**
         *
         */
        showNextQuestion(currentQuestion);
    });
}
