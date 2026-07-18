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
     * Render the full landing page HTML.
     */
    abstract public function render(): string;
}
