import {loaderFinishedEvent} from "../../../events/loaderFinishedEvent";

export function loader() {
    if ($('.growtype-quiz-loader-wrapper').length > 0) {
        let count = 0;
        let duration = $('.growtype-quiz-loader-wrapper').attr('data-duration');

        startLoader();

        function startLoader() {
            $('.growtype-quiz-loader-percentage').html(count + "<span>%</span>");
            updateLoader();
        }

        function updateLoader() {
            if (count < 100) {
                count++;
                setTimeout(startLoader, duration);
            } else {
                document.dispatchEvent(loaderFinishedEvent())
            }
        }
    }
}
