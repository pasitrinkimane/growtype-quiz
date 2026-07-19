<?php

class Growtype_Quiz_Landing
{
    public function __construct()
    {
        add_action('template_redirect', [$this, 'maybe_render_landing'], 5);
        add_filter('body_class', [$this, 'add_landing_body_class']);
    }

    public function add_landing_body_class(array $classes): array
    {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');

        if (str_starts_with($host, 'quiz.')) {
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            $path = parse_url($request_uri, PHP_URL_PATH);
            if (trim($path, '/') === '') {
                $classes[] = 'page-quiz-landing';
            }
        }

        return $classes;
    }

    public function maybe_render_landing(): void
    {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');

        if (!str_starts_with($host, 'quiz.')) {
            return;
        }

        // Only intercept the root path of the subdomain (e.g. quiz.presaid.test/)
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($request_uri, PHP_URL_PATH);
        if (trim($path, '/') !== '') {
            return;
        }

        $slug = apply_filters('growtype_quiz_landing_default_slug', '');

        if (empty($slug)) {
            return;
        }

        $entry = Growtype_Quiz_Registry::get(str_replace('-', '_', $slug));

        if (!$entry || empty($entry['__instance'])) {
            return;
        }

        $quiz = $entry['__instance'];
        $landing_class = $quiz->landing_class();

        if ($landing_class === null || !class_exists($landing_class)) {
            return;
        }

        $renderer = new $landing_class();

        status_header(200);

        echo growtype_quiz_include_view('landing.index', [
            'landing_content' => $renderer->render(),
        ]);

        exit;
    }
}
