if (!window.growtype_quiz_data) {
    window.growtype_quiz_data = {};
    window.growtype_quiz_data.answers = sessionStorage.getItem('growtype_quiz_answers') === null ? {} : JSON.parse(sessionStorage.getItem('growtype_quiz_answers'));
    window.growtype_quiz_data.correctly_answered = {};
    window.growtype_quiz_data.extra_details = {};
    window.growtype_quiz_data.quiz_id = jQuery('.growtype-quiz-wrapper').attr('data-quiz-id');
}

export function getQuizData() {
    return window.growtype_quiz_data;
}
