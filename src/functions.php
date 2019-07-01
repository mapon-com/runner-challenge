<?php

use App\Bootstrap;

/**
 * @param string $route
 * @return string
 */
function route($route): string
{
    return Bootstrap::$routeGenerator->generate($route);
}

/**
 * @param string $url
 * @return string
 */
function asset(string $url): string
{
    return Bootstrap::$routeGenerator->generate('index') . ltrim($url, '/');
}