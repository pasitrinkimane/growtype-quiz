import {getQuizData} from "./helpers/getQuizData";

$ = jQuery;

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
window.showNextQuestionWasFired = false;

$(document).ready(function () {
    if (window.growtype_quiz_global) {
        if ($('.growtype-quiz').attr('data-save-on-load')) {
            document.dispatchEvent(saveQuizDataEvent(getQuizData()));
        }

        new answerTrigger().init();

        input();
        unitSystem();
        showInitialQuestion();
        nextQuestionTrigger();
        modal();
        previousQuestionTrigger();
        updateQuestionsCounter();
        updateProgressBar();
        updateProgressCounter();
        duration();
        countDownTimer();
    }
});
