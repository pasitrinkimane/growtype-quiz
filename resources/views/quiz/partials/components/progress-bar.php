<?php
$hide_initially_class = isset($quiz_data['quiz_header_progress_bar_hide_initially']) && $quiz_data['quiz_header_progress_bar_hide_initially'] ? 'hide-initially' : 'show-initially';
?>
<div class="growtype-quiz-progressbar <?= $hide_initially_class ?>">
    <div class="growtype-quiz-progressbar-inner"></div>
</div>
