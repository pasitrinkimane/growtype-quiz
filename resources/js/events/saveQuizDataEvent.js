let saveQuizData = new Event('saveQuizData');
saveQuizData.answers = {};
saveQuizData.correctlyAnswered = {};

export function saveQuizDataEvent() {
    return saveQuizData;
}
