import { getQuizData } from "../../helpers/data";
import { collectQuizDataEvent } from "../../events/collectQuizDataEvent";

let existingAnswersParams = new URLSearchParams(window.location.search).get('answers');
let existingAnswers = {};

if (existingAnswersParams) {
    let pairs = existingAnswersParams.split('|');

    pairs.forEach(pair => {
        let [key, value] = pair.split(':');
        if (key && value) {
            existingAnswers[key] = value.split(',');
        }
    });
}

import { storage } from "../../helpers/storage";

export function collectQuizData(currentQuestion) {
    let quizWrapper = currentQuestion.closest('.growtype-quiz-wrapper');
    let quizId = quizWrapper.attr('id');
    let answers = getQuizData(quizId)['answers'];

    if (Object.keys(existingAnswers).length > 0) {
        for (let existingAnswer in existingAnswers) {
            if (answers.hasOwnProperty(existingAnswer)) {
                answers[existingAnswer] = [...new Set([...answers[existingAnswer], ...existingAnswers[existingAnswer]])];
            } else {
                answers[existingAnswer] = existingAnswers[existingAnswer];
            }
        }
    }

    let correctlyAnswered = getQuizData(quizId)['correctly_answered'];
    let currentQuestionKey = currentQuestion.attr('data-key');
    let currentQuestionType = currentQuestion.attr('data-question-type');

    /**
     * Skip info questions
     */
    if (currentQuestionType === 'info') {
        return;
    }

    /**
     * Init answers array
     */
    answers[currentQuestionKey] = [];

    /**
     * Collect answers
     */
    if (currentQuestionType === 'open') {
        answers[currentQuestionKey].push(currentQuestion.find('textarea').val())
    } else {
        currentQuestion.find('.growtype-quiz-question-answer.is-active').map(function (index, element) {
            answers[currentQuestionKey].push($(this).attr('data-value'))
        });
    }

    /**
     * Collect correct answers
     */
    if (quizWrapper.attr('data-quiz-type') === 'scored' && currentQuestionType !== 'open') {
        let correctAnswer = true;
        currentQuestion.find('.growtype-quiz-question-answer').map(function (index, element) {
            if ($(this).hasClass('is-active') && $(this).attr('data-cor').length === 0) {
                correctAnswer = false;
            }
        });

        if (!correctlyAnswered[currentQuestionKey]) {
            correctlyAnswered[currentQuestionKey] = [];
        }

        correctlyAnswered[currentQuestionKey].push(correctAnswer);
    }

    /**
     * Collect inputs data
     */
    if (currentQuestion.find('input:visible,textarea:visible').length > 0) {
        let formData = window.growtype_quiz_global[quizId]['files'];
        currentQuestion.find('input:visible,textarea:visible').each(function (index, element) {
            if ($(element).attr('type') === 'file') {
                let formKey = currentQuestionKey + '#_#' + index + '#_#' + $(element).attr('name');
                let images = $(element)[0].files;

                for (var x = 0; x < images.length; x++) {
                    formData.append(formKey, images[x]);
                }

                window.growtype_quiz_global[quizId]['files'] = formData;
            } else {
                answers[currentQuestionKey].push({
                    name: $(element).attr('name'),
                    value: $(element).val()
                });
            }
        })
    }

    /**
     * Save unit system
     */
    if (currentQuestion.find('.unitsystem-selector-item.is-active:visible').length > 0) {
        answers['unit_system'] = currentQuestion.find('.unitsystem-selector-item.is-active').attr('data-type');
    }

    /**
     * Custom values
     */
    currentQuestion.find('.growtype-quiz-custom-value.can-collect').each(function () {
        const value = $(this).attr('data-value');
        const key = $(this).attr('data-key');

        if (key) {
            // Ensure it's an object for key/value data
            if (Array.isArray(answers[currentQuestionKey])) {
                answers[currentQuestionKey] = {};
            }
            answers[currentQuestionKey][key] = value;
        } else {
            // Ensure it's an array for value-only data
            if (!Array.isArray(answers[currentQuestionKey])) {
                answers[currentQuestionKey] = [];
            }
            answers[currentQuestionKey].push(value);
        }
    });

    /**
     * Collect extra details
     */
    document.dispatchEvent(collectQuizDataEvent({
        currentQuestion: currentQuestion,
        answers: answers
    }));

    /**
     * Save answers to session
     */
    storage.set('growtype_quiz_answers', JSON.stringify(answers), 'session');

    getQuizData(quizId)['answers'] = answers;
}
