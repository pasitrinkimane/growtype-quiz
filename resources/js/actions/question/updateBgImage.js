/**
 * Update bg image
 */
export function updateBgImage(answer) {
    var imgUrl = answer.attr('data-img-url');

    if (imgUrl.length > 0) {
        let imageHolder = answer.closest('.growtype-quiz-question').find('.b-img .e-img')
        let currentImgUrl = imageHolder.css('background-image').replace(/^url\(['"](.+)['"]\)/, '$1');

        if (currentImgUrl !== imgUrl) {
            imageHolder.fadeOut(100).promise().done(function () {
                imageHolder.css({
                    "background-image": "url( " + imgUrl + " )"
                })
                imageHolder.fadeIn(100);
            })
        }
    }
}
