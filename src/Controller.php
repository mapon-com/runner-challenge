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

    public function myTeam()
    {
        $team = $this->teams->getById($this->user->teamId);

        return $this->render('my-team', [
            'team' => $team,
            'totals' => $team ? $this->teams->getUserTotals($team) : [],
        ]);
    }

    public function upload()
    {
        if (!$this->activities->canUpload()) {
            return $this->redirect('board', 'Activities cannot be logged at this moment');
        }

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

        if ($this->user->teamId) {
            $this->teams->recalculateTeamScore($this->teams->getById($this->user->teamId));
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
        return $this->render('leaderboard-teams', [
            'totals' => $this->teams->getTeamTotals(),
        ]);
    }

    public function leaderboardPeople()
    {
        return $this->render('leaderboard-people', [
            'totals' => $this->teams->getUserTotals(null),
        ]);
    }

    public function logout()
    {
        $this->users->logOut();
        return $this->redirect('register');
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
        $team = $this->teams->getById($_POST['teamId']);
        $users = $this->users->getByIds($_POST['userIds'] ?? []);

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

    public function deleteTeam()
    {
        $team = $this->teams->getById($_POST['teamId']);
        $this->teams->deleteTeam($team);
        return $this->redirect('admin', 'Team has been delete');
    }

    public function impersonate()
    {
        $user = $this->users->impersonate($_POST['userId']);
        return $this->redirect('board', 'You are now impersonating ' . htmlspecialchars($user->name));
    }

    public function enableUpload()
    {
        $this->activities->setUpload((bool)$_POST['canUpload']);
        return $this->redirect('admin');
    }
}
