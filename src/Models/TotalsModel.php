<?php


namespace App\Models;


use Carbon\Carbon;
use Carbon\CarbonInterval;

class TotalsModel
{
    /** @var int User or team ID */
    public $id;

    /** @var string User or team name */
    public $name;

    public $distance;

    public $lastActivityAt;

    public $activityCount;

    public $duration;

    public function getReadableLastActivityAt(): string
    {
        if (!$this->lastActivityAt) {
            return '-';
        }
        return Carbon::createFromTimestamp($this->lastActivityAt)->diffForHumans();
    }

    public function getReadableDistance(): string
    {
        return round($this->distance / 1000, 2) . ' km';
    }

    public function getReadableDuration(): string
    {
        if (!$this->duration) {
            return '-';
        }
        $d = $this->duration;

        $days = floor($d / (24 * 3600));
        $hrs = floor(($d - $days * 24 * 3600) / 3600);
        $min = floor(($d - $hrs * 3600) / 60);

        return CarbonInterval::days($days)->hours($hrs)->minutes($min)->forHumans(['short' => true]);
    }
}