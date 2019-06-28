<?php

namespace App;

use App\Models\UserModel;
use League\Plates\Engine;

abstract class BaseController
{
    /** @var UserService */
    protected $users;

    /** @var ActivityService */
    protected $activities;

    /** @var UserModel|null */
    protected $user;

    /** @var string */
    protected $flash;

    public function __construct()
    {
        $this->users = new UserService;
        $this->activities = new ActivityService;
        $this->user = $this->users->getLoggedIn();

        $this->flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
    }

    public function call(array $parameters)
    {
        $isPublic = $parameters['public'] ?? false;
        if (!$isPublic && !$this->user) {
            return $this->redirect('register');
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