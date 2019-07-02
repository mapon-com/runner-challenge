<?php

use App\Bootstrap;

/**
 * @param string $route
 * @param bool $absolute
 * @return string
 */
function route($route, bool $absolute = false): string
{
    $prefix = '';

    if ($absolute) {
        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        $prefix = ($isHttps ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    }

    return $prefix . Bootstrap::$routeGenerator->generate($route);
}

/**
 * @param string $url
 * @return string
 */
function asset(string $url): string
{
    return Bootstrap::$routeGenerator->generate('index') . ltrim($url, '/');
}