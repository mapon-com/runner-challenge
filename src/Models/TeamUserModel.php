<?php

namespace App\Models;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class TeamUserModel
{
    const TABLE = 'teamsusers';

    /** @var int */
    public $id;
    /** @var int */
    public $teamId;
    /** @var int */
    public $userId;

    public function save()
    {
        $bean = R::dispense(self::TABLE);

        if ($this->id) {
            $bean->id = $this->id;
        }

        $bean->team_id = $this->teamId;
        $bean->user_id = $this->userId;

        R::store($bean);

        $this->id = $bean->id;
    }

    public static function fromBean(OODBBean $bean): TeamUserModel
    {
        $m = new self;

        $m->id = $bean->id;
        $m->teamId = $bean->team_id;
        $m->userId = $bean->user_id;

        return $m;
    }

    public function delete()
    {
        R::trash(self::TABLE, $this->id);
    }

    /**
     * @param int $teamId
     * @param int $userId
     * @return TeamUserModel|null
     */
    public static function findOne(int $teamId, int $userId)
    {
        $bean = R::findOne(self::TABLE, 'team_id = ? AND user_id = ?', [$teamId, $userId]);
        if ($bean) {
            return self::fromBean($bean);
        }
        return null;
    }
}