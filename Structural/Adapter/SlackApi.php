<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

/**
 * Class SlackApi
 *
 * Represents a simplified Slack API client for demonstration purposes.
 * This is used within the Adapter pattern to simulate message dispatching.
 *
 * Responsibilities:
 *  - Simulates authentication with a given token
 *  - Collects sent messages internally
 *
 * @package DesignPatterns\Structural\Adapter
 */
class SlackApi
{
    /**
     * Base URL or endpoint of the Slack API (not used in simulation).
     */
    protected string $slackApi;

    /**
     * OAuth token used to simulate authentication.
     */
    protected string $token;

    /**
     * Stores messages sent during the session.
     * Each message is an associative array:
     *  - 'title'   => string
     *  - 'message' => string
     *
     * @var array<int, array{title: string, message: string}>
     */
    protected array $sentMessages = [];

    /**
     * Tracks whether the client is currently authenticated.
     */
    protected bool $loggedIn = false;

    /**
     * Initializes the API client with a base URL and token.
     *
     * @param string $slackApi Base URL or endpoint of the Slack API.
     * @param string $token    OAuth token to simulate login.
     */
    public function __construct(string $slackApi, string $token)
    {
        $this->slackApi = $slackApi;
        $this->token = $token;
    }

    /**
     * Sends a message by storing it in an internal buffer.
     *
     * @param string $title   Message title (used as header).
     * @param string $message Message body content.
     *
     * @return void
     */
    public function send(string $title, string $message): void
    {
        $this->sentMessages[] = [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * Simulates a login attempt using the provided token.
     *
     * @throws \Exception If the token is invalid.
     *
     * @return void
     */
    public function login(): void
    {
        if ($this->token === 'valid_token') {
            $this->loggedIn = true;
        } else {
            throw new \Exception('Invalid token provided');
        }
    }

    /**
     * Returns all messages that were sent during this session.
     *
     * @return array<int, array{title: string, message: string}>
     */
    public function getSentMessages(): array
    {
        return $this->sentMessages;
    }

    /**
     * Indicates whether the client is authenticated.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }
}
