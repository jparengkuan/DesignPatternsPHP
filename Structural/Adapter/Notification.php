<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

interface Notification
{
    /**
     * Sends a notification message to the specified recipient.
     *
     * @param string $title The title of the notification.
     * @param string $message The message to be sent.
     */
    public function send(string $title, string $message): void;
}
