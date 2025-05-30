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
import {textareaCharactersCounter} from "../../components/textareaCharactersCounter";
import {input} from "../../components/input";
import {getQuizData} from "../../helpers/getQuizData";

/**
 * Show next question
 * @param currentQuestion
 */
export function showNextQuestion(currentQuestion) {
    let quizWrapper = currentQuestion.closest('.growtype-quiz-wrapper');
    let quizId = quizWrapper.attr('id');

    /**
     * Prevent double click
     */
    if (window.growtype_quiz_global[quizId]['showNextQuestionWasFired']) {
        return;
    }

    /**
     * Check if answer has additional question, and show it
     */
    let additionalQuestionContent = '';
    currentQuestion.find('.growtype-quiz-question-answer.is-active').map(function (index, element) {
        if ($(element).attr('data-additional-question') === 'true' && !$(element).hasClass('skip-additional-question')) {
            additionalQuestionContent = $(element).attr('data-additional-question-content');
            return;
        }
    });

    if (additionalQuestionContent.length > 0) {
        quizWrapper.find('.growtype-quiz-modal .growtype-quiz-modal-content-form').html(additionalQuestionContent);
        quizWrapper.find('.growtype-quiz-modal').fadeIn();
        textareaCharactersCounter();
        input();
        return;
    }

    window.growtype_quiz_global[quizId]['showNextQuestionWasFired'] = true;

    let nextFunnel = currentQuestion.find('.growtype-quiz-question-answer.is-active').attr('data-funnel');
    let submitDelay = 0;

    window.growtype_quiz_global[quizId]['current_funnel'] = nextFunnel;

    /**
     * If questions has multiple answers and funnels are enabled, adjust next question to follow answers funnels
     */
    if (currentQuestion.attr('data-answer-type') === 'multiple' && currentQuestion.attr('data-has-funnel') === 'true') {
        let selectedFunnels = [];
        let additionalQuestions = 0;
        currentQuestion.find('.growtype-quiz-question-answer.is-active').map(function (index, element) {
            if ($(element).attr('data-funnel') !== window.growtype_quiz_global[quizId]['initial_funnel']) {
                selectedFunnels.push($(element).attr('data-funnel'))
            }
        })

        if (selectedFunnels.length > 0) {
            let funnelQuestionsGrouped = [];
            selectedFunnels.map(function (funnelKey, funnelIndex) {
                let funnelQuestions = currentQuestion.nextAll('.growtype-quiz-question[data-has-funnel="true"][data-funnel-conditional="' + funnelKey + '"]');

                if (funnelQuestions.length !== 0) {
                    additionalQuestions += funnelQuestions.length;

                    funnelQuestions.map(function (funnelQuestionIndex, funnelQuestion) {
                        let adjustedFunnelKey = funnelKey

                        if (funnelQuestions.length === funnelQuestionIndex + 1) {
                            if (selectedFunnels.length === funnelIndex + 1) {
                                adjustedFunnelKey = window.growtype_quiz_global[quizId]['initial_funnel']

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

        window.growtype_quiz_global[quizId]['additional_questions_amount'] = additionalQuestions;
    }

    if (nextFunnel === undefined) {
        nextFunnel = window.growtype_quiz_global[quizId]['initial_funnel'];
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
                if (!disabledValueIsIncluded(quizWrapper, disabledIf)) {
                    nextQuestion = $(question);
                    console.warn('Question found among answers but ignored answers not found.')
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

    window.growtype_quiz_global[quizId]['already_visited_questions_keys'].push(currentQuestion.attr('data-key'))
    window.growtype_quiz_global[quizId]['already_visited_questions_funnels'].push(currentQuestion.attr('data-funnel'))

    window.quizLastQuestion = currentQuestion;

    if ($(currentQuestion).attr('data-question-type') !== 'info') {
        window.growtype_quiz_global[quizId]['current_question_counter_nr']++;
    }

    window.growtype_quiz_global[quizId]['current_question_nr'] = nextQuestion.attr('data-question-nr');

    showProgressIndicators(quizWrapper);

    /**
     * Show correct answer
     */
    if (quizWrapper.find('.growtype-quiz').attr('data-show-correct-answer') && quizWrapper.find('.growtype-quiz').attr('data-correct-answers-trigger') === 'after_submit') {
        submitDelay = 1000;
        currentQuestion.find('.growtype-quiz-question-answer').map(function (index, element) {
            if ($(element).attr('data-cor') !== '1') {
                $(element).addClass('is-wrong')
            } else {
                $(element).addClass('is-correct')
            }
        });
    }

    let currentQuestionHeight = $(currentQuestion).height();

    /**
     * Show next question
     */
    currentQuestion.delay(submitDelay).removeClass('is-active').not('.is-always-visible').fadeOut(300, function () {
        quizWrapper.removeClass('is-valid is-half-valid');
    }).promise().done(function () {
        /**
         * Check if success page event was fired and quiz is finished
         */
        if (window.growtype_quiz_global[quizId]['is_finished']) {
            return;
        }

        /**
         * Update next btn label
         */
        let finishLabel = quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label-finish');

        if (finishLabel && finishLabel.length > 0 && (nextQuestion.next('.growtype-quiz-question') === undefined || nextQuestion.attr('data-question-type') === 'success')) {
            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').text(finishLabel);
        } else if (parseInt(window.growtype_quiz_global[quizId]['current_question_counter_nr']) <= window.growtype_quiz_global[quizId]['quiz_questions_amount'] - 1) {
            let nextLabel = quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label');

            if (quizWrapper.find('.growtype-quiz-nav[data-type="footer"]').attr('data-question-title-nav') === 'true') {
                let nextQuestionTitle = nextQuestion.nextAll('.growtype-quiz-question:first').attr('data-question-title');

                if (nextQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first').length > 0) {
                    nextQuestionTitle = nextQuestion.nextAll('.growtype-quiz-question[data-funnel="' + nextFunnel + '"]:first').attr('data-question-title');
                }

                if (nextQuestionTitle && nextQuestionTitle.length > 0) {
                    nextLabel = nextQuestionTitle;
                }
            }

            quizWrapper.find('.growtype-quiz-nav .growtype-quiz-btn-go-next .e-label').attr('data-label', nextLabel).text(nextLabel);
        }

        /**
         * Update components
         */
        if (nextQuestion.length > 0) {
            nextQuestion.addClass('is-active').fadeIn(300).promise().done(function () {
                updateQuestionsCounter(quizWrapper, nextQuestion);
                updateProgressCounter(quizWrapper);
                updateProgressBar(quizWrapper);

                /**
                 * Validate question if it was filled already
                 */
                if (nextQuestion.find('input').val() !== undefined && nextQuestion.find('input').val().length > 0) {
                    validateQuestion($(this));
                }

                quizWrapper.find('.growtype-quiz-nav .btn').attr('disabled', false);
            });
        }

        updateQuizComponents(nextQuestion);

        loader(quizWrapper);

        /**
         * Show question general event
         */
        document.dispatchEvent(showQuestionEvent({
            currentQuestion: nextQuestion, previousQuestion: currentQuestion, answer_details: {
                'question_number': window.growtype_quiz_global[quizId]['current_question_counter_nr'] - 1,
                'question': $(currentQuestion).attr('data-key'),
                'answer': window.growtype_quiz_data[quizId]['answers'][$(currentQuestion).attr('data-key')],
            }
        }));

        if (currentQuestionHeight > $(window).height()) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $(".growtype-quiz").offset().top
            }, 100);
        }

        if (nextQuestion.length === 0 || nextQuestion.attr('data-question-type') === 'success') {

            /**
             * Save quiz data event
             */
            document.dispatchEvent(saveQuizDataEvent(getQuizData(quizId)));

            /**
             * Show success question event
             */
            document.dispatchEvent(showSuccessQuestionEvent({
                currentQuestion: currentQuestion,
                nextQuestion: nextQuestion
            }));
        } else {
            /**
             * Show next question event
             */
            document.dispatchEvent(showNextQuestionEvent({
                currentQuestion: currentQuestion,
                nextQuestion: nextQuestion
            }));
        }

        window.growtype_quiz_global[quizId]['showNextQuestionWasFired'] = false;
    });
}
