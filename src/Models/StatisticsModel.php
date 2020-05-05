<?php

namespace App\Models;

use Carbon\Carbon;

class StatisticsModel
{
    /** @var float  */
    public $totalDistance = 0.0;

    /** @var int  */
    public $totalDuration = 0;

    /** @var float  */
    public $averageDistance = 0.0;

    /** @var float  */
    public $averageDuration = 0.0;

    /** @var float  */
    public $averageSpeed = 0.0;

    /** @var int  */
    public $totalActivities = 0;

    /** @var int  */
    public $activitiesPerDay = 0;

    /** @var float  */
    public $distancePerDay = 0.0;

    /** @var float  */
    public $durationPerDay = 0.0;

    /** @var int  */
    public $activitiesPerUser = 0;

    /** @var float  */
    public $distancePerUser = 0.0;

    /** @var float  */
    public $durationPerUser = 0.0;

    /** @var int  */
    protected $participants = 0;

    /** @var int  */
    protected $days = 0;

    /** @var Carbon */
    protected $start;

    /** @var Carbon */
    protected $end;

    public function __construct(ChallengeModel $challenge, int $participants)
    {
        $this->participants = $participants;
        $this->start = clone $challenge->openFrom;
        $this->end = new Carbon('now', $this->start->timezone);
        $this->end->setTime(23, 59, 59);

        $this->days = $this->end->diffInDays($this->start);
    }

    public function calculateDailyAverages()
    {
        if (!$this->days) {
            return;
        }

        $this->activitiesPerDay = round($this->totalActivities / $this->days);
        $this->distancePerDay = round($this->totalDistance / $this->days, 2);
        $this->durationPerDay = round($this->totalDuration / $this->days, 2);
    }

    public function calculateUserAverages()
    {
        if (!$this->participants) {
            return;
        }

        $this->activitiesPerUser = round($this->totalActivities / $this->participants);
        $this->distancePerUser = round($this->totalDistance / $this->participants, 2);
        $this->durationPerUser = round($this->totalDuration / $this->participants, 2);
    }

    /**
     * @return StatisticsModelReadable
     */
    public function toReadable(): StatisticsModelReadable
    {
        $readable = new StatisticsModelReadable();
        $readable->participants = $this->participants;
        $readable->totalActivities = $this->totalActivities;
        $readable->activitiesPerUser = $this->activitiesPerUser;
        $readable->activitiesPerDay = $this->activitiesPerDay;
        $readable->setTotalDistance($this->totalDistance);
        $readable->setAvgDistance($this->averageDistance);
        $readable->setDistancePerDay($this->distancePerDay);
        $readable->setDistancePerUser($this->distancePerUser);
        $readable->setTotalDuration($this->totalDuration);
        $readable->setAvgDuration($this->averageDuration);
        $readable->setDurationPerDay($this->durationPerDay);
        $readable->setDurationPerUser($this->durationPerUser);
        $readable->setAverageSpeed($this->averageSpeed);

        return $readable;
    }
}
