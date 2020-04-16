<?php

namespace App\Services;

use App\Models\GpxStats;
use DateTime;
use phpGPX\Models\Point;
use phpGPX\phpGPX;

class GpxParser
{
    /**
     * @param $pathname
     * @param bool $isWalking
     * @return GpxStats
     */
    public function parse($pathname, bool $isWalking): GpxStats
    {
        $parsed = (new phpGPX())->load($pathname);

        $stats = new GpxStats();

        foreach ($parsed->tracks as $v) {
            foreach ($v->segments as $v2) {
                $validPoints = [];

                foreach ($v2->points as $key => $point) {
                    // There may be point jumps, so we skip points that are from a huge difference
                    if (!$isWalking || $point->difference <= 500) {
                        if (!$stats->startTime || $stats->startTime > $point->time->getTimestamp()) {
                            $stats->startTime = $point->time->getTimestamp();
                        }
                        $validPoints[] = $point;
                    }
                }

                $distance = $this->getPointDistance($validPoints);

                if ($distance) {
                    $stats->duration += $this->getPointDuration($validPoints);
                    $stats->distance += $distance;
                }
            }
        }

        return $stats;
    }

    /**
     * @param Point[] $points
     * @return int Seconds
     */
    private function getPointDuration(array $points): int
    {
        /** @var DateTime $minTime */
        $minTime = null;
        /** @var DateTime $maxTime */
        $maxTime = null;

        foreacH ($points as $point) {
            if (!$minTime || $minTime->getTimestamp() > $point->time->getTimestamp()) {
                $minTime = $point->time;
            }
            if (!$maxTime || $maxTime->getTimestamp() < $point->time->getTimestamp()) {
                $maxTime = $point->time;
            }
        }

        return $maxTime->getTimestamp() - $minTime->getTimestamp();
    }

    /**
     * @param array $points
     * @return float Meters
     */
    private function getPointDistance(array $points): float
    {
        return array_reduce($points, function ($carry, Point $point) {
            $carry += $point->difference;
            return $carry;
        }, 0);
    }

    /**
     * @param $gpxFile
     * @return array
     */
    public function getCoordinates($gpxFile): array
    {
        $parsed = phpGPX::parse($gpxFile);
        $coords = [
            'points' => []
        ];
        foreach ($parsed->tracks as $v) {
            if (!isset($coords['start']) && $v->stats->startedAt) {
                $coords['start'] = $v->stats->startedAt->format('D, M j, Y G:i');
            }
            if ($v->stats->finishedAt) {
                $coords['end'] = $v->stats->finishedAt->format('D, M j, Y G:i');
            }
            $coords['track'] = $v->name ?? '';

            foreach ($v->segments as $v2) {
                foreach ($v2->points as $key => $point) {
                    $coords['points'][] = [
                        $point->latitude,
                        $point->longitude
                    ];
                }
            }
        }

        return $coords;
    }

}