<?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/head.php') ?>

@extends('layouts.app')

@section('header')
    @include('partials.sections.header')
@endsection

@section('content')
    <?php if (growtype_quiz_is_enabled()) {
        echo growtype_quiz_include_view('quiz.sections.content', ['quiz_data' => $quiz_data]);
    } else {
        echo growtype_quiz_include_view('quiz.sections.content-disabled', ['quiz_data' => $quiz_data]);
    } ?>
@endsection

@section('footer')
    <?php do_action('growtype_quiz_section_footer'); ?>
    <?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/footer-scripts.php') ?>
    @include('partials.sections.footer')
@endsection
