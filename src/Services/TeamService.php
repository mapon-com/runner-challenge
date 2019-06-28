<?php

namespace App\Services;

use App\Models\ChallengeModel;
use App\Models\TeamModel;
use RedBeanPHP\R;

class TeamService
{
    /**
     * @param ChallengeModel $challenge
     * @return TeamModel
     */
    public function addTeam(ChallengeModel $challenge): TeamModel
    {
        $team = R::dispense('teams');
        $team->challenge_id = $challenge->id;
        $team->name = "";
        $team->captain_id = 0;
        $team->image_id = 0;
        $team->created_at = time();

        R::store($team);

        $team->name = 'Team #' . $team->id;
        R::store($team);

        return TeamModel::fromBean($team);
    }

    /**
     * @param ChallengeModel $challenge
     * @return TeamModel[]
     */
    public function getAll(ChallengeModel $challenge): array
    {
        $beans = R::findAll('teams', 'challenge_id = ?', [$challenge->id]);

        return array_map(function ($bean) {
            return TeamModel::fromBean($bean);
        }, $beans);
    }
}