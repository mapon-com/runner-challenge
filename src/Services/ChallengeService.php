<?php


namespace App\Services;


use App\Models\ChallengeModel;

class ChallengeService
{
    /**
     * @return ChallengeModel|null
     */
    public function getCurrent(): ?ChallengeModel
    {
        $challenge = new ChallengeModel;
        $challenge->id = 1;

        return $challenge;
    }
}