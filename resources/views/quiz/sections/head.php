<?php
if (!isset($quiz_data)) {
    $post = get_post();
    $quiz_data = growtype_quiz_get_quiz_data($post->ID);
}
?>
