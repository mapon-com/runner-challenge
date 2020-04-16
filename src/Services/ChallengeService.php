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
        return $this->getById(5);
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

        $challenge = new ChallengeModel();
        $challenge->id = 1;
        $challenge->openFrom = Carbon::createFromDate(2019, 7, 8, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2019, 7, 29, 'Europe/Riga')->setTime(23, 59, 59);
        $all[] = $challenge;

        $challenge = new ChallengeModel();
        $challenge->id = 2;
        $challenge->openFrom = Carbon::createFromDate(2019, 8, 6, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2019, 8, 27, 'Europe/Riga')->setTime(23, 59, 59);
        $all[] = $challenge;

        $challenge = new ChallengeModel();
        $challenge->id = 3;
        $challenge->openFrom = Carbon::createFromDate(2019, 9, 6, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2019, 9, 26, 'Europe/Riga')->setTime(23, 59, 59);
        $challenge->isWalking = true;
        $all[] = $challenge;

        $challenge = new ChallengeModel();
        $challenge->id = 4;
        $challenge->openFrom = Carbon::createFromDate(2020, 3, 12, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2020, 4, 2, 'Europe/Riga')->setTime(23, 59, 59);
        $challenge->isWalking = true;
        $all[] = $challenge;

        $challenge = new ChallengeModel();
        $challenge->id = 5;
        $challenge->openFrom = Carbon::createFromDate(2020, 4, 6, 'Europe/Riga')->setTime(0, 0, 0);
        $challenge->openUntil = Carbon::createFromDate(2020, 5, 6, 'Europe/Riga')->setTime(23, 59, 59);
        $challenge->isWalking = true;
        $all[] = $challenge;

        return $all;
    }
}