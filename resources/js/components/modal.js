export function modal(quizWrapper) {
    let quizId = quizWrapper.attr('id');

    /**
     * Close modal
     */
    quizWrapper.find('.growtype-quiz-modal .growtype-quiz-modal-inner .e-close').on('click', function () {
        $(this).closest('.growtype-quiz-modal').fadeOut();

        window.growtype_quiz_global[quizId]['showNextQuestionWasFired'] = false;

        quizWrapper.find('.growtype-quiz-btn-go-next').attr('disabled', false);
        quizWrapper.find('.growtype-quiz-btn-go-back').attr('disabled', false);
    });

    /**
     * Continue button
     */
    let modalBtnContinueTriggered = false;
    quizWrapper.find('.growtype-quiz-modal .growtype-quiz-modal-actions .btn-continue').on('click', function () {

        if (modalBtnContinueTriggered) {
            return;
        }

        modalBtnContinueTriggered = true;

        let additionalQuestionInput = $(this).closest('.growtype-quiz-modal-content').find('[name="additional_question"]');
        let activeQuestion = quizWrapper.find('.growtype-quiz-question.is-active');

        let additionalQuestionInputValue = additionalQuestionInput.val();

        setTimeout(function () {
            window.growtype_quiz_data[quizId]['answers'][activeQuestion.attr('data-key') + '_additional_question'] = [additionalQuestionInputValue];
        }, 100)

        /**
         * Append files
         */
        if (additionalQuestionInput[0] ?? '') {
            let additionalQuestionFiles = additionalQuestionInput[0].files;

            if (additionalQuestionFiles && additionalQuestionFiles.length > 0) {
                for (let i = 0; i < additionalQuestionFiles.length; i++) {
                    window.growtype_quiz_global[quizId]['files'].append(activeQuestion.attr('data-key') + '_additional_question_' + i, additionalQuestionFiles[i]);
                }
            }
        }

        $(this).closest('.growtype-quiz-modal').fadeOut();

        activeQuestion.find('.growtype-quiz-question-answer.is-active').addClass('skip-additional-question');

        window.growtype_quiz_global[quizId]['showNextQuestionWasFired'] = false;

        quizWrapper.find('.growtype-quiz-btn-go-next').attr('disabled', false);

        quizWrapper.find('.growtype-quiz-btn-go-next').click();

        setTimeout(function () {
            modalBtnContinueTriggered = false;
        }, 500);
    });
}
