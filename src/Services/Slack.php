<?php

namespace App\Services;

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

    public function send(string $message): bool
    {
        try {
            $this->client->send($message);
        } catch (\Exception $e) {
            return false;
            // Whoops
        }
        return true;
    }
}