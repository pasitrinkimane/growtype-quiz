import {showLastQuestion} from "../question/showLastQuestion";

document.addEventListener('saveQuizData', saveQuizData)

function saveQuizData(data) {
    if (growtype_quiz_local.save_answers === 'false') {
        return false;
    }

    const answers = data.answers && Object.entries(data.answers).length > 0 ? data.answers : null;
    const extraDetails = data.extra_details && Object.entries(data.extra_details).length > 0 ? data.extra_details : null;
    const showLastQuestionOnError = false;
    const duration = window.growtype_quiz_global.duration ?? null;
    const quizId = $('.growtype-quiz-wrapper').attr('data-quiz-id');
    const files = window.growtype_quiz_global.files ?? null;

    let formData = new FormData();
    formData.append("action", "growtype_quiz_save_data");
    formData.append("answers", JSON.stringify(answers));

    if (extraDetails) {
        formData.append("extra_details", JSON.stringify(extraDetails));
    }

    formData.append("quiz_id", quizId);
    formData.append("duration", duration);
    formData.append("unique_hash", window.growtype_quiz_local.unique_hash);

    if (files) {
        for (var pair of files.entries()) {
            formData.append(pair[0], pair[1]);
        }
    }

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
                 * Set unique hash if input exists
                 */
                $('input[name="growtype_quiz_unique_hash"]').val(data.unique_hash);

                /**
                 * Save data to local storage
                 */
                localStorage.setItem("quiz_answers", JSON.stringify(answers));

                /**
                 * Redirect
                 */
                if (data.redirect_url !== null && data.redirect_url.length > 0) {
                    window.location.replace(data.redirect_url);
                }

                /**
                 * Update loader
                 */
                if ($('.growtype-quiz-loader-wrapper').length > 0) {
                    $('.growtype-quiz-loader-wrapper').attr('data-redirect-url', data.results_url);
                    $('.growtype-quiz-loader-wrapper .btn-continue').attr('href', data.results_url);
                }
            } else {
                if (showLastQuestionOnError) {
                    showLastQuestion(answers);
                }
            }
        },
        error: function (data) {
            if (showLastQuestionOnError) {
                showLastQuestion(answers, false);
            } else if (data['responseJSON'] && data['responseJSON']['message'] !== undefined) {
                console.error(data['responseJSON']['message']);
            }

            if ($('.growtype-quiz-question.is-active').attr('data-hide-back-button') !== 'true') {
                $('.growtype-quiz-wrapper .btn').attr('disabled', false).fadeIn();
            }
        },
        complete: function () {
        },
    })
}

