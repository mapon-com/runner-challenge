<?php

namespace App\Services;

class MessageService
{
    /** @var Slack */
    private $slack;

    public function __construct()
    {
        $this->slack = new Slack;
    }

    /**
     * Send a generic message announcement
     *
     * @param string $message
     * @return bool
     */
    public function send(string $message): bool
    {
        $message = trim($message);
        if (!$message) {
            return false;
        }
        return $this->slack->send($message);
    }
}