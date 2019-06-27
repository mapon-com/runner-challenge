<?php

namespace App;

use RedBeanPHP\R;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Bootstrap
{
    public function run()
    {
        session_start();

        $routes = new RouteCollection;

        $routes->add('index', new Route('/', ['action' => 'index', 'public' => true]));
        $routes->add('register', new Route('/register', ['action' => 'register', 'public' => true]));
        $routes->add('board', new Route('/board', ['action' => 'board']));
        $routes->add('logout', new Route('/logout', ['action' => 'logout']));
        $routes->add('upload', new Route('/upload', ['action' => 'upload']));

        $matcher = new UrlMatcher($routes, new RequestContext);

        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        try {
            $parameters = $matcher->match($url);
        } catch (ResourceNotFoundException $e) {
            return 'Page not found';
        }

        R::setup('sqlite:' . __DIR__ . '/../storage/database.sqlite');

        return (new Controller)->call($parameters);
    }
}