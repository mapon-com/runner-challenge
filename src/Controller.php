<?php

namespace App;

use App\Services\UserService;
use InvalidArgumentException;

class Controller extends BaseController
{
    public function index()
    {
        return $this->redirect('board');
    }

    public function board()
    {
        return $this->render('my-activities', [
            'activities' => $this->activities->getActivities($this->user),
        ]);
    }

    public function upload()
    {
        try {
            $this->activities->upload(
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

    public function leaderboardTeams()
    {
        return $this->render('leaderboard-teams');
    }

    public function leaderboardPeople()
    {
        return $this->render('leaderboard-people');
    }

    public function logout()
    {
        $this->users->logOut();
        return $this->redirect('');
    }

    public function admin()
    {
        return $this->render('admin', [
            'teams' => $this->teams->getAll($this->challenge),
            'users' => $this->users->getAll(),
        ]);
    }

    public function addTeam()
    {
        $team = $this->teams->addTeam($this->challenge);
        return $this->redirect('admin', 'Team "' . $team->name . '" was added');
    }

    public function assignTeam()
    {
        $team = $this->teams->getById($this->challenge, $_POST['teamId']);
        $users = $this->users->getByIds($_POST['userIds']);

        if (!$team) {
            $this->redirect('admin', 'Team was not found');
        }

        $this->teams->assignUsers($team, $users);

        return $this->redirect('admin', 'People have been assigned to a team.');
    }

    public function unassignTeam()
    {
        $user = $this->users->findById($_POST['userId']);
        $this->teams->unassignUser($user);
        return $this->redirect('admin', 'A person has been unassigned from a team.');
    }
}
