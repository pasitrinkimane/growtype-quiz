<?php

/**
 * Renders registered Growtype Quiz definitions inside reusable modal dialogs.
 *
 * Preferred one-file definition example:
 *
 * class Exit_Survey extends Growtype_Quiz_Definition {
 *     public function slug(): string { return 'exit_survey'; }
 *     public function questions(): array { return [...]; }
 *     public function modal_config(): ?array {
 *         return [
 *             'version' => 'dark',
 *             'show_once' => true,
 *             'dialog_class' => 'exit-survey-modal',
 *             'condition' => static fn(): bool => is_account_page(),
 *         ];
 *     }
 *     public function modal_styles(): string {
 *         return '.exit-survey-modal { max-width: 640px; }';
 *     }
 * }
 * Growtype_Quiz_Registry::register(Exit_Survey::class);
 */
class Growtype_Quiz_Modal
{
    private const DEVELOPMENT_MODAL_QUERY_PARAM = 'growtype_quiz_modal';

    /** @var array<string,array<string,mixed>> */
    private static array $modals = [];

    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;

        add_action('growtype_quiz_definition_registered', [self::class, 'register_definition']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_styles'], 20);
        add_filter('growtype_quiz_scripts_should_be_loaded', [self::class, 'should_load_assets']);
        add_filter('growtype_quiz_success_url', [self::class, 'prevent_modal_redirect'], PHP_INT_MAX, 3);
        add_action('wp_footer', [self::class, 'render'], 20);

        // Covers definitions registered before this component was booted.
        self::register_definitions();
    }

    /**
     * @param string $quiz_slug Registered Growtype Quiz definition slug.
     * @param array<string,mixed> $args Modal display and behavior options.
     */
    public static function register(string $quiz_slug, array $args = []): void
    {
        $quiz_slug = str_replace('-', '_', sanitize_key($quiz_slug));

        if ($quiz_slug === '') {
            throw new InvalidArgumentException('A Growtype Quiz modal requires a quiz slug.');
        }

        $args = wp_parse_args($args, [
            'title'              => '',
            'description'        => '',
            'aria_label'         => __('Questionnaire', 'growtype-quiz'),
            'version'            => 'light',
            'trigger_selector'   => '[data-growtype-quiz-modal-open="' . $quiz_slug . '"]',
            'dialog_class'       => '',
            'close_label'        => __('Close questionnaire', 'growtype-quiz'),
            'dismissible'        => true,
            'show_once'          => false,
            'auto_open'          => false,
            'auto_open_delay'    => 0,
            'auto_open_wait_for' => '',
            'completion_actions' => [],
            'default_action'     => 'close',
            'condition'          => static fn(): bool => true,
            'styles'             => '',
        ]);

        $args['version'] = strtolower((string) $args['version']);
        if (!in_array($args['version'], ['light', 'dark'], true)) {
            $args['version'] = 'light';
        }

        $args['dialog_class'] = self::sanitize_css_classes((string) $args['dialog_class']);
        $args['completion_actions'] = is_array($args['completion_actions']) ? $args['completion_actions'] : [];
        $args['show_once'] = filter_var($args['show_once'], FILTER_VALIDATE_BOOLEAN);
        $args['auto_open_delay'] = max(0, (int) $args['auto_open_delay']);
        self::$modals[$quiz_slug] = $args;
    }

    /** Register one class-based quiz definition when it enters the registry. */
    public static function register_definition(Growtype_Quiz_Definition $definition): void
    {
        $config = $definition->modal_config();
        if (!is_array($config)) {
            return;
        }

        $config['styles'] = $definition->modal_styles();
        self::register($definition->slug(), $config);
    }

    /** Register every quiz definition that declares its own modal config. */
    public static function register_definitions(): void
    {
        foreach (Growtype_Quiz_Registry::all() as $entry) {
            $definition = $entry['__instance'] ?? null;

            if (!$definition instanceof Growtype_Quiz_Definition) {
                continue;
            }

            self::register_definition($definition);
        }
    }

    public static function has(string $quiz_slug): bool
    {
        $quiz_slug = str_replace('-', '_', sanitize_key($quiz_slug));

        return isset(self::$modals[$quiz_slug]);
    }

    /** Attach renderable modal-specific CSS to the plugin's public stylesheet. */
    public static function enqueue_styles(): void
    {
        $styles = [];

        foreach (self::$modals as $slug => $args) {
            if (
                $args['styles'] !== ''
                && (
                    self::condition_passes($args['condition'])
                    || self::is_development_preview($slug)
                )
            ) {
                $styles[] = (string) $args['styles'];
            }
        }

        if (!empty($styles) && wp_style_is(Growtype_Quiz::PLUGIN_KEY, 'enqueued')) {
            wp_add_inline_style(Growtype_Quiz::PLUGIN_KEY, implode("\n", $styles));
        }
    }

    public static function should_load_assets(bool $should_load): bool
    {
        return $should_load || self::has_renderable_modal();
    }

    /**
     * Modal questionnaires handle completion in the browser so the originating
     * link or form can be continued safely after the response has been saved.
     */
    public static function prevent_modal_redirect(string $url, int $quiz_id, array $submitted): string
    {
        $slug = str_replace('-', '_', sanitize_key($submitted['quiz_slug'] ?? ''));

        return isset(self::$modals[$slug]) ? '' : $url;
    }

    public static function render(): void
    {
        foreach (self::$modals as $slug => $args) {
            $is_development_preview = self::is_development_preview($slug);

            if (!$is_development_preview && !self::condition_passes($args['condition'])) {
                continue;
            }

            $quiz_data = growtype_quiz_get_formatted_quiz_data(null, [
                'quiz_slug'              => $slug,
                'show_question_nr_in_url' => false,
                'redirect_on_complete'   => false,
            ]);

            if (empty($quiz_data['questions'])) {
                continue;
            }

            $quiz_data['show_question_nr_in_url'] = false;
            $quiz_data['quiz_wrapper_class'] = trim(
                ($quiz_data['quiz_wrapper_class'] ?? '') . ' growtype-quiz-modal-quiz'
            );

            $dialog_classes = trim('modal-dialog modal-dialog-centered modal-dialog-scrollable growtype-quiz-questionnaire-dialog ' . $args['dialog_class']);
            $modal_classes = 'modal fade growtype-quiz-questionnaire-modal growtype-quiz-questionnaire-modal--' . $args['version'];
            $modal_id = 'growtype-quiz-modal-' . $slug;
            $title_id = 'growtype-quiz-modal-title-' . $slug;
            $description_id = 'growtype-quiz-modal-description-' . $slug;

            ?>
            <div
                id="<?php echo esc_attr($modal_id); ?>"
                class="<?php echo esc_attr($modal_classes); ?>"
                data-growtype-quiz-modal="<?php echo esc_attr($slug); ?>"
                data-growtype-quiz-modal-version="<?php echo esc_attr($args['version']); ?>"
                data-trigger-selector="<?php echo esc_attr($args['trigger_selector']); ?>"
                data-completion-actions="<?php echo esc_attr(wp_json_encode($args['completion_actions'])); ?>"
                data-default-action="<?php echo esc_attr($args['default_action']); ?>"
                data-dismissible="<?php echo $args['dismissible'] ? 'true' : 'false'; ?>"
                data-show-once="<?php echo $args['show_once'] ? 'true' : 'false'; ?>"
                data-development-preview="<?php echo $is_development_preview ? 'true' : 'false'; ?>"
                data-bs-backdrop="<?php echo $args['dismissible'] ? 'true' : 'static'; ?>"
                data-bs-keyboard="<?php echo $args['dismissible'] ? 'true' : 'false'; ?>"
                data-auto-open="<?php echo ($args['auto_open'] || $is_development_preview) ? 'true' : 'false'; ?>"
                data-auto-open-delay="<?php echo $is_development_preview ? 0 : max(0, (int) $args['auto_open_delay']); ?>"
                data-auto-open-wait-for="<?php echo esc_attr($args['auto_open_wait_for']); ?>"
                tabindex="-1"
                <?php if ($args['title'] !== '') { ?>aria-labelledby="<?php echo esc_attr($title_id); ?>"<?php } else { ?>aria-label="<?php echo esc_attr($args['aria_label']); ?>"<?php } ?>
                <?php if ($args['description'] !== '') { ?>aria-describedby="<?php echo esc_attr($description_id); ?>"<?php } ?>
                aria-hidden="true"
            >
                <div class="<?php echo esc_attr($dialog_classes); ?>">
                    <div class="modal-content">
                        <div class="modal-header growtype-quiz-questionnaire-header">
                            <div class="growtype-quiz-questionnaire-heading">
                                <?php if ($args['title'] !== '') { ?>
                                    <h2 class="modal-title" id="<?php echo esc_attr($title_id); ?>"><?php echo wp_kses_post($args['title']); ?></h2>
                                <?php } ?>
                                <?php if ($args['description'] !== '') { ?>
                                    <p id="<?php echo esc_attr($description_id); ?>"><?php echo wp_kses_post($args['description']); ?></p>
                                <?php } ?>
                            </div>
                            <?php if ($args['dismissible']) { ?>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr($args['close_label']); ?>"></button>
                            <?php } ?>
                        </div>
                        <div class="modal-body growtype-quiz-questionnaire-content">
                            <?php echo growtype_quiz_include_view('quiz.sections.content', ['quiz_data' => $quiz_data]); ?>
                            <div class="growtype-quiz-questionnaire-status visually-hidden" role="status" aria-live="polite"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    private static function has_renderable_modal(): bool
    {
        foreach (self::$modals as $slug => $args) {
            if (
                self::condition_passes($args['condition'])
                || self::is_development_preview($slug)
            ) {
                return true;
            }
        }

        return false;
    }

    private static function condition_passes($condition): bool
    {
        return is_callable($condition)
            ? (bool) call_user_func($condition)
            : (bool) $condition;
    }

    private static function is_development_preview(string $quiz_slug): bool
    {
        if (!self::is_development_environment()) {
            return false;
        }

        $requested_slug = isset($_GET[self::DEVELOPMENT_MODAL_QUERY_PARAM])
            ? str_replace('-', '_', sanitize_key(wp_unslash($_GET[self::DEVELOPMENT_MODAL_QUERY_PARAM])))
            : '';

        return $requested_slug !== '' && $requested_slug === $quiz_slug;
    }

    private static function is_development_environment(): bool
    {
        if (
            function_exists('wp_get_environment_type')
            && wp_get_environment_type() === 'development'
        ) {
            return true;
        }

        $host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));

        return $host === 'localhost'
            || $host === '127.0.0.1'
            || $host === '::1'
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.localhost');
    }

    private static function sanitize_css_classes(string $classes): string
    {
        $classes = preg_split('/\s+/', trim($classes)) ?: [];

        return implode(' ', array_filter(array_map('sanitize_html_class', $classes)));
    }
}
