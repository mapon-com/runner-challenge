<?php

namespace App;

use InvalidArgumentException;
use League\Plates\Engine;

class Controller
{
    /** @var UserService */
    private $users;

    /** @var TrackService */
    private $tracks;

    public function __construct()
    {
        $this->users = new UserService;
        $this->tracks = new TrackService;
    }

    public function index()
    {
        if (!$this->users->getLoggedIn()) {
            return $this->redirect('register');
        }

        return $this->redirect('board');
    }

    public function board()
    {
        if (!$this->users->getLoggedIn()) {
            return $this->redirect('register');
        }
        return $this->render('board');
    }

    public function upload()
    {
        if (!$this->users->getLoggedIn()) {
            return $this->redirect('');
        }

        $file = $_FILES['gpx'];

        try {
            $wasUploaded = $this->tracks->upload(
                $this->users->getLoggedIn(),
                $file['name'],
                $file['tmp_name']
            );
        } catch (InvalidArgumentException $e) {
            $wasUploaded = false;
        }

        return $this->redirect('board?was-uploaded=' . (int)$wasUploaded);
    }

    public function register()
    {
        if ($this->users->getLoggedIn()) {
            return $this->redirect('board');
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
                return $this->redirect('board?logged-in');
            }
            return $this->redirect('register?bad-password');
        }

        $user = $this->users->register($email, $password, $name);
        $this->users->logIn($user);

        return $this->redirect('board?registered');
    }

    public function logout()
    {
        $this->users->logOut();
        return $this->redirect('');
    }

    private function redirect($url)
    {
        header("Location: /$url");
        return "";
    }

    private function render(string $view, array $variables = [])
    {
        $templates = Engine::create(__DIR__ . '/../views');
        return $templates->render(basename($view), $variables);
    }
}
