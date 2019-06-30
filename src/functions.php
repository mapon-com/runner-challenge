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