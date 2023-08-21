import {showNextQuestion} from "../actions/question/showNextQuestion";
import {collectQuizData} from "../actions/crud/collectQuizData";
import {validateQuestion} from "../listeners/validation/validateQuestion";

export function answerTrigger() {
    $('.growtype-quiz-question-answers .growtype-quiz-question-answer').click(function () {
        let answersLimit = $(this).closest('.growtype-quiz-question').attr('data-answers-limit');
        let answersAmount = $(this).closest('.growtype-quiz-question').find('.growtype-quiz-question-answer.is-active').length;

        if ($(this).attr('data-url').length > 0) {
            window.location = $(this).attr('data-url');
        }

        let answerType = $(this).closest('.growtype-quiz-question').attr('data-answer-type');
        
        if (answerType !== 'multiple') {
            $(this).closest('.growtype-quiz-question-answers').find('.growtype-quiz-question-answer').removeClass('is-active');
        }

        if (!$(this).hasClass('is-active')) {
            if (answersLimit > 0 && parseInt(answersAmount) === parseInt(answersLimit)) {
                return;
            }
            $(this).addClass('is-active');
        } else {
            if (answerType === 'multiple') {
                $(this).removeClass('is-active');
            }
        }

        if (answerType === 'single_instant') {
            let currentQuestion = $(this).closest('.growtype-quiz-question')
            collectQuizData(currentQuestion);
            showNextQuestion(currentQuestion);
            return;
        } else if (answerType === 'multiple') {
            validateQuestion();
        }
    });

    /**
     * Featured img change on click
     */
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
