<?php

namespace App\Services;

use Exception;
use Maknz\Slack\Client;

class Slack
{
    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->client = new Client(getenv('SLACK_WEBHOOK'));
        $this->client->setDefaultChannel(getenv('SLACK_CHANNEL'));
        $this->client->setDefaultIcon(':challenge:');
        $this->client->setDefaultUsername('Challenge');
    }

    public function send(string $message, ?string $color = null, ?string $imageUrl = null): bool
    {
        $attach = [];

        if ($color) {
            $attach += [
                'text' => $message,
                'color' => $color,
            ];
        }

        if ($imageUrl) {
            $attach += [
                'text' => $message,
                'image_url' => $imageUrl,
            ];
        }

        try {
            if ($attach) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->client->attach($attach)->send();
            } else {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->client->send($message);
            }
        } catch (Exception $e) {
            return false;
            // Whoops
        }
        return true;
    }
}