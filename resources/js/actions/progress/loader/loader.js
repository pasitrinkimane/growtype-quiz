import {loaderFinishedEvent} from "../../../events/loaderFinishedEvent";
import {loaderStartedEvent} from "../../../events/loaderStartedEvent";

export function loader() {
    let visibleLoader = $('.growtype-quiz-loader-wrapper:visible');

    if (visibleLoader.length > 0) {
        let count = 0;
        let duration = visibleLoader.attr('data-duration');

        /**
         * Reset
         */
        visibleLoader.find('.growtype-quiz-loader-bar-inner').css('width', '');

        setTimeout(function () {
            document.dispatchEvent(loaderStartedEvent({
                'loader': visibleLoader,
            }))
            startLoader();
        }, 500)

        function startLoader() {
            visibleLoader.find('.growtype-quiz-loader-bar-inner').css('width', count + '%');
            visibleLoader.find('.growtype-quiz-loader-percentage').html(count + "<span>%</span>");
            updateLoader();
        }

        function updateLoader() {
            if (count < 100) {
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

        function radial_animate() {
            visibleLoader.find('svg.radial-progress').each(function (index, value) {
                $(this).find($('circle.bar--animated')).removeAttr('style');

                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();

                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    var percent = $(value).data('countervalue');
                    var radius = $(this).find($('circle.bar--animated')).attr('r');
                    var circumference = 2 * Math.PI * radius;
                    var strokeDashOffset = circumference - ((percent * circumference) / 100);
                    $(this).find($('circle.bar--animated')).animate({'stroke-dashoffset': strokeDashOffset}, duration * 100);
                }
            });
        }

        function check_if_in_view() {
            visibleLoader.find('.countervalue').each(function () {
                if ($(this).hasClass('start')) {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();

                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();

                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        $(this).removeClass('start');
                        $('.countervalue').text();
                        var myNumbers = $(this).text();
                        if (myNumbers == Math.floor(myNumbers)) {
                            $(this).animate({
                                Counter: $(this).text()
                            }, {
                                duration: duration * 100,
                                easing: 'swing',
                                step: function (now) {
                                    $(this).text(Math.ceil(now) + '%');
                                }
                            });
                        } else {
                            $(this).animate({
                                Counter: $(this).text()
                            }, {
                                duration: duration * 100,
                                easing: 'swing',
                                step: function (now) {
                                    $(this).text(now.toFixed(2) + '$');
                                }
                            });
                        }

                        radial_animate();
                    }
                }
            });
        }

        check_if_in_view();
    }
}
