<?php

namespace App\Models;

use Carbon\CarbonInterval;

class StatisticsModelReadable
{
    /** @var string  */
    public $totalDistance = '';

    /** @var string */
    public $totalDuration = '';

    /** @var string */
    public $averageDistance = '';

    /** @var string */
    public $averageDuration = '';

    /** @var string */
    public $averageSpeed = '';

    /** @var string */
    public $totalActivities = 0;

    /** @var int */
    public $activitiesPerDay = 0;

    /** @var string */
    public $distancePerDay = '';

    /** @var string */
    public $durationPerDay = '';

    /** @var int */
    public $activitiesPerUser = 0;

    /** @var string */
    public $distancePerUser = '';

    /** @var string */
    public $durationPerUser = '';

    /** @var int */
    public $participants = 0;

    public function setTotalDistance(float $dist)
    {
        $this->totalDistance = $this->formatDistance($dist);
    }

    public function setAvgDistance(float $dist)
    {
        $this->averageDistance = $this->formatDistance($dist);
    }

    public function setDistancePerDay(float $dist)
    {
        $this->distancePerDay = $this->formatDistance($dist);
    }

    public function setDistancePerUser(float $dist)
    {
        $this->distancePerUser = $this->formatDistance($dist);
    }

    protected function formatDistance(float $dist): string
    {
        return round($dist / 1000, 2) . ' km';
    }

    public function setTotalDuration(float $duration)
    {
        $this->totalDuration = $this->formatDuration($duration);
    }

    public function setAvgDuration(float $duration)
    {
        $this->averageDuration = $this->formatDuration($duration);
    }

    public function setDurationPerDay(float $duration)
    {
        $this->durationPerDay = $this->formatDuration($duration);
    }

    public function setDurationPerUser(float $duration)
    {
        $this->durationPerUser = $this->formatDuration($duration);
    }

    protected function formatDuration(float $duration): string
    {
        $hrs = floor($duration / 3600);
        $min = floor(($duration - $hrs * 3600) / 60);

        return CarbonInterval::hours($hrs)->minutes($min)->forHumans(['short' => true]);
    }

    public function setAverageSpeed(float $avgSpeed)
    {
        $this->averageSpeed = $this->formatSpeed($avgSpeed);
    }

    protected function formatSpeed(float $speed): string
    {
        return round($speed, 2) . ' km/h';
    }
}
