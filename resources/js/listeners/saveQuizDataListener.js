import {showLastQuestion} from "../actions/question/showLastQuestion";
import {prepareSubmitFormData} from "../actions/question/prepareSubmitFormData";

/**
 * Save quiz data
 */
document.addEventListener('growtypeQuizSaveQuizData', saveQuizDataListener)

function saveQuizDataListener(data) {
    if (!$('.growtype-quiz').attr('data-save-answers')) {
        return false;
    }

    const answers = data.detail.answers && Object.entries(data.detail.answers).length > 0 ? data.detail.answers : null;
    const extraDetails = data.detail.extra_details && Object.entries(data.detail.extra_details).length > 0 ? data.detail.extra_details : null;
    const showLastQuestionOnError = false;

    let formData = prepareSubmitFormData(answers, extraDetails)

    /**
     * Save extra data to answers
     */
    $.ajax({
        url: growtype_quiz_local.ajax_url,
        type: "post",
        processData: false,
        contentType: false,
        cache: false,
        enctype: 'multipart/form-data',
        data: formData,
        beforeSend: function () {
        },
        success: function (data) {
            if (data.success) {
                /**
                 * Save data to local storage
                 */
                localStorage.setItem("growtype_quiz_unique_hash", data.unique_hash);

                /**
                 * Set unique hash if input exists
                 */
                if ($('input[name="growtype_quiz_unique_hash"]').length > 0) {
                    $('input[name="growtype_quiz_unique_hash"]').val(data.unique_hash);
                }

                /**
                 * Save data to local storage
                 */
                localStorage.setItem("growtype_quiz_answers", JSON.stringify(answers));

                /**
                 * Redirect url
                 */
                let redirectUrl = data.redirect_url !== null && data.redirect_url.length > 0 ? data.redirect_url : data.results_url;

                /**
                 * Update loader
                 */
                setTimeout(function () {
                    if ($('.growtype-quiz-loader-wrapper:visible').length > 0) {
                        if (redirectUrl !== null && redirectUrl.length > 0) {
                            if ($('.growtype-quiz-loader-wrapper').attr('data-redirect-url').length === 0) {
                                $('.growtype-quiz-loader-wrapper').attr('data-redirect-url', redirectUrl);
                            }

                            $('.growtype-quiz-loader-wrapper .btn-continue').attr('href', redirectUrl);
                        }
                    } else {
                        if (redirectUrl !== null && redirectUrl.length > 0) {
                            window.location.replace(data.redirect_url);
                        }
                    }
                }, 500)
            } else {
                if (showLastQuestionOnError) {
                    showLastQuestion(answers);
                }
            }
        },
        error: function (data) {
            /**
             * Set quiz as not finished
             * @type {boolean}
             */
            window.growtype_quiz_global.is_finished = false;

            if (showLastQuestionOnError) {
                showLastQuestion(answers, false);
            } else if (data['responseJSON'] && data['responseJSON']['message'] !== undefined) {
                console.error(data['responseJSON']['message']);
            }

            if ($('.growtype-quiz-question.is-active').attr('data-hide-back-button') !== 'true') {
                $('.growtype-quiz-wrapper .btn').attr('disabled', false).fadeIn();
            }
        }
    })
}

