<?php
$post = get_post();
$quiz_data = get_quiz_data($post->ID);

if (!current_user_can('manage_options')) {
    if (!is_null($quiz_data['is_enabled']) && !$quiz_data['is_enabled']) {
        wp_redirect(get_home_url());
    }
}

?>

@extends('layouts.app')

@section('header')
    @include('partials.sections.header', ['fixedHeader' => false])
@endsection

@section('content')
    <section class="s-intro">
        <div class="container">
            {!! get_the_content() !!}
        </div>
    </section>
    <section class="s-quiz">
        <div class="container">
            <?= include_quiz_view('partials.quiz-types.scored', ['quiz_data' => $quiz_data]) ?>
        </div>
    </section>
@endsection

@section('footer')
@endsection

@push('footerScripts')
    <script>
        let quizInTestMode = @json($quiz_data['is_test_mode']);
        let quizSaveAnswers = @json($quiz_data['save_answers'] === false ? false : true);
        let showCorrectAnswersInitially = @json($quiz_data['show_correct_answers_initially'] === false ? false : true);
        let quizId = @json($post->ID);
        window.quizCurrentFunnel = 'a';
        window.quizQuestionsAmount = $('.b-quiz-question:not(.b-quiz-question[data-key="success"])').length;
        window.quizCurrentQuestionNr = 1;
        window.quizQuestionsKeysAlreadyVisited = [];
        window.quizQuestionsFunnelsAlreadyVisited = [];
        window.quizLastQuestionNextLabel = @json(__("Finish", "growtype-registration"));
    </script>
@endpush
