<?php

namespace App;

use App\Models\UserModel;
use Exception;
use InvalidArgumentException;
use RedBeanPHP\R;
use Waddle\Parsers\GPXParser;

class TrackService
{
    public function upload(
        UserModel $user,
        string $filename,
        string $pathname,
        string $workoutUrl,
        string $comment
    ): bool {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, ['gpx'])) {
            throw new InvalidArgumentException('Invalid file extension. Has to be GPX or TCX');
        }

        try {
            /** @noinspection PhpParamsInspection */
            $result = (new GPXParser)->parse($pathname);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Could not parse workout file');
        }

        $file = R::dispense('files');
        $file->content = file_get_contents($pathname);
        $file->md5 = md5($file->gpx);
        $file->user_id = $user->id;
        $file->created_at = time();
        $file->original_filename = $filename;
        R::store($file);

        $activity = R::dispense('activities');
        $activity->user_id = $user->id;
        $activity->file_id = $file->id;
        $activity->workout_url = $workoutUrl;
        $activity->comment = mb_substr(trim($comment), 0, 500);
        $activity->distsance = $result->getTotalDistance();
        $activity->average_speed = $result->getAverageSpeedInKPH();
        $activity->max_speed = $result->getMaxSpeedInKPH();
        $activity->duration = $result->getTotalDuration();
        $activity->created_at = time();
        $activity->deleted_at = null;
        R::store($activity);

        return true;
    }
}