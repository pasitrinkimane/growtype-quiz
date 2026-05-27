<?php

/**
 * Growtype_Quiz_Registry
 *
 * Central registry for quiz extensions.
 * Accepts class-based definitions (recommended) or legacy function-based configs.
 *
 * Class-based usage (recommended):
 *
 *   class My_Quiz extends Growtype_Quiz_Definition { ... }
 *   Growtype_Quiz_Registry::register(My_Quiz::class);
 *
 * Legacy function-based usage:
 *
 *   Growtype_Quiz_Registry::define('my_slug', [
 *       'data_provider' => 'my_fn',
 *       'quiz_id'       => 123,
 *   ]);
 *
 * @package Growtype_Quiz
 * @since   1.0.0
 */
class Growtype_Quiz_Registry
{
    // ── Schema (legacy define() keys) ─────────────────────────────────────────

    private const SCHEMA = [
        'slug'          => 'string',
        'quiz_id'       => 'int',
        'data_provider' => 'callable',
        'on_success'    => 'callable',
        'success_url'   => 'string|callable',
        'theme'         => 'string',
        'label'         => 'string',
        'meta'          => 'array',
        '__class'       => 'string',   // internal: set by register()
    ];

    private const SLUG_PATTERN = '/^[a-z0-9_]+$/';

    // ── State ─────────────────────────────────────────────────────────────────

    /** @var array<string, array> Entries keyed by slug. */
    private static array $definitions = [];

    /** @var bool Whether WP hooks have been registered. */
    private static bool $booted = false;

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Register a class-based quiz definition (recommended).
     *
     * @param class-string<Growtype_Quiz_Definition> $class
     * @throws \InvalidArgumentException
     */
    public static function register(string $class): void
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Quiz class \"{$class}\" does not exist.");
        }
        if (!is_subclass_of($class, Growtype_Quiz_Definition::class)) {
            throw new \InvalidArgumentException(
                "\"{$class}\" must extend Growtype_Quiz_Definition."
            );
        }

        /** @var Growtype_Quiz_Definition $instance */
        $instance = new $class();
        $slug     = $instance->slug();

        self::validate_slug($slug);

        self::$definitions[$slug] = array_merge(
            $instance->to_registry_config(),
            ['slug' => $slug]
        );

        self::boot();
    }

    /**
     * Register a legacy function-based quiz definition.
     *
     * @param string $slug   Lowercase, underscore slug.
     * @param array  $config See SCHEMA keys.
     * @throws \InvalidArgumentException
     */
    public static function define(string $slug, array $config = []): void
    {
        self::validate_slug($slug);
        self::validate_config($config);

        self::$definitions[$slug] = array_merge(
            ['slug' => $slug],
            self::cast_config($config)
        );

        self::boot();
    }

    /**
     * Get a definition by slug, or null.
     */
    public static function get(string $slug): ?array
    {
        return self::$definitions[$slug] ?? null;
    }

    /**
     * Find a definition by WP post ID.
     */
    public static function find_by_quiz_id(int $quiz_id): ?array
    {
        foreach (self::$definitions as $def) {
            if (isset($def['quiz_id']) && $def['quiz_id'] === $quiz_id) {
                return $def;
            }
        }
        return null;
    }

    /**
     * Resolve by slug first, then by quiz_id.
     */
    public static function resolve(string $slug, int $quiz_id = 0): ?array
    {
        return self::get($slug) ?? ($quiz_id > 0 ? self::find_by_quiz_id($quiz_id) : null);
    }

    /** Return all registered definitions. */
    public static function all(): array
    {
        return self::$definitions;
    }

    /** Check if a slug is registered. */
    public static function has(string $slug): bool
    {
        return isset(self::$definitions[$slug]);
    }

    // ── WP Hook Integration ───────────────────────────────────────────────────

    private static function boot(): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        add_filter('growtype_quiz_registry',         [static::class, 'merge_into_registry']);
        add_filter('growtype_quiz_get_quiz_data',     [static::class, 'dispatch_data_provider'], 5);
        add_action('growtype_quiz_after_save_data',   [static::class, 'dispatch_on_success'], 10, 2);
        add_filter('growtype_quiz_success_url',       [static::class, 'dispatch_success_url'], 5, 3);
        add_filter('growtype_quiz_id_is_required',    [static::class, 'maybe_relax_quiz_id_requirement']);
        add_filter('posts_results',                   [static::class, 'inject_virtual_post'], 10, 2);
    }

    /** @internal */
    public static function merge_into_registry(array $registry): array
    {
        return array_merge($registry, self::$definitions);
    }

    /** @internal — relax quiz_id requirement for virtual (ID=0) quizzes */
    public static function maybe_relax_quiz_id_requirement(bool $required): bool
    {
        $slug = isset($_POST['quiz_slug'])
            ? str_replace('-', '_', sanitize_text_field($_POST['quiz_slug']))
            : '';

        if ($slug && self::has($slug)) {
            return false; // quiz_slug alone is enough to identify this quiz
        }

        return $required;
    }

    /** @internal */
    public static function dispatch_data_provider(array $quiz_data): array
    {
        $slug    = str_replace('-', '_', $quiz_data['quiz_slug'] ?? '');
        $quiz_id = (int) ($quiz_data['quiz_id'] ?? 0);
        $entry   = self::resolve($slug, $quiz_id);

        if (!$entry) {
            return $quiz_data;
        }

        // ── Class-based dispatch ──────────────────────────────────
        if (!empty($entry['__class']) && class_exists($entry['__class'])) {
            /** @var Growtype_Quiz_Definition $def */
            $def   = new $entry['__class']();
            $extra = $def->to_quiz_data();
            return array_merge($quiz_data, $extra);
        }

        // ── Legacy function-based dispatch ────────────────────────
        if (!empty($entry['data_provider']) && function_exists($entry['data_provider'])) {
            $extra = call_user_func($entry['data_provider']);
            if (is_array($extra)) {
                return array_merge($quiz_data, $extra);
            }
        }

        return $quiz_data;
    }

    /** @internal */
    public static function dispatch_on_success(int $quiz_id, array $submitted): void
    {
        $entry = self::resolve_by_post($quiz_id);

        if (!$entry) {
            return;
        }

        // ── Class-based ───────────────────────────────────────────
        if (!empty($entry['__class']) && class_exists($entry['__class'])) {
            (new $entry['__class']())->on_success($quiz_id, $submitted);
            return;
        }

        // ── Legacy ────────────────────────────────────────────────
        if (!empty($entry['on_success']) && function_exists($entry['on_success'])) {
            call_user_func($entry['on_success'], $quiz_id, $submitted, $entry);
        }
    }

    /** @internal */
    public static function dispatch_success_url(string $url, int $quiz_id, array $submitted): string
    {
        $entry = self::resolve_by_post($quiz_id);

        if (!$entry) {
            return $url;
        }

        // ── Class-based ───────────────────────────────────────────
        if (!empty($entry['__class']) && class_exists($entry['__class'])) {
            $result = (new $entry['__class']())->success_url();
            return $result ?? $url;
        }

        // ── Legacy ────────────────────────────────────────────────
        if (!empty($entry['success_url'])) {
            $target = $entry['success_url'];
            return is_callable($target) ? call_user_func($target, $quiz_id, $submitted) : (string) $target;
        }

        return $url;
    }

    // ── Virtual post ──────────────────────────────────────────────────────────

    /**
     * Inject a virtual WP_Post when the URL matches a registered quiz slug
     * but no real post exists in the database.
     *
     * Fires on the `posts_results` filter — returning a non-empty array
     * prevents WordPress from setting is_404 = true.
     *
     * @internal
     */
    public static function inject_virtual_post(array $posts, \WP_Query $query): array
    {
        // Only act on the main query when WP found nothing
        if (!$query->is_main_query() || !empty($posts)) {
            return $posts;
        }

        $post_type = class_exists('Growtype_Quiz')
            ? Growtype_Quiz::get_growtype_quiz_post_type()
            : 'quiz';

        // Use WP's already-parsed query vars — reliable, no URL parsing
        $queried_type = $query->get('post_type');
        $queried_name = $query->get('name');

        if ($queried_type !== $post_type || empty($queried_name)) {
            return $posts;
        }

        // Normalise: hyphens → underscores to match registry keys
        $slug  = str_replace('-', '_', $queried_name);
        $entry = self::get($slug);

        if (!$entry) {
            return $posts;
        }

        // Build a synthetic post so WP renders the quiz template normally
        $now          = current_time('mysql');
        $now_gmt      = current_time('mysql', true);
        $display_slug = str_replace('_', '-', $slug);

        $virtual = new \WP_Post((object) [
            'ID'                => 0,
            'post_title'        => $entry['label'] ?? ucwords(str_replace('_', ' ', $slug)),
            'post_name'         => $display_slug,
            'post_type'         => $post_type,
            'post_status'       => 'publish',
            'post_content'      => '',
            'post_excerpt'      => '',
            'post_author'       => 0,
            'comment_status'    => 'closed',
            'ping_status'       => 'closed',
            'post_date'         => $now,
            'post_date_gmt'     => $now_gmt,
            'post_modified'     => $now,
            'post_modified_gmt' => $now_gmt,
            'post_parent'       => 0,
            'menu_order'        => 0,
            'guid'              => home_url('/' . $post_type . '/' . $display_slug . '/'),
            'filter'            => 'raw',
        ]);

        $query->is_404      = false;
        $query->is_single   = true;
        $query->is_singular = true;

        // Ensure HTTP 200 — without this WP still sends a 404 header
        status_header(200);

        return [$virtual];
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private static function resolve_by_post(int $quiz_id): ?array
    {
        $post = get_post($quiz_id);
        $slug = $post ? str_replace('-', '_', $post->post_name) : '';
        return self::resolve($slug, $quiz_id);
    }

    private static function validate_slug(string $slug): void
    {
        if ($slug === '') {
            throw new \InvalidArgumentException('Quiz slug cannot be empty.');
        }
        if (!preg_match(self::SLUG_PATTERN, $slug)) {
            throw new \InvalidArgumentException(
                "Quiz slug \"{$slug}\" is invalid. Use lowercase letters, digits, and underscores only."
            );
        }
    }

    private static function validate_config(array $config): void
    {
        $unknown = array_diff(array_keys($config), array_keys(self::SCHEMA));
        if (!empty($unknown)) {
            throw new \InvalidArgumentException(
                'Unknown quiz config key(s): ' . implode(', ', $unknown) . '. '
                . 'Allowed: ' . implode(', ', array_keys(self::SCHEMA))
            );
        }
    }

    private static function cast_config(array $config): array
    {
        $out = [];
        foreach ($config as $key => $value) {
            $type = self::SCHEMA[$key] ?? null;
            if ($type === null || $value === null) {
                continue;
            }
            $out[$key] = match ($type) {
                'int'    => (int) $value,
                'string' => (string) $value,
                'array'  => (array) $value,
                default  => $value,
            };
        }
        return $out;
    }
}
