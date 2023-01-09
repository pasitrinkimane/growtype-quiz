import {showLastQuestion} from "../question/showLastQuestion";

document.addEventListener('saveQuizData', saveQuizData)

function saveQuizData(data) {

    if (!quizSaveAnswers) {
        return false;
    }

    const answers = data.answers;
    const showLastQuestionOnError = false;
    const duration = window.growtype_quiz.duration ?? null;
    const quizId = $('.growtype-quiz-wrapper').attr('data-quiz-id');
    const files = window.growtype_quiz.files ?? null;

    let formData = new FormData();
    formData.append("action", "growtype_quiz_save_data");
    formData.append("status", "save");
    formData.append("answers", JSON.stringify(answers));
    formData.append("quiz_id", quizId);
    formData.append("duration", duration);

    if (files) {
        for (var pair of files.entries()) {
            formData.append(pair[0], pair[1]);
        }
    }

    /**
     * Save extra data to answers
     */
    $.ajax({
        url: ajax_object.ajaxurl,
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
            } else {
                if (showLastQuestionOnError) {
                    showLastQuestion(answers);
                }
            }
        },
        error: function (data) {
            if (showLastQuestionOnError) {
                showLastQuestion(answers, false);
            } else if (data['responseJSON']['message'] !== undefined) {
                alert(data['responseJSON']['message'])
            }

            $('.growtype-quiz-wrapper .btn').attr('disabled', false).fadeIn();
        },
        complete: function () {
        },
    })
}

