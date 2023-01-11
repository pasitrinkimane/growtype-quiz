/**
 * Calculate duration
 */
let initialDate = new Date();

export function duration() {

    /**
     * Check quiz type
     */
    if ($('.growtype-quiz-wrapper').attr('data-quiz-type') === 'presentation') {
        return false;
    }

    setInterval(function () {
        let currentDate = new Date();
        let durationInMilliseconds = currentDate - initialDate;
        window.growtype_quiz_global.duration = (durationInMilliseconds / 1000).toFixed(0);
    }, 1000);
}
