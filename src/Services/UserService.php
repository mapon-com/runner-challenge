<?php

namespace App\Services;

use App\Models\UserModel;
use InvalidArgumentException;
use RedBeanPHP\R;

class UserService
{
    public function register($email, $password, $name): UserModel
    {
        $email = trim(mb_strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$password || !$name) {
            throw new InvalidArgumentException('Bad email, password or name.');
        }

        if ($this->findUser($email)) {
            throw new InvalidArgumentException('Email already registered');
        }

        $shouldBeAdmin = in_array($email, explode(',', getenv('ADMIN_EMAILS')), true);

        $user = new UserModel;

        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->name = trim($name);
        $user->isAdmin = $shouldBeAdmin;

        $user->save();

        return $user;
    }

    public function findUser($email): ?UserModel
    {
        $bean = R::findOne('users', 'email = ?', [$email]);

        if ($bean) {
            return UserModel::fromBean($bean);
        }

        return null;
    }

    public function findById($id): ?UserModel
    {
        $bean = R::findOne('users', 'id = ?', [$id]);

        if ($bean) {
            return UserModel::fromBean($bean);
        }

        return null;
    }

    public function logIn(UserModel $user)
    {
        $_SESSION['userId'] = $user->id;
    }

    public function getLoggedIn(): ?UserModel
    {
        $id = $_SESSION['userId'] ?? null;
        if ($id) {
            $user = $this->findById($id);
            if (!empty($_SESSION['impersonator'])) {
                $user->isImpersonating = true;
            }
            return $user;
        }

        return null;
    }

    public function logOut()
    {
        $impersonatorUserId = $_SESSION['impersonator'] ?? null;
        if ($impersonatorUserId) {
            unset($_SESSION['impersonator']);
            $_SESSION['userId'] = $impersonatorUserId;
            return;
        }
        unset($_SESSION['userId']);
    }

    /**
     * @return UserModel[]
     */
    public function getAll(): array
    {
        return array_map(function ($bean) {
            return UserModel::fromBean($bean);
        }, R::findAll('users'));
    }

    /**
     * @param $userIds
     * @return UserModel[]
     */
    public function getByIds($userIds): array
    {
        return array_reduce($userIds, function ($carry, int $userId) {
            $user = $this->findById($userId);
            if ($user) {
                $carry[] = $user;
            }
            return $carry;
        }, []);
    }

    public function impersonate(int $userId)
    {
        $current = $this->getLoggedIn();
        $user = $this->findById($userId);

        $this->logIn($user);

        $_SESSION['impersonator'] = $current->id;

        return $user;
    }
}