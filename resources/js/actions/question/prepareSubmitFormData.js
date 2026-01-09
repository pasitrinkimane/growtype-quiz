import { storage } from "../../helpers/storage";

export function prepareSubmitFormData(quizId, answers, extraDetails = null) {
    const quizWrapper = $('#' + quizId);
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

    let uniqueHash = (window.growtype_quiz_local && window.growtype_quiz_local.unique_hash) || storage.get("growtype_quiz_unique_hash");
    formData.append("unique_hash", uniqueHash);

    if (window.growtype_quiz_local && window.growtype_quiz_local.nonce) {
        formData.append("nonce", window.growtype_quiz_local.nonce);
    }

    let existingToken = (window.growtype_quiz_local && window.growtype_quiz_local.gqtoken) || storage.get("growtype_quiz_unique_hash") || null;
    if (existingToken) {
        formData.append("gqtoken", existingToken);
    }

    // Append files safely
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            formData.append(`file_${i}`, files[i]);
        }
    }

    return formData;
}
