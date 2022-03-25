import {showLastQuestion} from "../question/showLastQuestion";

document.addEventListener('saveQuizData', saveQuizData)

function saveQuizData(data) {

    if (!quizSaveAnswers) {
        return false;
    }

    const answers = data.answers;
    const showLastQuestionOnError = false;
    const duration = window.countDownTimerDuration;

    /**
     * Save extra data to answers
     */
    $.ajax({
        url: '/wp/wp-admin/admin-ajax.php',
        type: "post",
        data: {
            action: 'quiz_data',
            status: 'save',
            answers: answers,
            quiz_id: quizId,
            duration: duration
        },
        beforeSend: function () {
        },
        success: function (data) {
            if (data.success) {
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
        error: function (xhr) {
            if (showLastQuestionOnError) {
                showLastQuestion(answers, false);
            }
        },
        complete: function () {
        },
    })
}

