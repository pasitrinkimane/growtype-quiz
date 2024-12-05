export function saveQuizDataEvent(params) {
    return new CustomEvent("growtypeQuizSaveQuizData", {
        detail: params
    });
}
