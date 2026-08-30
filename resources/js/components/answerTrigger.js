import { showNextQuestion } from "../actions/question/showNextQuestion";
import { collectQuizData } from "../actions/crud/collectQuizData";
import { validateQuestion } from "../listeners/validation/validateQuestion";
import { updateBgImage } from "../actions/question/updateBgImage";
import { getAttrValue } from "../helpers/attributes";

export class answerTrigger {
    clickInit(answer) {
        let quizWrapper = answer.closest('.growtype-quiz-wrapper');
        let quizId = quizWrapper.attr('id');
        let quizState = window.growtype_quiz_global && window.growtype_quiz_global[quizId];

        /**
         * Prevent double click
         */
        if (!quizState) {
            console.error('[growtype-quiz][answerTrigger] Missing quiz state for answer click.', {
                quizId: quizId,
                answerValue: getAttrValue(answer, 'data-value'),
                questionKey: getAttrValue(answer.closest('.growtype-quiz-question'), 'data-key')
            });
            return;
        }

        if (quizState['showNextQuestionWasFired']) {
            return;
        }

        let currentQuestion = answer.closest('.growtype-quiz-question')
        let answersLimit = currentQuestion.attr('data-answers-limit');
        let answersAmount = currentQuestion.find('.growtype-quiz-question-answer.is-active').length;
        let answerType = currentQuestion.attr('data-answer-type');
        let answerUrl = getAttrValue(answer, 'data-url');

        if (answerUrl.length > 0) {
            window.location = answerUrl;
            return;
        }

        if (answerType !== 'multiple' || answer.hasClass('clear-other-selections')) {
            answer.closest('.growtype-quiz-question-answers').find('.growtype-quiz-question-answer').removeClass('is-active');
        } else {
            answer.closest('.growtype-quiz-question-answers').find('.growtype-quiz-question-answer.clear-other-selections').removeClass('is-active');
        }

        /**
         * Hide additional answer input
         */
        answer.closest('.growtype-quiz-question-answers').find('.input-other').hide();

        if (!answer.hasClass('is-active')) {
            if (
                answersLimit > 0
                && parseInt(answersAmount) === parseInt(answersLimit)
                && !answer.hasClass('clear-other-selections')
            ) {
                return;
            }
            answer.addClass('is-active');
        } else {
            if (answerType === 'multiple') {
                answer.removeClass('is-active');
            }
        }

        /**
         * Other value input
         */
        if (answer.find('.input-other').length > 0) {
            answer.find('.input-other').show().find('input,textarea').removeClass('is-invalid').focus();
            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next').show();
            event.stopPropagation();
            return;
        }

        if (
            answerType === 'single_instant'
            ||
            answer.hasClass('clear-other-selections') && answer.find('.input-wrapper').length === 0
        ) {
            collectQuizData(currentQuestion);
            showNextQuestion(currentQuestion);
            return;
        } else if (answerType === 'multiple') {
            /**
             * Validate question
             */
            validateQuestion(answer);
        }
    }

    init(trigger = $('.growtype-quiz-question-answers .growtype-quiz-question-answer')) {
        trigger.off('click').on('click', function (event) {
            new answerTrigger().clickInit($(this));

            if ($(this).attr('data-option-featured-img-main') === 'true') {
                updateBgImage($(this));
            }
        });
    }
}
