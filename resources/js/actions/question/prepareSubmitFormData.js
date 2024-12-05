export function prepareSubmitFormData(answers, extraDetails = null) {
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

    let existingToken = window.growtype_quiz_local.token ? window.growtype_quiz_local.token : localStorage.growtype_quiz_unique_hash ?? null;

    if (existingToken) {
        formData.append("token", existingToken);
    }

    if (files) {
        for (var pair of files.entries()) {
            formData.append(pair[0], pair[1]);
        }
    }

    return formData;
}
