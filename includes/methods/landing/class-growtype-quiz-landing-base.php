<?php

/**
 * Base class for quiz landing pages.
 *
 * Extend this to create a custom landing page for a quiz.
 *
 * ## Usage
 *   1. Create a class extending this base.
 *   2. Override landing_class() in the quiz class to return the class name.
 *   3. Access via subdomain quiz.domain.test.
 *
 * @package Growtype_Quiz
 */
abstract class Growtype_Quiz_Landing_Base
{
    /**
     * Whether the current request is a quiz landing page.
     *
     * Detected by subdomain (host starts with "quiz.") and root path.
     */
    public static function is_quiz_landing(): bool
    {
        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');

        if (!str_starts_with($host, 'quiz.')) {
            return false;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

        return trim($path, '/') === '';
    }

    /**
     * Render the full landing page HTML.
     */
    abstract public function render(): string;
}
