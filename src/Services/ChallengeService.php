<?php

namespace App\Services;

use App\Models\ChallengeModel;
use Carbon\Carbon;

class ChallengeService
{
    /**
     * @return ChallengeModel|null
     */
    public function getCurrent(): ?ChallengeModel
    {
        return $this->getById(2);
    }

    public function getById(int $challengeId): ?ChallengeModel
    {
        foreach ($this->all() as $c) {
            if ($c->id === $challengeId) {
                return $c;
            }
        }
        return null;
    }

    /**
     * @return ChallengeModel[]
     */
    private function all(): array
    {
        $all = [];

        $challenge = new ChallengeModel;
        $challenge->id = 1;
        $challenge->openFrom = Carbon::createFromDate(2019, 7, 8, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2019, 7, 29, 'Europe/Riga')->setTime(23, 59, 59);
        $all[] = $challenge;

        $challenge = new ChallengeModel;
        $challenge->id = 2;
        $challenge->openFrom = Carbon::createFromDate(2019, 8, 6, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2019, 8, 31, 'Europe/Riga')->setTime(23, 59, 59);
        $all[] = $challenge;

        return $all;
    }

}