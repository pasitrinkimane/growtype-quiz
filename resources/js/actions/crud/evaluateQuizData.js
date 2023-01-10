import {saveQuizDataEvent} from "../../events/saveQuizDataEvent";
import {resultsEvaluatedEvent} from "../../events/resultsEvaluatedEvent";

export function evaluateQuizData() {
    let results = {
        'answers': saveQuizDataEvent().answers,
        'correctlyAnswered': saveQuizDataEvent().correctlyAnswered,
        'totalAnswers': Object.values(saveQuizDataEvent().correctlyAnswered).length,
        'correctAnswers': 0
    }

    Object.values(results['correctlyAnswered']).map(function (element, index) {
        if (element[0]) {
            results['correctAnswers']++;
        }
    });

    results['correctAnswersFormatted'] = results['correctAnswers'];

    if (Object.values(results['correctlyAnswered']).length >= 10 && results['correctAnswers'].length < 10) {
        results['correctAnswersFormatted'] = '0' + results['correctAnswers'];
    }

    let correctAnswersResultFormatted = results['correctAnswersFormatted'] + '/' + results['totalAnswers'];

    if ($('.growtype-quiz-question[data-question-type="success"] .e-result').length > 0) {
        $('.growtype-quiz-question[data-question-type="success"] .e-result').text(correctAnswersResultFormatted);
    }

    /**
     * Include results to event
     */
    resultsEvaluatedEvent().results = results;

    document.dispatchEvent(resultsEvaluatedEvent());
}
