/**
 * Calculate duration
 */
let initialDate = new Date();

export function duration(quizWrapper) {
    let quizId = quizWrapper.attr('id');

    if (quizWrapper.attr('data-quiz-type') === 'presentation') {
        return false;
    }

    setInterval(function () {
        let currentDate = new Date();
        let durationInMilliseconds = currentDate - initialDate;
        window.growtype_quiz_global[quizId]['duration'] = (durationInMilliseconds / 1000).toFixed(0);
    }, 1000);
}
