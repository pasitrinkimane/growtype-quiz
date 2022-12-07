let progressbarIndicatorWidth = 0;

/**
 * Update progress bar
 */
export function updateProgressBar() {
    let progressBar = $('.growtype-quiz-progressbar');

    if (progressBar.length === 0) {
        return false;
    }

    let progressbarWidth = progressBar.width();
    let progressbarStep = progressbarWidth / (window.quizQuestionsAmount);
    progressbarIndicatorWidth = window.growtype_quiz.current_question_nr * progressbarStep;

    $('.growtype-quiz-progressbar-inner').width(progressbarIndicatorWidth);
}
