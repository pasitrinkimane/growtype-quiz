import {getQuizData} from "../../helpers/data";
import {resultsEvaluatedEvent} from "../../events/resultsEvaluatedEvent";

export function evaluateQuizData(quizWrapper) {
    let quizId = quizWrapper.attr('id');

    let results = {
        'answers': getQuizData(quizId)['answers'],
        'correctlyAnswered': typeof getQuizData(quizId)['correctly_answered'] !== 'undefined' ? getQuizData(quizId)['correctly_answered'] : 0,
        'totalAnswers': typeof getQuizData(quizId)['correctly_answered'] !== 'undefined' ? Object.values(getQuizData(quizId)['correctly_answered']).length : 0,
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

    if (quizWrapper.find('.growtype-quiz-question[data-question-type="success"] .e-result').length > 0) {
        quizWrapper.find('.growtype-quiz-question[data-question-type="success"] .e-result').text(correctAnswersResultFormatted);
    }

    /**
     * Include results to event
     */
    resultsEvaluatedEvent().results = results;

    document.dispatchEvent(resultsEvaluatedEvent());
}
