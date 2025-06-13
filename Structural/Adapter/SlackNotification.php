<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

/**
 * Adapter that allows the application to send formatted notifications through Slack.
 *
 * This class converts the generic {@see Notification} interface into
 * specific calls understood by {@see SlackApi}.
 *
 * @package DesignPatterns\Structural\Adapter
 */
class SlackNotification implements Notification
{
    /**
     * The Slack API client used to perform HTTP requests.
     */
    protected SlackApi $slackApi;

    /**
     * The authentication token for the Slack API.
     */
    protected string $token;

    /**
     * Constructs a new SlackNotification adapter instance.
     *
     * @param SlackApi $slackApi The Slack API client instance.
     * @param string   $token    The OAuth token granting permission to post messages.
     */
    public function __construct(SlackApi $slackApi, string $token)
    {
        $this->slackApi = $slackApi;
        $this->token = $token;
    }

    /**
     * Sends a message to Slack by transforming a generic notification.
     *
     * @param string $title   The title of the notification.
     * @param string $message The body of the notification (HTML tags will be stripped).
     *
     * @return void
     */
    public function send(string $title, string $message): void
    {
        $plainMessage = strip_tags($message);

        try {
            $this->slackApi->login();
            $this->slackApi->send($title, $plainMessage);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
        }
    }

    /**
     * Returns the most recently sent Slack message.
     *
     * @return string|null The latest message if available, or null if none sent.
     */
    public function getLatestSentMessage(): ?string
    {
        $messages = $this->slackApi->getSentMessages();

        if (empty($messages)) {
            return null;
        }

        $lastMessage = end($messages);
        return $lastMessage['message'] ?? null;
    }
}
