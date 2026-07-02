/**
 * Show current slide
 */
export function updateProgressCounter(quizWrapper) {
  let quizId = quizWrapper.attr("id");
  let total = window.growtype_quiz_global[quizId]["quiz_questions_amount"];
  let current =
    window.growtype_quiz_global[quizId]["current_question_counter_nr"];

  // ── Cap current at total (guard against overshoot when
  //     trailing slides are all excluded info slides) ──────
  if (current > total) {
    current = total;
  }

  let slideTotalNrFormatted = total < 10 ? "0" + total : total;
  let slideNrFormatted = current < 10 ? "0" + current : current;

  if (
    quizWrapper
      .find(".growtype-quiz-question-nr")
      .attr("data-counter-style") === "steps" ||
    quizWrapper
      .find(".growtype-quiz-question-nr")
      .attr("data-counter-style") === "outof"
  ) {
    slideTotalNrFormatted = total;
    slideNrFormatted = current;
  }

  if (
    quizWrapper
      .find(".growtype-quiz-question-nr")
      .attr("data-counter-style") === "answered_only"
  ) {
    slideTotalNrFormatted = total;
    slideNrFormatted = current - 1;
  }

  quizWrapper
    .find(".growtype-quiz-question-nr .growtype-quiz-question-nr-current-slide")
    .text(slideNrFormatted);
  quizWrapper
    .find(".growtype-quiz-question-nr .growtype-quiz-question-nr-total-slide")
    .text(slideTotalNrFormatted);

  /**
   * Set question attribute to highest dom element
   */
  $("body").attr(
    "data-current-question",
    window.growtype_quiz_global[quizId]["current_question_nr"],
  );
}
