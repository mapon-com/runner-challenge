<?php

namespace App;

use App\Models\ChallengeModel;
use App\Models\UserModel;
use App\Services\ActivityService;
use App\Services\ChallengeService;
use App\Services\TeamService;
use App\Services\UserService;
use League\Plates\Engine;

abstract class BaseController
{
    /** @var UserService */
    protected $users;

    /** @var ActivityService */
    protected $activities;

    /** @var ?UserModel */
    protected $user;

    /** @var ?ChallengeModel Current challenge */
    protected $challenge;

    /** @var string */
    protected $flash;

    /** @var TeamService */
    protected $teams;

    /** @var ChallengeService */
    protected $challenges;

    public function __construct()
    {
        $this->users = new UserService;
        $this->activities = new ActivityService;
        $this->user = $this->users->getLoggedIn();
        $this->teams = new TeamService;
        $this->challenges = new ChallengeService;
        $this->challenge = $this->challenges->getCurrent();

        $this->flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
    }

    public function call(array $parameters)
    {
        $isAdmin = $parameters['admin'] ?? false;
        $isPublic = !$isAdmin && ($parameters['public'] ?? false);

        if (!$isPublic && !$this->user) {
            return $this->redirect('register');
        }

        if ($isAdmin && !$this->user->isAdmin) {
            return $this->redirect('board', 'Unfortunately, you are not an admin.');
        }

        return $this->{$parameters['action']}();
    }

    protected function render(string $view, array $variables = [])
    {
        $variables += [
            'user' => $this->user,
            '_flash' => $this->flash,
        ];

        $templates = Engine::create(__DIR__ . '/../views');
        return $templates->render(basename($view), $variables);
    }

    protected function redirect($url, string $flashMessage = null)
    {
        $_SESSION['_flash'] = $flashMessage;
        header("Location: /$url");
        return "";
    }
}