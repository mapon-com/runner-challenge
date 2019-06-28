<?php

namespace App\Services;

use App\Models\UserModel;
use InvalidArgumentException;
use RedBeanPHP\R;

class UserService
{
    public function register($email, $password, $name): UserModel
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$password || !$name) {
            throw new InvalidArgumentException('Bad email, password or name.');
        }

        if ($this->findUser($email)) {
            throw new InvalidArgumentException('Email already registered');
        }

        $user = new UserModel;

        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->name = trim($name);
        $user->isAdmin = 0;
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
            return $this->findById($id);
        }

        return null;
    }

    public function logOut()
    {
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
}