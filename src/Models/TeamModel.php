<?php

namespace App\Models;

use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class TeamModel
{
    const TABLE = 'teams';

    /** @var int */
    public $id;
    /** @var string $captainId User id of captain */
    public $captainId;
    /** @var string $name Name of the team */
    public $name;
    /** @var int $imageId Entry form images */
    public $imageId;
    /** @var int */
    public $challengeId;
    /** @var int */
    public $createdAt;
    /** @var float */
    public $totalDistance;
    /** @var float */
    public $totalDuration;

    public function save()
    {
        $team = R::dispense(self::TABLE);

        if ($this->id) {
            $team->id = $this->id;
        }

        $team->challenge_id = $this->challengeId;
        $team->name = $this->name;
        $team->captain_id = $this->captainId;
        $team->image_id = $this->imageId;
        $team->total_duration = $this->totalDuration;
        $team->total_distance = $this->totalDistance;
        $team->created_at = $this->createdAt;

        R::store($team);

        $this->id = $team->id;
    }

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
        $m->challengeId = $bean->challenge_id;
        $m->imageId = $bean->image_id;
        $m->totalDuration = $bean->total_duration;
        $m->totalDistance = $bean->total_distance;
        $m->createdAt = $bean->created_at;

        return $m;
    }

    public function delete()
    {
        R::trash(self::TABLE, $this->id);
    }

    public function getReadableDistance(): string
    {
        return round($this->totalDistance / 1000, 2) . ' km';
    }

    public function getImageUrl(): ?string
    {
        if ($this->imageId) {
            return route('image') . '?id=' . $this->imageId;
        }
        return null;
    }

    /**
     * @param string|null $sql
     * @param array $bindings
     * @return TeamModel[]
     */
    public static function findAll($sql = null, array $bindings = [])
    {
        $beans = R::findAll(self::TABLE, $sql, $bindings);
        return array_map(function ($bean) {
            return self::fromBean($bean);
        }, $beans);
    }

    /**
     * @param string|null $sql
     * @param array $bindings
     * @return TeamModel|null
     */
    public static function findOne($sql = null, array $bindings = [])
    {
        $bean = R::findOne(self::TABLE, $sql, $bindings);
        return $bean ? self::fromBean($bean) : null;
    }
}