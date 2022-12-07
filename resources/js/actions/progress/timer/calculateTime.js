import {showSuccessQuestion} from "../../../events/showSuccessQuestion";
import {saveQuizDataEvent} from "../../../events/saveQuizData";

/**
 * Calculate time
 */
export function calculateTime() {
    let timer = $('.growtype-quiz-timer');

    if (timer.length === 0) {
        return false;
    }

    let durationInSeconds = timer.attr('data-duration');
    let currentTime = new Date();

    currentTime.setSeconds(currentTime.getSeconds() + Number(durationInSeconds));

    let countDownDate = currentTime.getTime();

    window.countDownTimer = setInterval(function () {
        let now = new Date().getTime();
        let distance = countDownDate - now;
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);

        if (distance < 0) {
            clearInterval(window.countDownTimer);
            document.dispatchEvent(showSuccessQuestion());
            document.dispatchEvent(saveQuizDataEvent());
        } else {
            window.growtype_quiz.countDownTimerDuration = Number(durationInSeconds) - Number(((minutes * 60) + seconds));

            /**
             * Format time
             */
            if (minutes < 10) {
                minutes = '0' + minutes;
            }

            if (seconds < 10) {
                seconds = '0' + seconds;
            }

            window.countDownTimerCurrentTime = minutes + ":" + seconds;

            timer.find('.e-time').text(window.countDownTimerCurrentTime);
        }
    }, 1000);
}
