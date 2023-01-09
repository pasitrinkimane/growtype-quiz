import {showSuccessQuestion} from "../../../events/showSuccessQuestion";
import {saveQuizDataEvent} from "../../../events/saveQuizData";

/**
 * Calculate time
 */
export function countDownTimer() {
    let timer = $('.growtype-quiz-timer');

    if (timer.length === 0) {
        return false;
    }

    window.growtype_quiz.countdown = {};

    let durationInSeconds = timer.attr('data-duration');
    let currentTime = new Date();

    currentTime.setSeconds(currentTime.getSeconds() + Number(durationInSeconds));

    let countDownDate = currentTime.getTime();

    window.countdown_timer = setInterval(function () {
        let now = new Date().getTime();
        let distance = countDownDate - now;
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let minutesFormatted = minutes;
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
        let secondsFormatted = seconds;

        if (distance < 0) {
            clearInterval(window.countdown_timer);
            document.dispatchEvent(showSuccessQuestion());
            document.dispatchEvent(saveQuizDataEvent());
        } else {
            window.growtype_quiz.countdown.duration = Number(durationInSeconds) - Number(((minutes * 60) + seconds));

            /**
             * Format time
             */
            if (minutes < 10) {
                minutesFormatted = '0' + minutes;
            }

            if (seconds < 10) {
                secondsFormatted = '0' + seconds;
            }

            window.growtype_quiz.countdown.current_time = minutesFormatted + ":" + secondsFormatted;

            timer.find('.e-time').text(window.growtype_quiz.countdown.current_time);
        }
    }, 1000);
}
