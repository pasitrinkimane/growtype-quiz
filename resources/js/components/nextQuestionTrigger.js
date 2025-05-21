import {showNextQuestion} from './../actions/question/showNextQuestion';
import {validateQuestionEvent} from './../events/validateQuestionEvent';
import {collectQuizData} from "../actions/crud/collectQuizData";
import {nextQuestionTriggerEvent} from "../events/nextQuestionTriggerEvent";

export function nextQuestionTrigger(quizWrapper) {
    let quizId = quizWrapper.attr('id');
    let defaultInitialQuestion = quizWrapper.find('.growtype-quiz-question.first-question');

    quizWrapper.find('.growtype-quiz-btn-go-next').click(function () {
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
        if (window.growtype_quiz_global[quizId]['showNextQuestionWasFired']) {
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

            if (!window.growtype_quiz_global[quizId]['is_valid']) {
                return
            }
        }

        quizWrapper.find('.growtype-quiz-nav .btn').attr('disabled', true)

        /**
         * Colect answers for existing questions
         */
        if (quizWrapper.find('.growtype-quiz-question.is-always-visible').length > 0) {
            quizWrapper.find('.growtype-quiz-question.is-always-visible').each(function (index, element) {
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
