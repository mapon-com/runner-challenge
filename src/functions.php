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
 * @param bool $absolute
 * @return string
 */
function asset(string $url, bool $absolute = false): string
{
    return route('index', $absolute) . ltrim($url, '/');
}

/**
 * Check if current IP is in the whitelist
 * @return bool
 */
function is_ip_whitelisted()
{
    $whitelisted = explode(',', getenv('IP_WHITELIST'));
    $whitelisted = array_filter(array_map('trim', $whitelisted));

    if (!$whitelisted) {
        return true;
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    return $ip && in_array($ip, $whitelisted, true);
}