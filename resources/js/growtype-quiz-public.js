import { getQuizData } from "./helpers/data";
import { saveQuizDataEvent } from "./events/saveQuizDataEvent";
import { showInitialQuestion } from './actions/question/showInitialQuestion.js';
import { nextQuestionTrigger } from './components/nextQuestionTrigger.js';
import { previousQuestionTrigger } from './components/previousQuestionTrigger.js';
import { answerTrigger } from './components/answerTrigger.js';
import { singleFeedback } from './components/singleFeedback.js';
import { input } from './components/input.js';
import { unitSystem } from './components/unitSystem.js';
import { modal } from './components/modal.js';
import { questionnaireModal } from './components/questionnaireModal.js';
import { updateProgressBar } from "./actions/progress/bar/updateProgressBar";
import { updateQuestionsCounter } from "./actions/progress/counter/updateQuestionsCounter";
import { updateProgressCounter } from "./actions/progress/counter/updateProgressCounter";
import { countDownTimer } from "./actions/progress/timer/countDownTimer";
import { duration } from "./actions/progress/timer/duration";

import "./listeners/saveQuizDataListener";
import "./listeners/showSuccessQuestionListener";
import "./listeners/loaderFinishedListener";
import "./listeners/validation/validateQuestion";

function setQuizParams(element) {
    growtypeQuizSetParams($(element));
}

function activateQuiz(element) {
    let quizWrapper = $(element);
    let quizId = quizWrapper.attr('id');

    window.growtype_quiz_global[quizId]['showNextQuestionWasFired'] = false;

    if (quizWrapper.find('.growtype-quiz').attr('data-save-on-load')) {
        document.dispatchEvent(saveQuizDataEvent(getQuizData(quizId)));
    }

    new answerTrigger().init();

    singleFeedback();

    input(quizWrapper);
    unitSystem(quizWrapper);
    showInitialQuestion(quizWrapper);
    nextQuestionTrigger(quizWrapper);
    modal(quizWrapper);
    previousQuestionTrigger(quizWrapper);
    updateQuestionsCounter(quizWrapper);
    updateProgressBar(quizWrapper);
    updateProgressCounter(quizWrapper);
    duration(quizWrapper);
    countDownTimer(quizWrapper);
}

/**
 * Prevent double click
 */
$(document).ready(function () {
    questionnaireModal();

    if (window.growtype_quiz_global) {
        $('.growtype-quiz-wrapper').map(function (index, element) {
            setQuizParams(element);
        });

        $('.growtype-quiz-wrapper').map(function (index, element) {
            activateQuiz(element);
        });

        document.addEventListener('growtypeQuizReinitialize', (event) => {
            const modalSlug = event.detail && event.detail.modalSlug;
            const modalElement = document.querySelector('[data-growtype-quiz-modal="' + modalSlug + '"]');
            const quizWrapper = modalElement && modalElement.querySelector('.growtype-quiz-wrapper');

            if (!quizWrapper) {
                return;
            }

            delete window.growtype_quiz_global[quizWrapper.id];
            delete window.growtype_quiz_data[quizWrapper.id];

            setQuizParams(quizWrapper);
            activateQuiz(quizWrapper);
        });
    }
});
