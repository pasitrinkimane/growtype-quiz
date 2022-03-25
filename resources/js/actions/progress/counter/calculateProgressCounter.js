/**
 * Calculate slides amount
 */
function calculateProgressCounter(slide, increase = true) {

    if (increase) {
        window.funnelsVisited.push(slide.attr('data-funnel'));
        window.keysVisited.push(slide.attr('data-key'));
    } else {
        window.funnelsVisited.pop(slide.attr('data-funnel'));
        window.keysVisited.pop(slide.attr('data-key'));
    }

    var filteredFunnels = window.funnelsVisited.filter(function (value, index, self) {
        return self.indexOf(value) === index;
    })

    /**
     * Always include initially counted funnels
     */
    window.initiallyCountedFunnels.map(function (value) {
        if (!filteredFunnels.includes(value)) {
            filteredFunnels.push(value)
        }
    })
}
