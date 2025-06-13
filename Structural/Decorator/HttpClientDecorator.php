<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

/**
 * Base decorator for {@see HttpClient} implementations.
 *
 * It forwards every call to an “inner” client, letting concrete subclasses
 * inject cross-cutting behaviour (logging, retries, caching, auth headers,
 * rate-limiting, …) before or after delegating to {@see request()}.
 *
 * Usage example:
 * ```
 * $client = new RetryDecorator(
 *     new LoggingDecorator(
 *         new CurlHttpClient()
 *     )
 * );
 * ```
 */
abstract class HttpClientDecorator implements HttpClient
{
    /** The wrapped client that does the actual work. */
    protected HttpClient $httpClient;

    /**
     * @param HttpClient $httpClient Client instance being decorated.
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $options Arbitrary transport options.
     */
    public function request(string $method, string $url, array $options = []): Response
    {
        return $this->httpClient->request($method, $url, $options);
    }
}
