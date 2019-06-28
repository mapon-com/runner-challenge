<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
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

    public function getReadableActivityAt(): string
    {
        return Carbon::createFromTimestamp($this->activityAt)->diffForHumans();
    }

    public function getReadableCreatedAt(): string
    {
        return Carbon::createFromTimestamp($this->createdAt)->diffForHumans();
    }

    public function getReadableDistance(): string
    {
        return round($this->distance / 1000, 2) . ' km';
    }

    public function getReadableDuration(): string
    {
        $hrs = floor($this->duration / 3600);
        $min = floor(($this->duration - $hrs * 3600) / 60);

        return CarbonInterval::hours($hrs)->minutes($min)->forHumans(['short' => true]);
    }

    public function getReadableSpeed(): string
    {
        return round($this->averageSpeed, 2) . ' km/h';
    }
}