export function showSuccessQuestionEvent(params = {}) {
    return new CustomEvent("growtypeQuizShowSuccessQuestion", {
        detail: params
    });
}
