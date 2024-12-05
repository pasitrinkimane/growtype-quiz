<div class="growtype-quiz-nav"
     data-first-question-answer-type="<?php echo isset($quiz_data['first_question_answer_type']) ? $quiz_data['first_question_answer_type'] : 'single' ?>"
     data-question-title-nav="<?php echo isset($quiz_data) && $quiz_data['use_question_title_nav'] ? 'true' : 'false' ?>"
     data-type="footer"
>
    <div class="growtype-quiz-nav-inner">
        <button class="btn btn-secondary growtype-quiz-btn-go-back" style="display: none;">
            <div class="icon-arrow">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 7C0.447715 7 4.82823e-08 7.44772 0 8C-4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM15.7071 8.70711C16.0976 8.31658 16.0976 7.68342 15.7071 7.29289L9.34315 0.928933C8.95262 0.538409 8.31946 0.538409 7.92893 0.928933C7.53841 1.31946 7.53841 1.95262 7.92893 2.34315L13.5858 8L7.92893 13.6569C7.53841 14.0474 7.53841 14.6805 7.92893 15.0711C8.31946 15.4616 8.95262 15.4616 9.34315 15.0711L15.7071 8.70711ZM1 9L15 9L15 7L1 7L1 9Z" fill="black"/>
                </svg>
            </div>
            <span class="e-label" data-label="<?php echo $quiz_data['back_btn_label'] ?>"><?php echo $quiz_data['back_btn_label'] ?></span>
        </button>

        <?php if (isset($quiz_data) && $quiz_data['slide_counter'] && ($quiz_data['slide_counter_position'] === 'bottom' || $quiz_data['slide_counter_position'] === 'both')) { ?>
            <?php echo growtype_quiz_include_view('quiz.partials.components.question-nr', ['quiz_data' => $quiz_data]); ?>
        <?php } ?>

        <button class="btn btn-primary growtype-quiz-btn-go-next">
            <span class="e-label" data-label="<?php echo $quiz_data['next_btn_label'] ?>" data-label-finish="<?php echo $quiz_data['finish_btn_label'] ?>" data-label-start="<?php echo $quiz_data['start_btn_label'] ?>"><?php echo $quiz_data['start_btn_label'] ?></span>
            <span class="icon-arrow"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 7C0.447715 7 4.82823e-08 7.44772 0 8C-4.82823e-08 8.55228 0.447715 9 1 9L1 7ZM15.7071 8.70711C16.0976 8.31658 16.0976 7.68342 15.7071 7.29289L9.34315 0.928933C8.95262 0.538409 8.31946 0.538409 7.92893 0.928933C7.53841 1.31946 7.53841 1.95262 7.92893 2.34315L13.5858 8L7.92893 13.6569C7.53841 14.0474 7.53841 14.6805 7.92893 15.0711C8.31946 15.4616 8.95262 15.4616 9.34315 15.0711L15.7071 8.70711ZM1 9L15 9L15 7L1 7L1 9Z" fill="white"/>
</svg>
</span>
        </button>
    </div>
</div>
