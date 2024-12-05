import {showNextQuestion} from "../actions/question/showNextQuestion";
import {collectQuizData} from "../actions/crud/collectQuizData";
import {validateQuestion} from "../listeners/validation/validateQuestion";
import {updateBgImage} from "../actions/question/updateBgImage";

export class answerTrigger {
    clickInit(answer) {
        /**
         * Prevent double click
         */
        if (window.showNextQuestionWasFired) {
            return;
        }

        let answersLimit = answer.closest('.growtype-quiz-question').attr('data-answers-limit');
        let answersAmount = answer.closest('.growtype-quiz-question').find('.growtype-quiz-question-answer.is-active').length;

        if (answer.attr('data-url').length > 0) {
            window.location = answer.attr('data-url');
        }

        let answerType = answer.closest('.growtype-quiz-question').attr('data-answer-type');
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
            if (answersLimit > 0 && parseInt(answersAmount) === parseInt(answersLimit)) {
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
            $('.growtype-quiz-nav .growtype-quiz-btn-go-next').show();
            event.stopPropagation();
            return;
        }

        if (
            answerType === 'single_instant'
            ||
            answer.hasClass('clear-other-selections') && answer.find('.input-wrapper').length === 0
        ) {
            let currentQuestion = answer.closest('.growtype-quiz-question')
            collectQuizData(currentQuestion);
            showNextQuestion(currentQuestion);
            return;
        } else if (answerType === 'multiple') {
            /**
             * Validate question
             */
            validateQuestion($(this));
        }
    }

    init(trigger = $('.growtype-quiz-question-answers .growtype-quiz-question-answer')) {
        trigger.click(function () {
            new answerTrigger().clickInit($(this));

            if ($(this).attr('data-option-featured-img-main') === 'true') {
                updateBgImage($(this));
            }
        });
    }
}
