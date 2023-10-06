let progressbarIndicatorWidth = 0;
let progressbarStepWidth = 0;

/**
 * Update progress bar
 */
export function updateProgressBar() {
    let progressBar = $('.growtype-quiz-progressbar');
    let questionsAmount = window.quizCountedQuestionsAmount;
    let questionsCounterNr = window.growtype_quiz_global.current_question_counter_nr;
    let chapters = $('.growtype-quiz-question.chapter-start').length

    if (progressBar.length === 0 || questionsCounterNr === 0) {
        $('.growtype-quiz-progressbar-inner').width(0);
    }

    let progressbarWidth = progressBar.width();

    if (chapters > 0) {
        chapters = chapters + 1;

        $('.growtype-quiz-progressbar .growtype-quiz-progressbar-chapter').remove()

        let mainFunnelQuestionNr = 0;
        let separatorsStepSize = [];
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
                if (separatorsStepSize.length === 0) {
                    separatorsStepSize.push({
                        chapter_start: 0,
                        chapter_end: mainFunnelQuestionNr,
                        steps_difference: mainFunnelQuestionNr,
                    })
                } else {
                    separatorsStepSize.push({
                        chapter_start: separatorsStepSize[separatorsStepSize.length - 1]['chapter_end'],
                        chapter_end: mainFunnelQuestionNr,
                        steps_difference: mainFunnelQuestionNr - separatorsStepSize[separatorsStepSize.length - 1]['chapter_end'],
                    })
                }
            }
        })

        separatorsStepSize.push({
            chapter_start: separatorsStepSize[separatorsStepSize.length - 1]['chapter_end'],
            chapter_end: questionsAmount,
            steps_difference: questionsAmount - separatorsStepSize[separatorsStepSize.length - 1]['chapter_end'],
        })

        let chapterLength = progressbarWidth / chapters;

        for (let i = 1; i < chapters; i++) {
            $('.growtype-quiz-progressbar').append('<span class="growtype-quiz-progressbar-chapter" style="left:' + (chapterLength * i) + 'px;"></span>')
        }

        let currentStepsLength = 0;
        separatorsStepSize.map(function (element, index) {
            if (element['chapter_start'] < questionsCounterNr) {
                let stepSize = element['chapter_end'] - questionsCounterNr === 0 ? 1 : element['chapter_end'] - questionsCounterNr;

                progressbarStepWidth = (chapterLength / element['steps_difference']) * (questionsCounterNr - element['chapter_start']);

                if (progressbarStepWidth > chapterLength) {
                    progressbarStepWidth = chapterLength;
                }

                currentStepsLength += progressbarStepWidth;
            }
        })

        progressbarIndicatorWidth = currentStepsLength;
    } else {
        progressbarStepWidth = progressbarWidth / questionsAmount;
        progressbarIndicatorWidth = questionsCounterNr * progressbarStepWidth;
    }

    $('.growtype-quiz-progressbar-inner').width(progressbarIndicatorWidth);

    sessionStorage.setItem('growtype_quiz_global', JSON.stringify(window.growtype_quiz_global))
}
