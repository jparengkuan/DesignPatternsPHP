<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

/**
 * Minimal abstraction for any synchronous HTTP client.
 *
 * Implementations may call cURL directly, wrap Guzzle, Symfony HttpClient,
 * or even provide an in-memory fake for testing.  The single responsibility
 * is to turn an HTTP request into a {@see Response} object.
 */
interface HttpClient
{
    /**
     * Sends an HTTP request and returns the raw response.
     *
     * @param string               $method  HTTP verb (GET, POST, PUT, PATCH…).
     * @param string               $url     Fully-qualified request URL.
     * @param array<string, mixed> $options Transport-specific options
     *                                      (timeout, headers, body, etc.).
     *
     * @return Response The HTTP response wrapper with status code and body.
     */
    public function request(string $method, string $url, array $options = []): Response;
}
