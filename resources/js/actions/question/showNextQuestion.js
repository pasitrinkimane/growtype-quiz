import {updateProgressCounter} from "../../actions/progress/counter/updateProgressCounter.js";
import {updateQuestionsCounter} from "../../actions/progress/counter/updateQuestionsCounter.js";
import {updateProgressBar} from "../../actions/progress/bar/updateProgressBar";
import {saveQuizDataEvent} from "../../events/saveQuizDataEvent";
import {disabledValueIsIncluded, showProgressIndicators} from "../../actions/progress/general";
import {updateQuizComponents} from "./updateQuizComponents";
import {showSuccessQuestionEvent} from "../../events/showSuccessQuestionEvent";
import {showNextQuestionEvent} from "../../events/showNextQuestionEvent";
import {validateQuestion} from "../../listeners/validation/validateQuestion";
import {answerTrigger} from "../../components/answerTrigger";
import {loader} from "../progress/loader/loader";
import {showQuestionEvent} from "../../events/showQuestionEvent";

/**
 * Show next slide
 */
export function showNextQuestion(currentQuestion) {
    let nextFunnel = currentQuestion.find('.growtype-quiz-question-answer.is-active').attr('data-funnel');
    let submitDelay = 0;

    window.growtype_quiz_global.current_funnel = nextFunnel;

    /**
     * If questions has multiple answers and funnels are enabled, adjust next question to follow answers funnels
     */
    if (currentQuestion.attr('data-answer-type') === 'multiple' && currentQuestion.attr('data-has-funnel') === 'true') {
        let selectedFunnels = [];
        let additionalQuestions = 0;
        currentQuestion.find('.growtype-quiz-question-answer.is-active').map(function (index, element) {
            if ($(element).attr('data-funnel') !== window.growtype_quiz_global.initial_funnel) {
                selectedFunnels.push($(element).attr('data-funnel'))
            }
        })

        if (selectedFunnels.length > 0) {
            let funnelQuestionsGrouped = [];
            selectedFunnels.map(function (funnelKey, funnelIndex) {
                let funnelQuestions = currentQuestion.nextAll('.growtype-quiz-question[data-has-funnel="true"][data-funnel-conditional="' + funnelKey + '"]')

                if (funnelQuestions.length !== 0) {
                    additionalQuestions += funnelQuestions.length;

                    funnelQuestions.map(function (funnelQuestionIndex, funnelQuestion) {

                        let adjustedFunnelKey = funnelKey

                        if (funnelQuestions.length === funnelQuestionIndex + 1) {
                            if (selectedFunnels.length === funnelIndex + 1) {
                                adjustedFunnelKey = window.growtype_quiz_global.initial_funnel

                                console.info('Last same funnel conditional question has answer with next funnel - ' + adjustedFunnelKey)
                            } else {
                                adjustedFunnelKey = selectedFunnels[funnelIndex + 1]
                            }
                        }

                        let clonedQuestion = $(funnelQuestion).clone().addClass('is-conditionally-cloned')
                        $(funnelQuestion).addClass('is-conditionally-skipped')

                        $(clonedQuestion).attr('data-funnel', funnelKey)
                        $(clonedQuestion).find('.growtype-quiz-question-answer').attr('data-funnel', adjustedFunnelKey)

                        /**
                         * Init answer trigger
                         */
                        clonedQuestion.find('.growtype-quiz-question-answer').click(function () {
                            new answerTrigger().clickInit($(this));
                        });

                        if (!funnelQuestionsGrouped[funnelKey]) {
                            funnelQuestionsGrouped[funnelKey] = [clonedQuestion]
                        } else {
                            funnelQuestionsGrouped[funnelKey].push(clonedQuestion)
                        }
                    })
                }
            });

            let funnelQuestionsGroupedOrdered = [];
            Object.entries(funnelQuestionsGrouped).map(function (funnelQuestionsGroup) {
                funnelQuestionsGroup[1].map(function (funnelQuestion) {
                    funnelQuestionsGroupedOrdered.push(funnelQuestion)
                })
            })

            if (funnelQuestionsGroupedOrdered.length > 0) {
                nextFunnel = funnelQuestionsGroupedOrdered[0].attr('data-funnel');
            }

            funnelQuestionsGroupedOrdered.reverse();

            funnelQuestionsGroupedOrdered.map(function (funnelQuestion) {
                $(funnelQuestion).insertAfter(currentQuestion)
            });
        }

        window.growtype_quiz_global.additional_questions_amount = additionalQuestions;
    }

    if (nextFunnel === undefined) {
        nextFunnel = window.growtype_quiz_global.initial_funnel;
    }

    let nextQuestion = currentQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:not([class*="skipped"]):first');

    /**
     * Check if next question is disabled
     */
    if (nextQuestion.attr('data-disabled-if') && nextQuestion.attr('data-disabled-if').length > 0) {
        let availableQuestions = currentQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:not([class*="skipped"])');

        for (var i = 0; i < availableQuestions.length; i++) {
            let question = availableQuestions[i];
            let disabledIf = $(question).attr('data-disabled-if')

            if (disabledIf.length > 0) {
                if (!disabledValueIsIncluded(disabledIf)) {
                    console.warn('Question found among answers but ignored answers not found.')
                    nextQuestion = $(question);
                    break;
                } else {
                    console.warn('Question key not found among answers')
                }
            } else {
                nextQuestion = $(question);
                console.warn('Next question "Disabled If" value is empty, so next question is taken.')
                break;
            }
        }
    }

    window.growtype_quiz_global.already_visited_questions_keys.push(currentQuestion.attr('data-key'))
    window.growtype_quiz_global.already_visited_questions_funnels.push(currentQuestion.attr('data-funnel'))

    window.quizLastQuestion = currentQuestion;

    if ($(currentQuestion).attr('data-question-type') !== 'info') {
        window.growtype_quiz_global.current_question_counter_nr++;
    }

    window.growtype_quiz_global.current_question_nr = nextQuestion.attr('data-question-nr');

    showProgressIndicators();

    /**
     * Show correct answer
     */
    if (growtype_quiz_local.show_correct_answer && growtype_quiz_local.correct_answer_trigger === 'after_submit') {
        submitDelay = 1000;
        currentQuestion.find('.growtype-quiz-question-answer').map(function (index, element) {
            if ($(element).attr('data-cor') !== '1') {
                $(element).addClass('is-wrong')
            } else {
                $(element).addClass('is-correct')
            }
        });
    }

    /**
     * Show new question
     */
    currentQuestion.delay(submitDelay).removeClass('is-active').not('.is-always-visible').fadeOut(300, function () {
        $('.growtype-quiz-wrapper').removeClass('is-valid is-half-valid');
    }).promise().done(function () {

        /**
         * Check if success page event was fired and quiz is finished
         */
        if (window.growtype_quiz_global.is_finished) {
            return;
        }

        /**
         * Change next label
         */
        let finishLabel = $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label-finish');

        if (parseInt(window.growtype_quiz_global.current_question_nr) === parseInt(window.quizQuestionsAmount) && finishLabel.length > 0) {
            $(this).closest('.growtype-quiz').find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(finishLabel);
        }

        /**
         * Reset next btn label
         */
        if (window.growtype_quiz_global.current_question_nr < window.quizQuestionsAmount - 1) {
            let nextLabel = $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label');

            let nextQuestionTitle = nextQuestion.nextAll('.growtype-quiz-question:first').attr('data-question-title');

            if (nextQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first').length > 0) {
                nextQuestionTitle = nextQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first').attr('data-question-title');
            }

            if ($('.growtype-quiz-nav').attr('data-question-title-nav') === 'true' && nextQuestionTitle.length > 0) {
                nextLabel = nextQuestionTitle;
            }

            $('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label', nextLabel).text(nextLabel);
        }

        if (nextQuestion.length > 0) {
            updateQuestionsCounter(nextQuestion);
            updateProgressCounter();
            updateProgressBar();
            nextQuestion.addClass('is-active').fadeIn(300).promise().done(function () {

                /**
                 * Validate question if it was filled already
                 */
                if (nextQuestion.find('input').val() !== undefined && nextQuestion.find('input').val().length > 0) {
                    validateQuestion();
                }

                $('.growtype-quiz-nav .btn').attr('disabled', false);
            });
        }

        updateQuizComponents(nextQuestion);

        loader();

        if (nextQuestion.length === 0 || nextQuestion.attr('data-question-type') === 'success') {
            $('.growtype-quiz-btn-go-back').attr('disabled', false).hide();
            $('.growtype-quiz-btn-go-next').hide();
            document.dispatchEvent(saveQuizDataEvent());
            document.dispatchEvent(showSuccessQuestionEvent());
        } else {
            /**
             * Show question general event
             */
            document.dispatchEvent(showQuestionEvent({
                currentQuestion: nextQuestion,
                previousQuestion: currentQuestion,
            }));

            /**
             * Show next question
             */
            document.dispatchEvent(showNextQuestionEvent({
                currentQuestion: currentQuestion,
                nextQuestion: nextQuestion,
            }));
        }
    });
}
