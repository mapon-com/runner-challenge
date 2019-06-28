<?php

namespace App\Models;

use RedBeanPHP\OODBBean;

class TeamModel
{
    /** @var int */
    public $id;

    /** @var string $captainId User id of captain */
    public $captainId;

    /** @var string $name Name of the team */
    public $name;

    /** @var int $imageId Entry form images */
    public $imageId;

    /**
     * @param OODBBean $bean
     * @return TeamModel
     */
    public static function fromBean(OODBBean $bean): TeamModel
    {
        $m = new self;

        $m->id = $bean->id;
        $m->name = $bean->name;
        $m->captainId = $bean->captain_id;
        $m->imageId = $bean->image_id;
        $m->createdAt = $bean->created_at;

        return $m;
    }
}