<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

/**
 * Decorator that retries transient 5xx responses using exponential back-off.
 *
 * **Back-off formula**
 * ```
 * delay = baseDelayMs × 2^(attempt − 1)
 * ```
 *
 * With the defaults (`maxAttempts = 3`, `baseDelayMs = 100`), timings are:
 *
 * | Attempt | Delay before the call | Notes                       |
 * |---------|-----------------------|-----------------------------|
 * | 1       | 0 ms                  | first try                   |
 * | 2       | 100 ms                | 1st retry                   |
 * | 3       | 200 ms                | last retry (then give up)   |
 *
 * Each retry prints a line such as:
 * ```
 * Retry in 200 ms (attempt 2)…
 * ```
 * No delay is introduced after the final attempt.
 * The first non-5xx response (2xx or 4xx) is returned immediately.
 */
class RetryDecorator extends HttpClientDecorator
{
    /**
     * @param HttpClient $httpClient Client instance being decorated.
     * @param int $maxAttempts Maximum number of attempts (≥ 1).
     * @param int $baseDelayMs Base delay for the 2¹ back-off, in ms.
     */
    public function __construct(
        HttpClient  $httpClient,
        private int $maxAttempts = 3,
        private int $baseDelayMs = 100
    )
    {
        parent::__construct($httpClient);
    }

    /**
     * {@inheritDoc}
     *
     * Retries on 5xx; stops on anything below 500 or after `$maxAttempts`.
     *
     * @param array<string, mixed> $options Transport-specific options.
     */
    public function request(string $method, string $url, array $options = []): Response
    {
        $attempt = 0;

        do {
            $attempt++;

            $resp = parent::request($method, $url, $options);

            // Success or client error → return right away
            if ($resp->statusCode < 500) {
                return $resp;
            }

            // Still failing; plan another attempt if allowed
            if ($attempt < $this->maxAttempts) {
                $delay = $this->baseDelayMs * (2 ** ($attempt - 1));
                echo sprintf('Retry in %d ms (attempt %d)…%s', $delay, $attempt, PHP_EOL);
                usleep($delay * 1000); // Convert milliseconds to microseconds
            }
        } while ($attempt < $this->maxAttempts);

        // Ran out of attempts — bubble up the last 5xx response
        return $resp;
    }
}
