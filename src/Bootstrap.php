<?php

namespace App;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use RedBeanPHP\R;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Bootstrap
{
    /** @var UrlGenerator */
    public static $routeGenerator;

    public function run()
    {
        session_start();

        $this->loadEnv();

        require __DIR__ . '/functions.php';

        /** @var RouteCollection $routes */
        $routes = require __DIR__ . '/routes.php';

        $routes->addPrefix(ltrim(getenv('URL_PREFIX'), '/'));

        $routeContext = new RequestContext();
        $matcher = new UrlMatcher($routes, $routeContext);

        self::$routeGenerator = new UrlGenerator($routes, $routeContext);

        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        try {
            $parameters = $matcher->match($url);
        } catch (ResourceNotFoundException $e) {
            return 'Page not found';
        }

        R::setup('sqlite:' . __DIR__ . '/../storage/database.sqlite');

        return (new Controller)->call($parameters);
    }

    private function loadEnv()
    {
        try {
            Dotenv::create(__DIR__ . '/../', '.env')->load();
        } catch (InvalidPathException $e) {
            die('Environment file is not set.');
        }
    }
}