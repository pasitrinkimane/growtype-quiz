<div class="b-quiz-question-intro">
    {!! $question['intro'] !!}
</div>

@if($question['question_type'] !== 'success')
    <div class="b-quiz-question-answers-wrapper">
        <div class="b-quiz-question-answers">
            @if(!empty($question['options_all']))
                @foreach($question['options_all'] as $option)
                    <div class="b-quiz-question-answer" data-value="{!! $option['value'] !!}" data-cor="{!! $quiz_data['is_test_mode'] ? $option['correct'] : '' !!}">
                        <div class="e-radio-wrapper">
                            <div class="e-radio"></div>
                        </div>
                        <label>{!! $option['label'] !!}</label>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @if(!empty($question['hint']))
        <div class="b-quiz-hint" style="display: none;">
            {!! $question['hint'] !!}
        </div>
    @endif
@endif

