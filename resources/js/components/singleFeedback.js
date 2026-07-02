/**
 * singleFeedback — inline insight panel for answer_type="single_feedback"
 *
 * When a user clicks an answer on a question with data-answer-type="single_feedback":
 *  1. The answer is marked is-active (selected highlight)
 *  2. An insight panel slides in below the answers list using the answer's
 *     data-additional-question-content attribute as the text
 *  3. A "Continue" button in the panel triggers the quiz's native next button
 */
export function singleFeedback() {
  $(document).on(
    "click",
    '.growtype-quiz-question[data-answer-type="single_feedback"] .growtype-quiz-question-answer',
    function (e) {
      e.stopImmediatePropagation();

      const $answer = $(this);
      const $question = $answer.closest(".growtype-quiz-question");
      const $wrapper = $answer.closest(".growtype-quiz-wrapper");
      const feedback = (
        $answer.attr("data-additional-question-content") || ""
      ).trim();

      // ── Mark answer active ────────────────────────────────
      $question.find(".growtype-quiz-question-answer").removeClass("is-active");

      $answer.addClass("is-active");

      // ── Remove any existing feedback panel ────────────────
      $question.find(".gq-feedback-panel").remove();

      // ── No feedback text → advance immediately ────────────
      if (!feedback) {
        $wrapper.find(".growtype-quiz-btn-go-next").trigger("click");
        return;
      }

      // ── Build panel ───────────────────────────────────────
      const $panel = $(`
                <div class="gq-feedback-panel" role="status" aria-live="polite">
                    <div class="gq-feedback-panel-inner">
                        <span class="gq-feedback-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles mt-0.5 h-5 w-5 shrink-0 text-[color:var(--color-lime)]" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>
                        </span>
                        <p class="gq-feedback-text">${feedback}</p>
                    </div>
                </div>
            `);

      // ── Inject after answers wrapper ──────────────────────
      $question.find(".growtype-quiz-question-answers-wrapper").after($panel);

      // ── Animate in (next frame so transition fires) ───────
      requestAnimationFrame(() => $panel.addClass("is-visible"));
    },
  );
}
