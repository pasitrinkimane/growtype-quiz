export function prepareSubmitFormData(answers, extraDetails = null) {
    const quizWrapper = $('.growtype-quiz-wrapper');
    const quizId = quizWrapper.attr('id');
    const quizPostId = quizWrapper.attr('data-quiz-post-id');
    const quizSlug = quizWrapper.attr('data-quiz-slug');
    const duration = window.growtype_quiz_global[quizId]?.duration ?? null;
    const rawFiles = window.growtype_quiz_global[quizId]?.files ?? [];

    // Ensure files is always an array or a FileList
    const files = rawFiles instanceof FileList ? rawFiles : (Array.isArray(rawFiles) ? rawFiles : []);

    let formData = new FormData();
    formData.append("action", "growtype_quiz_save_data");
    formData.append("answers", JSON.stringify(answers));

    if (extraDetails) {
        formData.append("extra_details", JSON.stringify(extraDetails));
    }

    formData.append("quiz_id", quizPostId);
    formData.append("quiz_slug", quizSlug);
    formData.append("duration", duration);
    formData.append("unique_hash", window.growtype_quiz_local?.unique_hash ?? '');

    let existingToken = window.growtype_quiz_local?.token || localStorage.growtype_quiz_unique_hash || null;
    if (existingToken) {
        formData.append("token", existingToken);
    }

    // Append files safely
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            formData.append(`file_${i}`, files[i]);
        }
    }

    return formData;
}
