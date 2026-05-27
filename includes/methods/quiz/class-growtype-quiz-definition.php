<?php

/**
 * Growtype_Quiz_Definition
 *
 * Abstract base class for programmatic quiz definitions.
 * Extend this class to define a quiz — the framework handles
 * all dispatch (data merging, on_success, success_url) automatically.
 *
 * Minimal example:
 *
 *   class My_Quiz extends Growtype_Quiz_Definition {
 *       public function slug(): string { return 'my_quiz'; }
 *       public function questions(): array { return [...]; }
 *   }
 *   Growtype_Quiz_Registry::register(My_Quiz::class);
 *
 * @package Growtype_Quiz
 * @since   1.0.0
 */
abstract class Growtype_Quiz_Definition
{
    // ── Required ──────────────────────────────────────────────────────────────

    /** Unique quiz slug (lowercase, underscores). Used for URL and registry lookup. */
    abstract public function slug(): string;

    /**
     * Return the ordered questions array.
     * Include a 'loader' key for the final transition screen.
     *
     * @return array
     */
    abstract public function questions(): array;

    // ── Optional overrides ────────────────────────────────────────────────────

    /** Human-readable quiz name. */
    public function label(): string { return ''; }

    /** WordPress post ID of the quiz post (if slug-only lookup is not enough). */
    public function quiz_id(): ?int { return null; }

    /** Theme identifier passed to growtype_quiz_get_quiz_theme(). */
    public function theme(): ?string { return null; }

    /** Arbitrary metadata array. */
    public function meta(): array { return []; }

    /** Label for the start/continue button. */
    public function start_btn_label(): string { return 'Get Started'; }

    /** Slide counter style: 'basic' | 'progress'. */
    public function slide_counter_style(): string { return 'basic'; }

    /** Whether to append ?q=N to the URL as the user advances slides. */
    public function show_question_nr_in_url(): bool { return false; }

    /** Whether to show the back arrow in the quiz header globally. */
    public function show_back_btn(): bool { return true; }

    /**
     * Optional redirect URL after successful quiz submit.
     * Return null to use the plugin default.
     */
    public function success_url(): ?string { return null; }

    /**
     * Called after a successful quiz submit.
     * Override to add custom logic (e.g. send email, create post, etc.)
     *
     * @param int   $quiz_id   WordPress post ID of the quiz.
     * @param array $submitted Submitted answers keyed by question key.
     */
    public function on_success(int $quiz_id, array $submitted): void {}

    // ── Helpers available to subclasses ───────────────────────────────────────

    /**
     * Build a loader (final "success") question using the shared component.
     * Falls back gracefully if the helper is not loaded yet.
     *
     * @param string $content   Text shown while loading.
     * @param string $style     'default' | 'sliders'
     * @param int    $duration  Loader animation duration in seconds.
     */
    protected function loader(
        string $content  = 'Analyzing your results…',
        string $style    = 'default',
        int    $duration = 90
    ): array {
        if (function_exists('growtype_child_growtype_quiz_get_loader')) {
            return growtype_child_growtype_quiz_get_loader([
                'loader_content'  => $content,
                'loader_style'    => $style,
                'loader_duration' => $duration,
            ]);
        }

        // Minimal fallback if helper is not available
        return [
            'key'           => 'final',
            'question_type' => 'success',
            'has_intro'     => true,
            'intro'         => '<h2>Almost there…</h2><p>' . esc_html($content) . '</p>',
            'question_style' => 'horizontal',
        ];
    }

    // ── Framework internals (do not override) ─────────────────────────────────

    /**
     * Converts this definition into the quiz_data array that gets merged
     * into the base plugin's quiz_data by Growtype_Quiz_Registry.
     *
     * @internal
     */
    final public function to_quiz_data(): array
    {
        $data = [
            'slide_counter_style'       => $this->slide_counter_style(),
            'start_btn_label'           => $this->start_btn_label(),
            'show_question_nr_in_url'   => $this->show_question_nr_in_url(),
            'show_quiz_header_back_btn' => $this->show_back_btn(),
            'questions'                 => $this->questions(),
        ];

        if ($this->label() !== '') {
            $data['label'] = $this->label();
        }

        return $data;
    }

    /**
     * Converts this definition into the config array for Growtype_Quiz_Registry.
     *
     * @internal
     */
    final public function to_registry_config(): array
    {
        return array_filter([
            'label'        => $this->label() ?: null,
            'quiz_id'      => $this->quiz_id(),
            'theme'        => $this->theme(),
            'meta'         => $this->meta() ?: null,
            'success_url'  => $this->success_url(),
            '__class'      => static::class,   // internal: back-reference for dispatch
        ], fn($v) => $v !== null);
    }
}
