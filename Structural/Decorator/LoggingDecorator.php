<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

/**
 * Decorator that logs each HTTP request with execution time.
 *
 * Example log line (printed to STDOUT):
 * ```
 * [200] GET https://example.com/api (12.7ms)
 * ```
 * Place it anywhere in a decorator chain to gain transparent request/response
 * visibility without touching the underlying {@see HttpClient}.
 */
class LoggingDecorator extends HttpClientDecorator
{
    /**
     * {@inheritDoc}
     *
     * Adds wall-clock timing around the delegated call and prints one formatted
     * line to STDOUT.  It makes **no** changes to the response returned.
     *
     * @param array<string, mixed> $options Additional transport options.
     */
    public function request(string $method, string $url, array $options = []): Response
    {
        $start = microtime(true);

        $resp  = parent::request($method, $url, $options);

        $took  = number_format((microtime(true) - $start) * 1000, 1);
        printf('[%d] %s %s (%sms)' . PHP_EOL, $resp->statusCode, $method, $url, $took);

        return $resp;
    }
}
