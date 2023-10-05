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
    let progressbarStepWidth = progressbarWidth / (window.quizCountedQuestionsAmount);
    progressbarIndicatorWidth = window.growtype_quiz_global.current_question_counter_nr * progressbarStepWidth;

    sessionStorage.setItem('growtype_quiz_global', JSON.stringify(window.growtype_quiz_global))

    $('.growtype-quiz-progressbar-inner').width(progressbarIndicatorWidth);

    evaluateChapters(progressbarStepWidth);
}

function evaluateChapters(progressbarStepWidth) {
    let chapters = $('.growtype-quiz-question.chapter-start').length

    $('.growtype-quiz-progressbar .growtype-quiz-progressbar-chapter').remove()

    if (chapters > 0) {
        let mainFunnelQuestionNr = 0;
        let mainFunnelQuestionSeparators = [];
        $('.growtype-quiz .growtype-quiz-question').each(function (index, element) {
            if (
                $(element).attr('data-question-type') !== 'info'
                &&
                (
                    $(element).attr('data-funnel') === window.growtype_quiz_global.initial_funnel ||
                    $(element).hasClass('is-conditionally-cloned')
                )
            ) {
                mainFunnelQuestionNr++;
            }

            if ($(element).hasClass('chapter-start')) {
                mainFunnelQuestionSeparators.push(mainFunnelQuestionNr)
                let chapterLength = progressbarStepWidth * mainFunnelQuestionNr;

                $('.growtype-quiz-progressbar').append('<span class="growtype-quiz-progressbar-chapter" style="left:' + chapterLength + 'px;"></span>')
            }
        })
    }
}
