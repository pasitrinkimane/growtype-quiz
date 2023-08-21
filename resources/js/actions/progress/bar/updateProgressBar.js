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
    let progressbarStep = progressbarWidth / (window.quizCountedQuestionsAmount);
    progressbarIndicatorWidth = window.growtype_quiz_global.current_question_counter_nr * progressbarStep;

    $('.growtype-quiz-progressbar-inner').width(progressbarIndicatorWidth);
}
