<?php

namespace App\Services;

use App\Models\ActivityModel;
use App\Models\ChallengeModel;
use App\Models\UserModel;
use Exception;
use InvalidArgumentException;
use RedBeanPHP\R;
use Waddle\Parsers\GPXParser;

class ActivityService
{
    public function upload(
        UserModel $user,
        ChallengeModel $challenge,
        string $filename,
        string $pathname,
        string $activityUrl,
        string $comment,
        ?string $photoPathname
    ): bool {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, ['gpx'])) {
            throw new InvalidArgumentException('Invalid file extension. Has to be or GPX');
        }

        if (!filter_var($activityUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid activity URL. Make sure to copy the full URL from your browser.');
        }

        $imageId = null;
        if ($photoPathname) {
            $imageId = (new ImageService)->upload($photoPathname, 'images')->id;
        }

        try {
            /** @noinspection PhpParamsInspection */
            $result = (new GPXParser)->parse($pathname);
        } catch (Exception $e) {
            throw new InvalidArgumentException('The activity file could not be read.');
        }

        $content = file_get_contents($pathname);
        $md5 = md5($content);

        if (R::findOne('files', 'md5 = ?', [$md5])) {
            //throw new InvalidArgumentException('This activity has already been uploaded');
        }

        $file = R::dispense('files');
        $file->challenge_id = $challenge->id;
        $file->content = $content;
        $file->md5 = $md5;
        $file->user_id = $user->id;
        $file->created_at = time();
        $file->original_filename = $filename;
        R::store($file);

        $activity = new ActivityModel;
        $activity->challengeId = $challenge->id;
        $activity->userId = $user->id;
        $activity->fileId = $file->id;
        $activity->activityUrl = $activityUrl;
        $activity->imageId = $imageId;
        $activity->comment = mb_substr(trim($comment), 0, 500);
        $activity->distance = $result->getTotalDistance();
        $activity->averageSpeed = $result->getAverageSpeedInKPH();
        $activity->maxSpeed = $result->getMaxSpeedInKPH();
        $activity->duration = $result->getTotalDuration();
        $activity->activityAt = $result->getStartTime('U');
        $activity->createdAt = time();
        $activity->deletedAt = null;
        $activity->save();

        $this->notifyAboutActivity($user, $activity);

        return true;
    }

    /**
     * @param UserModel $user
     * @return ActivityModel[]
     */
    public function getActivities(UserModel $user): array
    {
        $beans = R::findLike('activities', [
            'user_id' => $user->id,
        ], 'ORDER BY created_at DESC');

        return array_map(function ($bean) {
            return ActivityModel::fromBean($bean);
        }, $beans);
    }

    /**
     * Is uploading enabled
     *
     * @param ChallengeModel $challenge
     * @return bool
     */
    public function canUpload(?ChallengeModel $challenge): bool
    {
        if ($challenge && !$challenge->isOpen()) {
            return false;
        }

        return (new SettingsService)->get('can_upload', false);
    }

    /**
     * Enable / Disable uploading
     * @param bool $canUpload
     * @return bool
     */
    public function setUpload(bool $canUpload)
    {
        return (new SettingsService)->set('can_upload', $canUpload);
    }

    private function notifyAboutActivity(UserModel $user, ActivityModel $activity)
    {
        $message = ":fire: *{$user->name}* just logged *{$activity->getReadableDistance()}* in *{$activity->getReadableDuration()}*.";
        $message .= " {$this->getQuote()}";

        [$name] = explode(' ', $user->name);

        $attachments = [];

        if ($activity->comment) {
            $attachments[] = [
                'text' => "{$name} says: _{$activity->comment}_",
                'color' => "good",
            ];
        }

        $image = $activity->getImage();
        if ($image) {
            $attachments[] = [
                "fallback" => "$name also uploaded a photo - " . $image->getLargeUrl(),
                "title" => "$name also uploaded a photo!",
                "title_link" => $image->getLargeUrl(),
                "text" => " ",
                "image_url" => $image->getSmallUrl(),
                "color" => "#764FA5",
            ];
        }

        (new Slack)->send($message, $attachments);
    }

    private function getQuote(): string
    {
        $q = [
            'Strong is what happens when you run out of weak.',
            'The hardest part is walking out the front door.',
            'The Price of Excellence is discipline.',
            'Too fit to quit.',
            'Use it or lose it.',
            'Willpower knows no obstacles.',
            'Why put off feeling good?',
            'Get a jump on your day.',
            'Your body hears everything that your mind says.',
            'Fitness is not a destination it is a way of life.',
            'Get in. Get fit, and get on with life.',
            'It never gets easier. You just get strong.',
            'Love yourself enough to work harder.',
            'Make yourself stronger than your excuses.',
            'No Pain. No Gain.',
            'Move it or lose it.',
            'No one ever drowned in sweat.',
            'Your sweat is your fat crying. Keep it up.',
            'All it takes is all you got.',
        ];

        shuffle($q);

        return array_pop($q);
    }

    public function deleteActivity(UserModel $user, int $activityId): bool
    {
        $bean = R::findOne('activities', 'id = ? AND user_id = ?', [(int)$activityId, (int)$user->id]);

        if (!$bean) {
            throw new InvalidArgumentException('Entry not found');
        }

        $activity = ActivityModel::fromBean($bean);
        $activity->delete();

        return true;
    }
}