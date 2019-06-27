<?php

namespace App;

use App\Models\UserModel;
use InvalidArgumentException;
use League\Plates\Engine;

class Controller
{
    /** @var UserService */
    private $users;

    /** @var TrackService */
    private $tracks;

    /** @var UserModel|null */
    private $user;

    /** @var string */
    private $flash;

    public function __construct()
    {
        $this->users = new UserService;
        $this->tracks = new TrackService;
        $this->user = $this->users->getLoggedIn();

        $this->flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
    }

    public function index()
    {
        if (!$this->user) {
            return $this->redirect('register');
        }

        return $this->redirect('board');
    }

    public function board()
    {
        if (!$this->user) {
            return $this->redirect('register');
        }
        return $this->render('board');
    }

    public function upload()
    {
        if (!$this->user) {
            return $this->redirect('');
        }

        try {
            $this->tracks->upload(
                $this->user,
                $_FILES['gpx']['name'],
                $_FILES['gpx']['tmp_name'],
                $_POST['activityUrl'],
                $_POST['comment']
            );
        } catch (InvalidArgumentException $e) {
            return $this->redirect('board', $e->getMessage());
        }

        return $this->redirect('board', 'Activity logged!');
    }

    public function register()
    {
        if ($this->user) {
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
                return $this->redirect('board', 'Welcome back, ' . htmlspecialchars($user->name));
            }
            return $this->redirect('register', 'Password is not correct!');
        }

        try {
            $user = $this->users->register($email, $password, $name);
        } catch (InvalidArgumentException $e) {
            return $this->redirect('register', $e->getMessage());
        }

        $this->users->logIn($user);

        return $this->redirect('board', 'Successfully registered!');
    }

    public function logout()
    {
        $this->users->logOut();
        return $this->redirect('');
    }

    private function redirect($url, string $flashMessage = null)
    {
        $_SESSION['_flash'] = $flashMessage;
        header("Location: /$url");
        return "";
    }

    private function render(string $view, array $variables = [])
    {
        $variables += [
            'user' => $this->user,
            '_flash' => $this->flash,
        ];

        $templates = Engine::create(__DIR__ . '/../views');
        return $templates->render(basename($view), $variables);
    }
}
