<?php

namespace App;

use League\Plates\Engine;

class Controller
{
    /** @var UserService */
    private $users;

    public function __construct()
    {
        $this->users = new UserService;
    }

    public function index()
    {
        if (!$this->users->getLoggedIn()) {
            $this->redirect('register');
        }

        $this->redirect('board');
    }

    public function board()
    {
        if (!$this->users->getLoggedIn()) {
            $this->redirect('register');
        }
        return $this->render('board');
    }

    public function register()
    {
        if ($this->users->getLoggedIn()) {
            $this->redirect('board');
        }

        $users = new UserService;

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name = $_POST['name'] ?? '';

        if (!$email || !$password) {
            return $this->render('register');
        }

        $user = $users->findUser($email);

        if ($user) {
            if ($user->passwordMatches($password)) {
                $this->users->logIn($user);
                $this->redirect('board?logged-in');
            }
            $this->redirect('register?bad-password');
        }

        $user = $this->users->register($email, $password, $name);
        $this->users->logIn($user);

        $this->redirect('board?registered');
    }

    public function logout()
    {
        $this->users->logOut();
        $this->redirect('');
    }

    private function redirect($url)
    {
        header("Location: /$url");
        die;
    }

    private function render(string $view, array $variables = [])
    {
        $templates = Engine::create(__DIR__ . '/../views');
        return $templates->render(basename($view), $variables);
    }

}
