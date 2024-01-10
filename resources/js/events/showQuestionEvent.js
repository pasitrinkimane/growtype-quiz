export function showQuestionEvent(params) {
    return new CustomEvent("growtypeQuizShowQuestion", {
        detail: params
    });
}
