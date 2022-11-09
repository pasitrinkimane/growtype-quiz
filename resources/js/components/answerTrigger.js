export function answerTrigger() {
    $('.b-quiz-question-answers .b-quiz-question-answer').click(function () {
        if ($(this).attr('data-url').length > 0) {
            window.location = $(this).attr('data-url');
        }

        if ($(this).closest('.b-quiz-question').attr('data-answer-type') !== 'multiple') {
            $(this).closest('.b-quiz-question-answers').find('.b-quiz-question-answer').removeClass('is-active');
        }

        if (!$(this).hasClass('is-active')) {
            $(this).addClass('is-active');
        } else {
            if ($(this).closest('.b-quiz-question').attr('data-answer-type') === 'multiple') {
                $(this).removeClass('is-active');
            }
        }
    });

    /**
     * F img change on click
     */
    $('.b-quiz-question-answers .b-quiz-question-answer[data-option-featured-img-main="true"]').click(function () {
        var imgUrl = $(this).attr('data-img-url');
        let imageHolder = $(this).closest('.b-quiz-question').find('.b-img .e-img')
        let currentImgUrl = imageHolder.css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');

        if (imgUrl.length > 0 && currentImgUrl !== imgUrl) {
            imageHolder.fadeOut(100).promise().done(function () {
                imageHolder.css({
                    "background-image": "url( " + imgUrl + " )"
                })
                imageHolder.fadeIn(100);
            })
        }
    });
}
