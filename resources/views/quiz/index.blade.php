<?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/head.php') ?>

@extends('layouts.app')

@section('header')
    @include('partials.sections.header')
@endsection

@section('content')
    <?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/content.php') ?>
@endsection

@section('footer')
    @include('partials.sections.footer')
@endsection

<?php include(GROWTYPE_QUIZ_PATH . 'resources/views/quiz/sections/footer-scripts.php') ?>
