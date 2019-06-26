<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App;

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

        $bean = R::dispense('users');

        $bean->email = $email;
        $bean->password = password_hash($password, PASSWORD_DEFAULT);
        $bean->name = trim($name);

        R::store($bean);

        return UserModel::fromBean($bean);
    }

    public function findUser($email): ?UserModel
    {
        $bean = R::findOne('users', 'email = ?', [$email]);

        if ($bean) {
            return UserModel::fromBean($bean);
        }

        return null;
    }

    public function logIn(UserModel $user)
    {
        $_SESSION['email'] = $user->email;
    }

    public function getLoggedIn(): ?UserModel
    {
        $email = $_SESSION['email'] ?? null;
        if (!$email) {
            return null;
        }

        return $this->findUser($email);
    }

    public function logOut()
    {
        unset($_SESSION['email']);
    }
}