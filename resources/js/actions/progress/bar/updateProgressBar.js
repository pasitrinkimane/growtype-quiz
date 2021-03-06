let progressbarIndicatorWidth = 0;

/**
 * Update progress bar
 */
export function updateProgressBar() {
    let progressBar = $('.b-quiz-progressbar');

    if (progressBar.length === 0) {
        return false;
    }

    let progressbarWidth = progressBar.width();
    let progressbarStep = progressbarWidth / (window.quizQuestionsAmount);
    progressbarIndicatorWidth = window.quizCurrentQuestionNr * progressbarStep;

    $('.b-quiz-progressbar-inner').width(progressbarIndicatorWidth);
}
