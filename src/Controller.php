<?php

namespace App;

use App\Services\ImageService;
use App\Services\TextService;
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

    public function rules()
    {
        return $this->render('rules', [
            'rules' => (new TextService)->getRulesHtml(),
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

        if (!$this->challenge) {
            return $this->redirect('board', 'No active challenges.');
        }

        $gpxPathname = $_FILES['gpx']['tmp_name'];

        if (!is_uploaded_file($gpxPathname)) {
            return $this->redirect('board', 'Bad file selected');
        }

        try {
            $this->activities->upload(
                $this->user,
                $this->challenge,
                $_FILES['gpx']['name'],
                $gpxPathname,
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

    public function editTeam()
    {
        if (!$this->team) {
            return $this->redirect('board', 'You are not in a team!');
        }

        $imagePathname = $_FILES['image'] ? $_FILES['image']['tmp_name'] : null;
        if (!is_uploaded_file($imagePathname)) {
            $imagePathname = null;
        }

        $newName = $_POST['teamName'] ?? '';

        try {
            $this->teams->editTeam($this->team, $newName, $imagePathname);
        } catch (InvalidArgumentException $e) {
            return $this->redirect('my-team', $e->getMessage());
        }

        return $this->redirect('my-team', 'Team information updated');
    }

    public function image()
    {
        $imageId = $_GET['id'] ?? 0;
        $content = (new ImageService)->getImageContent($imageId);

        header('Cache-Control: public, max-age=31536000');
        header('Content-Type: image/png');
        header('Content-Length: ' . strlen($content));

        return $content;
    }

    public function register()
    {
        if ($this->user) {
            return $this->redirect('board');
        }

        $resetKey = $_GET['resetKey'] ?? ($_POST['resetKey'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $name = $_POST['name'] ?? '';

        $user = $resetKey ? $this->users->findUserByResetKey($resetKey) : $this->users->findUser($email);

        if ($resetKey && !$user) {
            return $this->redirect('register', 'Password reset URL is not valid');
        }

        if (!$email || !$password) {
            return $this->render('login', [
                'resetKey' => $resetKey,
                'email' => $user ? $user->email : '',
            ]);
        }

        if ($user) {
            try {
                $this->users->attemptLogIn($email, $password, $resetKey);
            } catch (InvalidArgumentException $e) {
                return $this->redirect('register', $e->getMessage());
            }
            return $this->redirect('board', 'Welcome back, ' . htmlspecialchars($user->name));
        }

        try {
            $user = $this->users->register($email, $password, $name);
            $this->users->logIn($user);
        } catch (InvalidArgumentException $e) {
            return $this->redirect('register', $e->getMessage());
        }

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
            'rules' => (new TextService)->getRules(),
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

    public function editRules()
    {
        (new TextService)->setRules($_POST['html'] ?? '');
        return $this->redirect('admin', 'Rules saved');
    }

    public function setParticipating()
    {
        try {
            foreach ($_POST['userIds'] as $userId) {
                (new UserService)->setParticipating($userId, (bool)$_POST['isParticipating']);
            }
        } catch (InvalidArgumentException $e) {
            return $this->redirect('admin', $e->getMessage());
        }

        return $this->redirect('admin', 'Participation status changed');
    }

    public function resetPassword()
    {
        $newPassword = (new UserService)->resetPassword($_POST['userId']);

        return $this->redirect('admin', 'URL to reset password: <code>' . $newPassword . '</code>');
    }
}
