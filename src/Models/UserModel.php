<?php

namespace App\Models;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class UserModel
{
    public $id;
    public $password;
    public $email;
    public $name;
    public $isAdmin;
    /** @var bool */
    public $isParticipating;
    /** @var string|null */
    public $passwordResetKey;
    /** @var int */
    public $lastVisitedAt;

    /** @var bool Is currently impersonating this user */
    public $isImpersonating;

    public static function fromBean(?OODBBean $bean): UserModel
    {
        $m = new self;

        if (!$bean) {
            return $m;
        }

        $m->id = $bean->id;
        $m->password = $bean->password;
        $m->email = $bean->email;
        $m->name = $bean->name;
        $m->isAdmin = (bool)$bean->is_admin;
        $m->isParticipating = (bool)$bean->is_participating;
        $m->passwordResetKey = $bean->password_reset_key;
        $m->lastVisitedAt = (int)$bean->last_visited_at;

        return $m;
    }

    public static function findById(int $userId): ?UserModel
    {
        $bean = R::findOne('users', 'id = ?', [$userId]);
        return $bean ? self::fromBean($bean) : null;
    }

    public function passwordMatches($password): bool
    {
        return password_verify($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getPasswordResetUrl(): ?string
    {
        if (!$this->passwordResetKey) {
            return null;
        }
        return route('register', true) . '?resetKey=' . $this->passwordResetKey;
    }

    public function save()
    {
        $bean = R::dispense('users');

        if ($this->id) {
            $bean->id = $this->id;
        }

        $bean->password = $this->password;
        $bean->email = $this->email;
        $bean->name = $this->name;
        $bean->is_admin = (int)$this->isAdmin;
        $bean->is_participating = (bool)$this->isParticipating;
        $bean->password_reset_key = $this->passwordResetKey;
        $bean->last_visited_at = $this->lastVisitedAt;

        R::store($bean);

        $this->id = $bean->id;
    }
}