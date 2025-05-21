import {getQuizData} from "./helpers/getQuizData";
import {saveQuizDataEvent} from "./events/saveQuizDataEvent";
import {showInitialQuestion} from './actions/question/showInitialQuestion.js';
import {nextQuestionTrigger} from './components/nextQuestionTrigger.js';
import {previousQuestionTrigger} from './components/previousQuestionTrigger.js';
import {answerTrigger} from './components/answerTrigger.js';
import {input} from './components/input.js';
import {unitSystem} from './components/unitSystem.js';
import {modal} from './components/modal.js';
import {updateProgressBar} from "./actions/progress/bar/updateProgressBar";
import {updateQuestionsCounter} from "./actions/progress/counter/updateQuestionsCounter";
import {updateProgressCounter} from "./actions/progress/counter/updateProgressCounter";
import {countDownTimer} from "./actions/progress/timer/countDownTimer";
import {duration} from "./actions/progress/timer/duration";

import "./listeners/saveQuizDataListener";
import "./listeners/showSuccessQuestionListener";
import "./listeners/loaderFinishedListener";
import "./listeners/validation/validateQuestion";

/**
 * Prevent double click
 */
$(document).ready(function () {
    if (window.growtype_quiz_global) {
        $('.growtype-quiz-wrapper').map(function (index, element) {
            /**
             * Set params
             */
            growtypeQuizSetParams($(element));
        });

        $('.growtype-quiz-wrapper').map(function (index, element) {
            let quizId = $(element).attr('id');

            window.growtype_quiz_global[quizId]['showNextQuestionWasFired'] = false;

            if ($(element).find('.growtype-quiz').attr('data-save-on-load')) {
                document.dispatchEvent(saveQuizDataEvent(getQuizData(quizId)));
            }

            new answerTrigger().init();

            input($(element));
            unitSystem($(element));
            showInitialQuestion($(element));
            nextQuestionTrigger($(element));
            modal($(element));
            previousQuestionTrigger($(element));
            updateQuestionsCounter($(element));
            updateProgressBar($(element));
            updateProgressCounter($(element));
            duration($(element));
            countDownTimer($(element));
        });
    }
});
