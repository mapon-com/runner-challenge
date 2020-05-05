<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StatisticsGraphModel
{
    /** @var Carbon */
    protected $start;

    /** @var Carbon */
    protected $end;

    protected $dateCollection = [];

    public function __construct(ChallengeModel $challenge)
    {
        $this->start = clone $challenge->openFrom;
        $this->end = clone $challenge->openUntil;

        $this->initDateCollection();
    }

    protected function initDateCollection()
    {
        $period = new CarbonPeriod($this->start, '1 day', $this->end);
        foreach ($period as $key => $date) {
            $this->dateCollection[$date->format('Y-m-d')] = [];
        }
    }

    /**
     * @return array
     */
    public function dates(): array
    {
        return array_keys($this->dateCollection);
    }
}
