<?php

namespace App\Models;

use RedBeanPHP\OODBBean;

class ActivityModel
{
    public $id;
    public $userId;
    public $fileId;
    public $workoutUrl;
    public $comment;
    public $distance;
    public $averageSpeed;
    public $maxSpeed;
    public $duration;
    public $activityAt;
    public $createdAt;
    public $deletedAt;

    public static function fromBean(OODBBean $bean): ActivityModel
    {
        $m = new self;

        $m->id = $bean->id;
        $m->userId = $bean->user_id;
        $m->fileId = $bean->file_id;
        $m->workoutUrl = $bean->workout_url;
        $m->comment = $bean->comment;
        $m->distance = $bean->distance;
        $m->averageSpeed = $bean->average_speed;
        $m->maxSpeed = $bean->max_speed;
        $m->duration = $bean->duration;
        $m->activityAt = $bean->activityAt;
        $m->createdAt = $bean->created_at;
        $m->deletedAt = $bean->deleted_at;

        return $m;
    }
}