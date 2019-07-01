<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection;

$routes->add('index', new Route('/', ['action' => 'index', 'public' => true]));
$routes->add('register', new Route('/register', ['action' => 'register', 'public' => true]));

$routes->add('board', new Route('/board', ['action' => 'board']));
$routes->add('logout', new Route('/logout', ['action' => 'logout']));
$routes->add('upload', new Route('/upload', ['action' => 'upload']));
$routes->add('team-leaderboard', new Route('teams', ['action' => 'leaderboardTeams']));
$routes->add('people-leaderboard', new Route('people', ['action' => 'leaderboardPeople']));
$routes->add('my-team', new Route('/my-team', ['action' => 'myTeam']));
$routes->add('rules', new Route('/rules', ['action' => 'rules']));

$routes->add('admin', new Route('/admin', ['action' => 'admin', 'admin' => true]));
$routes->add('add-team', new Route('/admin/add-team', ['action' => 'addTeam', 'admin' => true]));
$routes->add('assign-team', new Route('/admin/assign-team', ['action' => 'assignTeam', 'admin' => true]));
$routes->add('unassign-team', new Route('/admin/unassign-team', ['action' => 'unassignTeam', 'admin' => true]));
$routes->add('delete-team', new Route('/admin/delete-team', ['action' => 'deleteTeam', 'admin' => true]));
$routes->add('impersonate', new Route('/admin/impersonate', ['action' => 'impersonate', 'admin' => true]));
$routes->add('enable-upload', new Route('/admin/enable-upload', ['action' => 'enableUpload', 'admin' => true]));
$routes->add('edit-rules', new Route('/admin/edit-rules', ['action' => 'editRules', 'admin' => true]));

return $routes;