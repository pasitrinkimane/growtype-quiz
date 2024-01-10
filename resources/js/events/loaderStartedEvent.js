export function loaderStartedEvent(params) {
    return new CustomEvent("growtypeQuizLoaderStarted", {
        detail: params
    });
}
