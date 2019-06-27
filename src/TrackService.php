<?php

namespace App;

use App\Models\UserModel;
use InvalidArgumentException;
use RedBeanPHP\R;
use Waddle\Parsers\GPXParser;

class TrackService
{
    public function upload(UserModel $user, string $filename, string $pathname): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, ['gpx'])) {
            throw new InvalidArgumentException('Invalid file extension. Has to be GPX or TCX');
        }

        $parser = new GPXParser;

        try {
            /** @noinspection PhpParamsInspection */
            $result = $parser->parse($pathname);
        } catch (\Exception $e) {
            return null;
        }

        $bean = R::dispense('tracks');

        $bean->user_id = $user->id;
        $bean->distsance = $result->getTotalDistance();
        $bean->average_speed = $result->getAverageSpeedInKPH();
        $bean->max_speed = $result->getMaxSpeedInKPH();
        $bean->duration = $result->getTotalDuration();
        $bean->original_filename = $filename;
        $bean->uploaded_at = time();

        $trackId = R::store($bean);

        $targetPathname = __DIR__ . '/../storage/tracks/' . $trackId . '.' . $extension;
        $dirname = dirname($targetPathname);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777);
        }

        file_put_contents($targetPathname, file_get_contents($pathname));

        return true;
    }
}