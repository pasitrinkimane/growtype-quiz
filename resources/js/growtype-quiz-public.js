$ = jQuery;
import {showFirstQuestion} from './actions/question/showFirstQuestion.js';
import {nextQuestionTrigger} from './components/nextQuestionTrigger.js';
import {previousQuestionTrigger} from './components/previousQuestionTrigger.js';
import {restartQuizTrigger} from './components/restartQuizTrigger.js';
import {answerTrigger} from './components/answerTrigger.js';
import {updateProgressBar} from "./actions/progress/bar/updateProgressBar";
import {updateQuestionsCounter} from "./actions/progress/counter/updateQuestionsCounter";
import {updateProgressCounter} from "./actions/progress/counter/updateProgressCounter";
import {calculateTime} from "./actions/progress/timer/calculateTime";

import "./listeners/showSuccessQuestion";

$(document).ready(function () {
    showFirstQuestion(true);
    answerTrigger();
    nextQuestionTrigger();
    previousQuestionTrigger();
    restartQuizTrigger();
    updateQuestionsCounter();
    updateProgressBar();
    updateProgressCounter();
    calculateTime();
});
