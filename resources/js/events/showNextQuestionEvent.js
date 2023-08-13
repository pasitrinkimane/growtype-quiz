export function showNextQuestionEvent(params) {
    return new CustomEvent("growtypeQuizShowNextQuestion", {
        detail: params
    });
}
