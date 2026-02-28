import { loaderFinishedEvent } from "../../../events/loaderFinishedEvent";
import { loaderStartedEvent } from "../../../events/loaderStartedEvent";
import { hideProgressIndicators } from "../general";

export function loader(quizWrapper) {
    let visibleLoader = quizWrapper.find('.growtype-quiz-loader-wrapper:visible');

    if (visibleLoader.length > 0) {
        let count = 0;
        let duration = parseInt(visibleLoader.attr('data-duration'));
        if (isNaN(duration)) duration = 90;

        /**
         * Hide progress indicators
         */
        hideProgressIndicators(quizWrapper);

        /**
         * Reset
         */
        visibleLoader.find('.growtype-quiz-loader-bar-inner').css('width', '');
        visibleLoader.find('circle.bar--animated').removeAttr('style');

        setTimeout(function () {
            document.dispatchEvent(loaderStartedEvent({
                'loader': visibleLoader,
            }))
            startLoader();
        }, 500)

        function startLoader() {
            visibleLoader.find('.growtype-quiz-loader-bar-inner').css('width', count + '%');
            visibleLoader.find('.growtype-quiz-loader-percentage').html(count + "<span>%</span>");

            /**
             * Update radial progress
             */
            visibleLoader.find('svg.radial-progress').each(function () {
                var radius = $(this).find('circle.bar--animated').attr('r');
                var circumference = 2 * Math.PI * radius;
                var strokeDashOffset = circumference - ((count * circumference) / 100);
                $(this).find('circle.bar--animated').css('stroke-dashoffset', strokeDashOffset);
            });

            /**
             * Update counter text
             */
            visibleLoader.find('.countervalue').text(count + '%');

            updateLoader();
        }

        function updateLoader() {
            if (count < 100) {
                /**
                 * If redirect is active, pause at 99% until redirectUrl is set (by saveQuizDataListener)
                 */
                if (count === 99) {
                    let redirect = visibleLoader.attr('data-redirect');
                    let redirectUrl = visibleLoader.attr('data-redirect-url');

                    if (redirect === 'true' && (!redirectUrl || redirectUrl.length === 0)) {
                        setTimeout(updateLoader, 200);
                        return;
                    }
                }

                count++;
                setTimeout(startLoader, duration);
            } else {
                /**
                 * Dispatch event
                 */
                document.dispatchEvent(loaderFinishedEvent({
                    'loader': visibleLoader,
                }))
            }
        }
    }
}
