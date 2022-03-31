<div class="b-quiz" data-type="{!! $quiz_data['quiz_type'] !!}">
    <div class="b-quiz-inner">

        @if($quiz_data['progress_bar'])
            <div class="b-quiz-progressbar mb-4">
                <div class="b-quiz-progressbar-inner"></div>
            </div>
        @endif

        @if($quiz_data['slide_counter'])
            <div class="b-quiz-header">
                <div class="b-quiz-question-nr"></div>
            </div>
        @endif

        @if($quiz_data['limited_time'])
            <div class="b-quiz-timer" data-duration="{!! $quiz_data['duration'] !!}">
                <span>Liko:</span>
                <div class="e-time-wrapper">
                    <span class="e-time"></span>
                </div>
                <span>min.</span>
            </div>
        @endif

        @php
            $index = 0;
        @endphp
        @foreach ($quiz_data['questions'] as $question)
            @php
            $disabled = $question['disabled'] ?? false;
            @endphp
            @if(!$disabled)
                <div class="b-quiz-question {!! $index === 0 ? 'first-question is-active' : '' !!}"
                     data-key="{!! $question['key'] !!}"
                     data-type="{!! $question['question_type'] !!}"
                     data-style="{!! $question['answer_type'] !!}"
                     data-funnel="{!! $question['funnel'] !!}"
                     data-hint="{!! $question['has_a_hint'] !!}"
                >
                    @if($question['question_type'] === 'open')
                        {!! include_quiz_view('partials.question-types.open', ['question' => $question, 'quiz_data' => $quiz_data]) !!}
                    @else
                        {!! include_quiz_view('partials.question-types.radio', ['question' => $question, 'quiz_data' => $quiz_data]) !!}
                    @endif
                </div>
            @endif
            @php
                $index++;
            @endphp
        @endforeach

        <div class="b-quiz-footer d-md-grid gap-2 d-md-flex">
            <button class="btn btn-secondary btn-go-back"><span class="dashicons dashicons-arrow-left-alt2"></span> Atgal
            </button>
            <button class="btn btn-primary btn-go-next">
                <span class="e-label" data-label="Kitas klausimas">Kitas klausimas</span>
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
        </div>
    </div>
</div>
