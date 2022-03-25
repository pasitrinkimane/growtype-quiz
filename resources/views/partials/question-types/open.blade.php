@php
    $max_characters_amount = $question['max_characters_amount'];
    $max_characters_amount_tag = !empty($max_characters_amount) ? 'maxlength="' . $max_characters_amount . '"' : '';
@endphp

<div class="b-quiz-question-intro">
    {!! $question['intro'] !!}
</div>

<div class="b-quiz-question-answers-wrapper">
    <div class="b-quiz-question-answer">
        <textarea name="answer-open" cols="30" rows="10" {!! $max_characters_amount_tag !!} required></textarea>
        @if(!empty($max_characters_amount))
            <div class="e-explanation">{!! __('Maksimalus simbolių skaičius','growtype-quiz') !!} - {!! $max_characters_amount !!}</div>
        @endif
    </div>
</div>
