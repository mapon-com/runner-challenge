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

    /** @var StatisticsGraphItemModel[] */
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
            $this->dateCollection[$date->format('Y-m-d')] = new StatisticsGraphItemModel();
        }
    }

    /**
     * @return array
     */
    public function dates(): array
    {
        return array_keys($this->dateCollection);
    }

    public function formattedDates(): array
    {
        $dates = [];
        foreach ($this->dates() as $d) {
            $dates[] = date('j M', strtotime($d));
        }

        return $dates;
    }

    public function getDistanceDataset(): array
    {
        $data = [];
        foreach ($this->dateCollection as $item) {
            $data[] = $item->distance;
        }

        return $data;
    }

    public function getDurationDataset(): array
    {
        $data = [];
        foreach ($this->dateCollection as $item) {
            $data[] = $item->duration;
        }

        return $data;
    }

    public function getActivityDataset(): array
    {
        $data = [];
        foreach ($this->dateCollection as $item) {
            $data[] = $item->activities;
        }

        return $data;
    }

    public function getParticipantDataset(): array
    {
        $data = [];
        foreach ($this->dateCollection as $item) {
            $data[] = $item->participants;
        }

        return $data;
    }

    public function fill(array $rows)
    {
        foreach ($rows as $row) {
            if (!isset($this->dateCollection[$row['activity_date']])) {
                continue;
            }

            $graphItem = new StatisticsGraphItemModel();
            $graphItem->activities = (int)$row['daily_count'];
            $graphItem->participants = (int)$row['daily_participants'];
            $graphItem->duration = round((int)$row['daily_duration'] / 3600, 2);
            $graphItem->distance = round((float)$row['daily_distance'] / 1000, 2);

            $this->dateCollection[$row['activity_date']] = $graphItem;
        }
    }
}
