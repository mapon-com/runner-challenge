<?php

namespace App\Services;

use App\Models\ChallengeModel;
use App\Models\StatisticsGraphModel;
use App\Models\StatisticsModel;
use App\Models\TeamModel;
use RedBeanPHP\R;

class StatisticsService
{
    /**
     * @param ChallengeModel $challenge
     * @return StatisticsModel
     */
    public function getChallengeStats(ChallengeModel $challenge): StatisticsModel
    {
        $bindings = [
            'challengeId' => $challenge->id,
        ];

        $totalsRaw = R::getRow("
            SELECT 
                   SUM(distance) AS total_distance, 
                   SUM(duration) AS total_duration, 
                   COUNT(a.id) AS activity_count,
                   AVG(a.distance) AS avg_distance,
                   AVG(a.duration) AS avg_duration,
                   AVG(a.average_speed) AS avg_speed,
                   COUNT(DISTINCT a.user_id) AS active_participants
            FROM activities a
            WHERE a.challenge_id = :challengeId
            AND a.deleted_at IS NULL
        ", $bindings);

        return $this->parseStats($totalsRaw, $challenge);
    }

    /**
     * @param array $totalsRaw
     * @param ChallengeModel $challenge
     * @return StatisticsModel
     */
    private function parseStats(array $totalsRaw, ChallengeModel $challenge): StatisticsModel
    {
        $stats = new StatisticsModel($challenge, (int)$totalsRaw['active_participants']);
        $stats->totalDuration = (int)$totalsRaw['total_duration'];
        $stats->totalActivities = (int)$totalsRaw['activity_count'];
        $stats->totalDistance = (float)$totalsRaw['total_distance'];
        $stats->averageDistance = (float)$totalsRaw['avg_distance'];
        $stats->averageDuration = (float)$totalsRaw['avg_duration'];
        $stats->averageSpeed = (float)$totalsRaw['avg_speed'];

        $stats->calculateDailyAverages();
        $stats->calculateUserAverages();

        return $stats;
    }

    /**
     * @param ChallengeModel $challenge
     * @return StatisticsGraphModel
     */
    public function getChallengeStatsGraph(ChallengeModel $challenge): StatisticsGraphModel
    {
        $bindings = [
            'challengeId' => $challenge->id,
        ];

        $totalsRaw = R::getAll("
            SELECT 
                   date(a.activity_at, 'unixepoch') AS activity_date,
                   SUM(a.distance) AS daily_distance, 
                   SUM(a.duration) AS daily_duration, 
                   COUNT(a.id) AS daily_count,
                   COUNT(DISTINCT a.user_id) AS daily_participants
            FROM activities a
            WHERE a.challenge_id = :challengeId
            AND a.deleted_at IS NULL
            GROUP BY activity_date
        ", $bindings);

        return $this->parseGraphData($totalsRaw, $challenge);
    }

    /**
     * @param array $totalsRaw
     * @param ChallengeModel $challenge
     * @return StatisticsGraphModel
     */
    private function parseGraphData(array $totalsRaw, ChallengeModel $challenge): StatisticsGraphModel
    {
        $graph = new StatisticsGraphModel($challenge);
        $graph->fill($totalsRaw);

        return $graph;
    }
}
