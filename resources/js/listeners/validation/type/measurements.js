var measurementsValidation = {
    'age': {
        'min': 18,
        'max': 100
    },
    'centimeters': {
        'min': 100,
        'max': 230
    },
    'feet': {
        'min': 2,
        'max': 10
    },
    'inches': {
        'min': 0,
        'max': 11
    },
    'current_weight': {
        'lb': {
            'min': 80,
            'max': 550
        },
        'kg': {
            'min': 30,
            'max': 250
        }
    },
    'target_weight': {
        'lb': {
            'min': 80,
            'max': 550
        },
        'kg': {
            'min': 30,
            'max': 250
        }
    }
};

/**
 * Measurements Validation
 */
function validateMeasurements($this, showErrors) {

    $('.b-form-measurements .e-message-error').remove();

    if (typeof showErrors === 'undefined') {
        showErrors = true;
    }

    var allInputsFilled = true;
    var failedValues = [];
    $this.find('input:visible').each(function () {
        $(this).removeClass('is-invalid')

        if ($(this).is(":visible") && $(this).attr('required') && $(this).val().length === 0
            || $(this).is(":visible") && $(this).attr('required') && isNaN(parseInt($(this).val()))) {
            if (showErrors) {
                $(this).addClass('is-invalid')
            }
            if ($(this).data('validation-empty').length > 0 && !failedValues.includes($(this).data('validation-empty'))) {
                failedValues.push($(this).data('validation-empty'));
            }
            allInputsFilled = false;
        }

        if ($(this).attr('name') === 'age') {
            if (parseInt($(this).val()) < measurementsValidation.age.min) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
            if (parseInt($(this).val()) > measurementsValidation.age.max) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
        } else if ($(this).attr('name') === 'current_weight') {
            if ($(this).data('type-units') === 'lb') {
                if (parseInt($(this).val()) < measurementsValidation.current_weight.lb.min) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
                if (parseInt($(this).val()) > measurementsValidation.current_weight.lb.max) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
            }
            if ($(this).data('type-units') === 'kg') {
                if (parseInt($(this).val()) < measurementsValidation.current_weight.kg.min) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
                if (parseInt($(this).val()) > measurementsValidation.current_weight.kg.max) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
            }
        } else if ($(this).attr('name') === 'centimeters') {
            if (parseInt($(this).val()) < measurementsValidation.centimeters.min) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
            if (parseInt($(this).val()) > measurementsValidation.centimeters.max) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
        } else if ($(this).attr('name') === 'feet') {
            if (parseInt($(this).val()) < measurementsValidation.feet.min) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
            if (parseInt($(this).val()) > measurementsValidation.feet.max) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
        } else if ($(this).attr('name') === 'inches') {
            if (parseInt($(this).val()) < measurementsValidation.inches.min) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
            if (parseInt($(this).val()) > measurementsValidation.inches.max) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                if (!failedValues.includes($(this).data('validation-fromto'))) {
                    failedValues.push($(this).data('validation-fromto'));
                }
                allInputsFilled = false;
            }
        } else if ($(this).attr('name') === 'target_weight') {
            if ($(this).data('type-units') === 'lb') {
                if (parseInt($(this).val()) < measurementsValidation.target_weight.lb.min) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
                if (parseInt($(this).val()) > measurementsValidation.target_weight.lb.max) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
            }
            if ($(this).data('type-units') === 'kg') {
                if (parseInt($(this).val()) < measurementsValidation.target_weight.kg.min) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
                if (parseInt($(this).val()) > measurementsValidation.target_weight.kg.max) {
                    if (showErrors) {
                        $(this).addClass('is-invalid')
                    }
                    if (!failedValues.includes($(this).data('validation-fromto'))) {
                        failedValues.push($(this).data('validation-fromto'));
                    }
                    allInputsFilled = false;
                }
            }
        } else if ($(this).attr('name') === 'carbs' || $(this).attr('name') === 'proteins' || $(this).attr('name') === 'fats') {
            var proteins = $('input[name="proteins"]').val()
            var carbs = $('input[name="carbs"]').val()
            var fats = $('input[name="fats"]').val()
            var total = parseInt(proteins) + parseInt(carbs) + parseInt(fats);

            var filledMacronutrients = [];

            if ($(this).attr('name') === 'carbs' && $(this).val().length === 0) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                allInputsFilled = false;
            } else {
                filledMacronutrients.push('carbs')
            }

            if ($(this).attr('name') === 'proteins' && $(this).val().length === 0) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                allInputsFilled = false;
            } else {
                filledMacronutrients.push('proteins')
            }

            if ($(this).attr('name') === 'fats' && $(this).val().length === 0) {
                if (showErrors) {
                    $(this).addClass('is-invalid')
                }
                allInputsFilled = false;
            } else {
                filledMacronutrients.push('fats')
            }

            $this.closest('.quizslide').find('.alert').hide()

            if (filledMacronutrients.includes('carbs') && filledMacronutrients.includes('proteins') && filledMacronutrients.includes('fats') && total !== 100) {
                if (showErrors) {
                    $this.closest('.quizslide').find('.alert').show()
                }
                allInputsFilled = false;
            }
        }
    });

    var currentWeight = $this.find('.b-form-measurements-data:visible').find('input[name="current_weight"]');
    var desiredWeight = $this.find('.b-form-measurements-data:visible').find('input[name="target_weight"]');

    if (
        (typeof window.answers_data.measurements_weight !== 'undefined' && (parseInt(window.answers_data.measurements_weight.current_weight) < parseInt(desiredWeight.val())))
        || (parseInt(currentWeight.val()) < parseInt(desiredWeight.val()))
    ) {
        desiredWeight.addClass('is-invalid')
        if (desiredWeight.data('validation-morethan').length > 0 && !failedValues.includes(desiredWeight.data('validation-morethan'))) {
            failedValues.push(desiredWeight.data('validation-morethan'));
        }
        allInputsFilled = false;
    }

    if (showErrors && !allInputsFilled) {
        failedValues.reverse().map(function (value, index) {
            $('.b-form-measurements').prepend('<div class="col-12 e-message-error"><div class="alert alert-danger" role="alert" style="margin-bottom: 10px;margin-top: 3px;">' + value + '</div></div>');
            $('.b-form-measurements:visible .form-control:first').focus();
        });
    }

    return allInputsFilled;
}

/**
 * Validate measurements form inputs on entering info
 */
function validateMeasurementsOnEnter() {
    /**
     * On enter check
     */
    $('.b-form-measurements .form-control[type="tel"]').on('keypress', function (key) {
        var inputVal = $(this).val().replace(/\s+/g, '');
        var inputName = $(this).attr('name');
        var charactersAmount = inputVal.length + 1;

        /**
         * On enter continue to next slide
         */
        if (key.charCode == 13) {
            $(this).closest('.quizslide').find('.growtype-quiz-btn-go-next').click();
            return false;
        }

        /**
         * Prevent letters
         */
        if (key.charCode !== 13) {
            if (key.charCode < 48 || key.charCode > 57) return false;
        }
    });

    /**
     * Check values after entered
     */
    $('.b-form-measurements .form-control').on('keyup', function (key) {
        var inputVal = $(this).val().replace(/\s+/g, '');
        var inputName = $(this).attr('name');

        if (inputName === 'age') {
            if (parseInt(inputVal) === 0) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }

            if (inputVal > measurementsValidation.age.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }

            if (inputVal.length > 1 && inputVal < measurementsValidation.age.min && inputVal != 10 && inputVal != 11) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'feet') {
            if (parseInt(inputVal) === 0) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
            if (inputVal > measurementsValidation.feet.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'inches') {
            if (parseInt(inputVal) === 0 && inputVal.length > 1) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
            if (inputVal > measurementsValidation.inches.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'centimeters') {
            if (parseInt(inputVal) === 0 || inputVal > measurementsValidation.centimeters.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'current_weight' && $(this).closest('.b-form-measurements-data').hasClass('m-imperial')) {
            if (parseInt(inputVal) === 0 || inputVal > measurementsValidation.current_weight.lb.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'current_weight' && $(this).closest('.b-form-measurements-data').hasClass('m-metric')) {
            if (parseInt(inputVal) === 0 || inputVal > measurementsValidation.current_weight.kg.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'target_weight' && $(this).closest('.b-form-measurements-data').hasClass('m-imperial')) {
            if (parseInt(inputVal) === 0 || inputVal > measurementsValidation.target_weight.lb.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        if (inputName === 'target_weight' && $(this).closest('.b-form-measurements-data').hasClass('m-metric')) {
            if (parseInt(inputVal) === 0 || inputVal > measurementsValidation.target_weight.kg.max) {
                return $(this).val(inputVal.substr(0, inputVal.length - 1));
            }
        }

        /**
         * Go to next input if [feet]
         */
        if (inputName === 'feet' && inputVal.length !== 0) {
            $(this).closest('.b-form-measurements').find('.form-control').each(function (index, element) {
                if ($(element).val() === '' && !$(element).is(":focus")) {
                    $(element).focus();
                    return false;
                }
            });
        }
    });

    /**
     * On type check ab test
     */
    if ($('body').hasClass('page-quiz-validation')) {
        $('.b-form-measurements .form-control').on('input', function (key) {
            if (validateMeasurements($(this).closest('.input-wrapper'), false)) {
                $(this).closest('.form-group').removeClass('is-invalid');
                $(this).closest('.form-group').addClass('is-valid');
                $(this).closest('.quizslide').find('.growtype-quiz-btn-go-next').addClass('is-active');
            } else {
                $(this).closest('.form-group').removeClass('is-valid');
                $(this).closest('.form-group').addClass('is-invalid');
                $(this).closest('.quizslide').find('.growtype-quiz-btn-go-next').removeClass('is-active');
            }
        });
    }
}
