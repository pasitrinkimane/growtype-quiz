export function validateQuestionEvent(params) {
    return new CustomEvent("growtypeQuizValidateQuestion", {
        detail: params
    });
}
