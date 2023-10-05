if (!window.growtype_quiz_data) {
    window.growtype_quiz_data = new Event('saveQuizData');
    window.growtype_quiz_data.answers = sessionStorage.getItem('growtype_quiz_answers') === null ? {} : JSON.parse(sessionStorage.getItem('growtype_quiz_answers'));
    window.growtype_quiz_data.correctlyAnswered = {};
    window.growtype_quiz_data.extra_details = {};
}

export function saveQuizDataEvent() {
    return window.growtype_quiz_data;
}
