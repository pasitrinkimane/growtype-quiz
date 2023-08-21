<?php include('sections/head.php') ?>

<?php echo growtype_quiz_include_view('quiz.sections.header') ?>
<?php echo growtype_quiz_include_view('quiz.sections.content', ['quiz_data' => $quiz_data]) ?>
<?php echo growtype_quiz_include_view('quiz.sections.footer') ?>
<?php echo growtype_quiz_include_view('quiz.sections.footer-scripts') ?>
