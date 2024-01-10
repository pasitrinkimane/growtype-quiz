import {showNextQuestion} from "../actions/question/showNextQuestion";
import {collectQuizData} from "../actions/crud/collectQuizData";
import {validateQuestion} from "../listeners/validation/validateQuestion";

export class answerTrigger {
    clickInit(answer) {
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
            validateQuestion();
        }
    }

    init() {
        $('.growtype-quiz-question-answers .growtype-quiz-question-answer').click(function () {
            new answerTrigger().clickInit($(this));
        });

        $('.growtype-quiz-question-answers .growtype-quiz-question-answer[data-option-featured-img-main="true"]').click(function () {
            var imgUrl = $(this).attr('data-img-url');
            let imageHolder = $(this).closest('.growtype-quiz-question').find('.b-img .e-img')
            let currentImgUrl = imageHolder.css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');

            if (imgUrl.length > 0 && currentImgUrl !== imgUrl) {
                imageHolder.fadeOut(100).promise().done(function () {
                    imageHolder.css({
                        "background-image": "url( " + imgUrl + " )"
                    })
                    imageHolder.fadeIn(100);
                })
            }
        });
    }
}
