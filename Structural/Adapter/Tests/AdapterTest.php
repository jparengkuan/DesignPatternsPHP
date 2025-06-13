<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter\Tests;

use DesignPatterns\Structural\Adapter\SlackApi;
use DesignPatterns\Structural\Adapter\SlackNotification;
use Exception;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    public function testSuccessfulLogin(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'valid_token');
        $slackApi->login();
        $this->assertTrue($slackApi->isLoggedIn());
    }

    public function testInvalidLoginThrowsException(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'invalid_token');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid token provided');

        $slackApi->login();
    }

    public function testMessageIsStoredAfterSend(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'valid_token');
        $slackApi->login();
        $slackApi->send('Test Title', 'Test Message');

        $messages = $slackApi->getSentMessages();
        $this->assertCount(1, $messages);
        $this->assertSame('Test Title', $messages[0]['title']);
        $this->assertSame('Test Message', $messages[0]['message']);
    }

    public function testSlackNotificationSendsStrippedMessage(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'valid_token');
        $notification = new SlackNotification($slackApi, 'valid_token');

        $title = 'My Notification';
        $htmlMessage = '<b>Hello</b> <i>World</i>';

        $notification->send($title, $htmlMessage);

        $sent = $slackApi->getSentMessages();
        $this->assertCount(1, $sent);
        $this->assertSame('Hello World', $sent[0]['message']);
    }

    public function testSlackNotificationHandlesInvalidTokenGracefully(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'invalid_token');
        $notification = new SlackNotification($slackApi, 'invalid_token');

        // Capture error log output
        $this->expectNotToPerformAssertions();

        // Should not throw, just log internally
        $notification->send('Title', 'Message');
    }

    public function testGetLatestSentMessageReturnsLast(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'valid_token');
        $notification = new SlackNotification($slackApi, 'valid_token');

        $notification->send('One', 'First');
        $notification->send('Two', 'Second');

        $this->assertSame('Second', $notification->getLatestSentMessage());
    }

    public function testGetLatestSentMessageReturnsNullIfEmpty(): void
    {
        $slackApi = new SlackApi('https://fake.slack.api', 'valid_token');
        $notification = new SlackNotification($slackApi, 'valid_token');

        $this->assertNull($notification->getLatestSentMessage());
    }
}
