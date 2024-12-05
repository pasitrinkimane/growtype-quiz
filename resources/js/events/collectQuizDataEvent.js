export function collectQuizDataEvent(params) {
    return new CustomEvent("growtypeQuizCollectQuizData", {
        detail: params
    });
}
