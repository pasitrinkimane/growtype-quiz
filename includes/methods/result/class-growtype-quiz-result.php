<?php

/**
 * Growtype_Quiz_Result
 *
 * Handles result retrieval and presentation for a completed quiz.
 *
 * @package Growtype_Quiz
 * @since   1.0.0
 */
class Growtype_Quiz_Result
{
    const RESULTS_PAGE_SLUG = "/gqresults/";

    /**
     * Return the (possibly domain-overridden) results page slug.
     * Domains can customise it via: add_filter('growtype_quiz_results_page_slug', fn() => '/my-results/');
     */
    public static function get_slug(): string
    {
        return (string) apply_filters(
            "growtype_quiz_results_page_slug",
            self::RESULTS_PAGE_SLUG,
        );
    }

    /**
     * Return the full base URL for the results page.
     */
    public static function get_base_url(): string
    {
        return home_url(self::get_slug());
    }

    /**
     * Check whether a results page post exists and is published.
     */
    private static function results_page_post_exists(): bool
    {
        $page_id = get_option("growtype_quiz_results_page");

        if (empty($page_id)) {
            return false;
        }

        $post = get_post($page_id);

        return $post && $post->post_status === "publish";
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    /**
     * Register WordPress hooks.
     * Call once from the plugin loader.
     */
    public static function boot(): void
    {
        // Rewrite rules — must run on init
        add_action("init", [static::class, "register_route"]);

        // Template handler — registered once, guards itself internally
        add_action("template_redirect", [static::class, "render_results_page"]);

        // Add 'page-results' body class on the results page
        add_filter("body_class", static function (array $classes): array {
            if (
                get_query_var("growtype_quiz_results") &&
                !self::results_page_post_exists()
            ) {
                $classes[] = "page-results";
            }

            return $classes;
        });
    }

    // ── Route ─────────────────────────────────────────────────────────────────

    /**
     * Register the /gqresults/ rewrite rule so WordPress handles the URL
     * even without a real page in the database.
     *
     * Only registers when no results page option is configured —
     * the configured page's own permalink handles the route in that case.
     */
    public static function register_route(): void
    {
        if (self::results_page_post_exists()) {
            return;
        }

        $slug = trim(self::get_slug(), "/"); // e.g. 'gqresults'

        add_rewrite_rule(
            "^" . $slug . '/?$',
            "index.php?growtype_quiz_results=1",
            "top",
        );

        // Register the query var so get_query_var() can read it
        add_filter("query_vars", static function (array $vars): array {
            $vars[] = "growtype_quiz_results";
            return $vars;
        });
    }

    /**
     * Render the results page.
     * Fires on template_redirect — guards itself with the query var check.
     */
    public static function render_results_page(): void
    {
        if (
            !get_query_var("growtype_quiz_results") ||
            self::results_page_post_exists()
        ) {
            return;
        }

        header("X-Robots-Tag: noindex, nofollow", true);
        status_header(200);

        echo growtype_quiz_include_view("result.index");
        exit();
    }

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Get a single quiz result by its unique hash.
     *
     * @param string $unique_hash
     * @return array|null
     */
    public static function get_by_hash(string $unique_hash): ?array
    {
        return Growtype_Quiz_Result_Crud::get_quiz_single_result_data_by_unique_hash(
            $unique_hash,
        ) ?:
            null;
    }

    /**
     * Get the result URL for a given unique hash.
     *
     * @param string $unique_hash
     * @return string
     */
    public static function get_results_url(string $unique_hash = ""): string
    {
        return growtype_quiz_results_page_url($unique_hash);
    }

    /**
     * Get the answers from a result row, decoded from JSON.
     *
     * @param array $result  A single result row from the DB.
     * @return array
     */
    public static function get_answers(array $result): array
    {
        if (empty($result["answers"])) {
            return [];
        }

        $decoded = json_decode($result["answers"], true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Get the quiz definition (quiz_data) associated with a result row.
     *
     * @param array $result  A single result row from the DB.
     * @return array
     */
    public static function get_quiz_data(array $result): array
    {
        $quiz_id = (int) ($result["quiz_id"] ?? 0);
        $quiz_slug = $result["quiz_slug"] ?? "";

        if ($quiz_id > 0) {
            return growtype_quiz_get_quiz_data($quiz_id);
        }

        if ($quiz_slug) {
            $slug = str_replace("-", "_", $quiz_slug);
            $entry = Growtype_Quiz_Registry::get($slug);

            if (
                $entry &&
                !empty($entry["__class"]) &&
                class_exists($entry["__class"])
            ) {
                $class = $entry["__class"];
                $instance = new $class();
                return $instance->to_quiz_data();
            }
        }

        return [];
    }
}
