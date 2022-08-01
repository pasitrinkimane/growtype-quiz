import {saveQuizDataEvent} from "../../events/saveQuizData";

export function evaluateQuizData() {
    let answers = saveQuizDataEvent().answers;
    let correctlyAnswered = saveQuizDataEvent().correctlyAnswered;

    if (Object.entries(correctlyAnswered).length > 0 && $('.b-quiz-question[data-question-type="success"] .e-result').length > 0) {
        let correctAnswers = 0;
        Object.values(correctlyAnswered).map(function (element, index) {
            if (element[0]) {
                correctAnswers++;
            }
        });

        if (Object.values(correctlyAnswered).length >= 10 && correctAnswers.length < 10) {
            correctAnswers = '0' + correctAnswers;
        }

        let result = correctAnswers + '/' + Object.values(correctlyAnswered).length;

        $('.b-quiz-question[data-question-type="success"] .e-result').text(result);
    }
}
