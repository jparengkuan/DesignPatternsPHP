<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator\Tests;

use DesignPatterns\Structural\Decorator\CurlHttpClient;
use DesignPatterns\Structural\Decorator\HttpClient;
use DesignPatterns\Structural\Decorator\LoggingDecorator;
use DesignPatterns\Structural\Decorator\Response;
use DesignPatterns\Structural\Decorator\RetryDecorator;
use PHPUnit\Framework\TestCase;

/**
 * DecoratorTest
 *
 * Execute with:
 *   vendor/bin/phpunit --filter DecoratorTest
 */
final class DecoratorTest extends TestCase
{
    /* -------------------------------------------------------------
     *  DecoratorClientDemo
     * ----------------------------------------------------------- */
    public function testDecoratorClientDemo(): void
    {
        $client = new RetryDecorator(
            new LoggingDecorator(
                new CurlHttpClient()
            ),
            maxAttempts: 4
        );

        $response = $client->request('GET', 'https://example.com/api/stats');
        $this->assertInstanceOf(Response::class, $response);
    }

    /* -------------------------------------------------------------
     *  CurlHttpClient (concrete component)
     * ----------------------------------------------------------- */

    public function testCurlClientReturnsResponseAndPrintsMessage(): void
    {
        $client = new CurlHttpClient();

        ob_start();
        $resp   = $client->request('GET', 'https://example.com');
        $output = ob_get_clean();

        $this->assertInstanceOf(Response::class, $resp);
        $this->assertSame(200, $resp->statusCode);
        $this->assertSame('{"data":"hello"}', $resp->body);
        $this->assertStringStartsWith('CURL → GET https://example.com', $output);
    }

    /* -------------------------------------------------------------
     *  LoggingDecorator
     * ----------------------------------------------------------- */

    public function testLoggingDecoratorDelegatesAndLogs(): void
    {
        $inner = $this->createMock(HttpClient::class);
        $inner->expects($this->once())
            ->method('request')
            ->with('POST', 'https://api.example.com', ['foo' => 'bar'])
            ->willReturn(new Response(201, 'CREATED'));

        $client = new LoggingDecorator($inner);

        ob_start();
        $resp   = $client->request('POST', 'https://api.example.com', ['foo' => 'bar']);
        $output = ob_get_clean();

        $this->assertSame(201, $resp->statusCode);
        $this->assertStringContainsString('[201] POST https://api.example.com', $output);
        $this->assertMatchesRegularExpression('/\(\d+\.\dms\)/', $output);
    }

    /* -------------------------------------------------------------
     *  RetryDecorator – success path
     * ----------------------------------------------------------- */

    public function testRetryDecoratorReturnsImmediatelyOnSuccess(): void
    {
        $inner = $this->createMock(HttpClient::class);
        $inner->expects($this->once())
            ->method('request')
            ->willReturn(new Response(200, 'OK'));

        $client = new RetryDecorator($inner, maxAttempts: 3, baseDelayMs: 1);

        ob_start();                     // suppress delay messages
        $resp = $client->request('GET', '/health');
        ob_end_clean();

        $this->assertSame(200, $resp->statusCode);
    }

    /* -------------------------------------------------------------
     *  RetryDecorator – retries until success
     * ----------------------------------------------------------- */

    public function testRetryDecoratorRetriesUntilSuccess(): void
    {
        $inner = $this->createMock(HttpClient::class);
        $inner->expects($this->exactly(3))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                new Response(500, 'ERR'),
                new Response(502, 'Bad Gateway'),
                new Response(200, 'OK')
            );

        $client = new RetryDecorator($inner, maxAttempts: 3, baseDelayMs: 1);

        ob_start();
        $resp   = $client->request('GET', '/resource');
        $output = ob_get_clean();

        $this->assertSame(200, $resp->statusCode);
        $this->assertStringContainsString('Retry in', $output);
    }

    /* -------------------------------------------------------------
     *  RetryDecorator – gives up after maxAttempts
     * ----------------------------------------------------------- */

    public function testRetryDecoratorStopsAfterMaxAttempts(): void
    {
        $inner = $this->createMock(HttpClient::class);
        $inner->expects($this->exactly(3))
            ->method('request')
            ->willReturn(new Response(503, 'Service Unavailable'));

        $client = new RetryDecorator($inner, maxAttempts: 3, baseDelayMs: 1);

        ob_start();
        $resp   = $client->request('GET', '/resource');
        $output = ob_get_clean();

        $this->assertSame(503, $resp->statusCode);
        $this->assertSame(2, substr_count($output, 'Retry in'));  // only before the last attempt
    }
}
