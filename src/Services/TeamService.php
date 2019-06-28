<?php

namespace App\Services;

use App\Models\ChallengeModel;
use App\Models\TeamModel;
use App\Models\UserModel;
use RedBeanPHP\R;

class TeamService
{
    /**
     * @param ChallengeModel $challenge
     * @return TeamModel
     */
    public function addTeam(ChallengeModel $challenge): TeamModel
    {
        $team = new TeamModel;
        $team->challengeId = $challenge->id;
        $team->name = "";
        $team->captainId = 0;
        $team->imageId = 0;
        $team->createdAt = time();
        $team->save();

        $team->name = 'Team #' . $team->id;
        $team->save();

        return $team;
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

    /**
     * @param ChallengeModel $challenge
     * @param int $teamId
     * @return TeamModel|null
     */
    public function getById(ChallengeModel $challenge, int $teamId): ?TeamModel
    {
        $bean = R::findOne('teams', 'id = ? AND challenge_id = ?', [$teamId, $challenge->id]);

        if ($bean) {
            return TeamModel::fromBean($bean);
        }

        return null;
    }

    /**
     * @param TeamModel $team
     * @param UserModel[] $users
     */
    public function assignUsers(TeamModel $team, array $users)
    {
        foreach ($users as $user) {
            if ($user->teamId) {
                continue;
            }
            $user->teamId = $team->id;
            $user->save();
        }
    }

    public function unassignUser(?UserModel $user)
    {
        $user->teamId = null;
        $user->save();
    }
}