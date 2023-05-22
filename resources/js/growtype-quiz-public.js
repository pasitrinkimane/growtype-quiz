import {saveQuizDataEvent} from "./events/saveQuizDataEvent";

$ = jQuery;
import {showFirstQuestion} from './actions/question/showFirstQuestion.js';
import {nextQuestionTrigger} from './components/nextQuestionTrigger.js';
import {previousQuestionTrigger} from './components/previousQuestionTrigger.js';
import {answerTrigger} from './components/answerTrigger.js';
import {input} from './components/input.js';
import {updateProgressBar} from "./actions/progress/bar/updateProgressBar";
import {updateQuestionsCounter} from "./actions/progress/counter/updateQuestionsCounter";
import {updateProgressCounter} from "./actions/progress/counter/updateProgressCounter";
import {countDownTimer} from "./actions/progress/timer/countDownTimer";
import {duration} from "./actions/progress/timer/duration";

import "./listeners/showSuccessQuestionListener";
import "./listeners/validation/validateQuestion";

$(document).ready(function () {

    if (growtype_quiz_local.save_data_on_load) {
        document.dispatchEvent(saveQuizDataEvent());
    }

    input();
    showFirstQuestion(true);
    answerTrigger();
    nextQuestionTrigger();
    previousQuestionTrigger();
    updateQuestionsCounter();
    updateProgressBar();
    updateProgressCounter();
    duration();
    countDownTimer();
});
