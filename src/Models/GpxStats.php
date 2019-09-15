<?php

namespace App\Models;

class GpxStats
{
    /**
     * @var int Timestamp
     */
    public $startTime = 0;
    /**
     * @var float Meters
     */
    public $distance = 0;
    /**
     * @var int Seconds
     */
    public $duration = 0;

    public function getAverageSpeedKmh(): float
    {
        return ($this->distance / 1000) / ($this->duration / 3600);
    }
}