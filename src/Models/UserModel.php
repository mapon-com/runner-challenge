<?php

namespace App\Models;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class UserModel
{
    public $id;
    public $password;
    public $email;
    public $teamId;
    public $name;
    public $isAdmin;

    public static function fromBean(?OODBBean $bean): UserModel
    {
        $m = new self;

        if (!$bean) {
            return $m;
        }

        $m->id = $bean->id;
        $m->password = $bean->password;
        $m->email = $bean->email;
        $m->teamId = $bean->team_id;
        $m->name = $bean->name;
        $m->isAdmin = (bool)$bean->is_admin;

        return $m;
    }

    public function passwordMatches($password): bool
    {
        return password_verify($password, $this->password);
    }

    public function save()
    {
        $bean = R::dispense('users');

        if ($this->id) {
            $bean->id = $this->id;
        }

        $bean->password = $this->password;
        $bean->email = $this->email;
        $bean->team_id = $this->teamId;
        $bean->name = $this->name;
        $bean->is_admin = (int)$this->isAdmin;

        R::store($bean);

        $this->id = $bean->id;
    }
}