export function loaderFinishedEvent(params) {
    return new CustomEvent("growtypeQuizLoaderFinished", {
        detail: params
    });
}
