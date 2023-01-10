<?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/head.php') ?>

@extends('layouts.app')

@section('header')
    @include('partials.sections.header')
@endsection

@section('content')
    <?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/content.php') ?>
@endsection

@section('footer')
    <?php do_action('growtype_quiz_section_footer'); ?>
    <?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/footer-scripts.php') ?>
    @include('partials.sections.footer')
@endsection
