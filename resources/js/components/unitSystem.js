import {updateGlobalStorageKey} from "../state/updateStorage";

export function unitSystem(quizWrapper) {
    let quizId = quizWrapper.attr('id');

    quizWrapper.find('.unitsystem-selector').each(function (index, element) {
        if (index > 1) {
            $(element).hide();
        }

        if (index === 0) {
            updateGlobalUnitSystem($(element).find('.unitsystem-selector-item.is-active'));
            updateUnitSystemSelector($(element).find('.unitsystem-selector-item.is-active'))
            updateUnitSystemSelectors($(element).find('.unitsystem-selector-item.is-active'))
        }
    });

    quizWrapper.find('.unitsystem-selector .unitsystem-selector-item').click(function () {
        updateGlobalUnitSystem($(this));
        updateUnitSystemSelector($(this))
        updateUnitSystemSelectors($(this))
    });

    function updateGlobalUnitSystem(selector) {
        let unitSystem = selector.attr('data-type');
        updateGlobalStorageKey('unit_system', unitSystem);
        window.growtype_quiz_global[quizId]['unit_system'] = unitSystem;
    }

    function updateUnitSystemSelector(selector) {
        selector.closest('.growtype-quiz-measurements-form').find('.unitsystem-selector-item').removeClass('is-active');
        selector.closest('.growtype-quiz-measurements-form').find('.unitsystem-selector-item[data-type="' + window.growtype_quiz_global[quizId]['unit_system'] + '"]').addClass('is-active');

        selector.closest('.growtype-quiz-measurements-form').find('.unitsystem-group').removeClass('is-active');
        selector.closest('.growtype-quiz-measurements-form').find('.unitsystem-group[data-type="' + window.growtype_quiz_global[quizId]['unit_system'] + '"]').addClass('is-active');
    }

    function updateUnitSystemSelectors(selector) {
        let unitSystem = selector.attr('data-type');

        let currentQuestion = selector.closest('.growtype-quiz-question');

        currentQuestion.find('.unitsystem-selector .unitsystem-selector-item').each(function (index, element) {
            if ($(element).attr('data-type') === unitSystem) {
                updateUnitSystemSelector($(element))
            }
        });

        let nextQuestions = selector.closest('.growtype-quiz-question').nextAll();

        nextQuestions.each(function (index, element) {
            if ($(element).find('.unitsystem-selector').length > 0) {
                $(element).find('.unitsystem-selector').hide();
                $(element).find('.unitsystem-selector .unitsystem-selector-item').each(function (index, element) {
                    if ($(element).attr('data-type') === unitSystem) {
                        updateUnitSystemSelector($(element))
                    }
                });
            }
        });
    }
}
