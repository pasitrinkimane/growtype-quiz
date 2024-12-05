export function nextQuestionTriggerEvent(params) {
    return new CustomEvent("growtypeQuizNextQuestionTrigger", {
        detail: params
    });
}
