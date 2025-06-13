<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

/**
 * Concrete HTTP client that delegates work to PHP’s cURL extension.
 *
 * This is the thinnest possible demo implementation: it just prints
 * what it *would* send and always returns a fixed 200/JSON payload.
 * In production you would build a real cURL handle, map any transport
 * errors to exceptions, and parse headers / body into a richer
 * Response object.
 */
class CurlHttpClient implements HttpClient
{
    /**
     * Perform an HTTP request.
     *
     * @param string               $method  HTTP verb (e.g. GET, POST, PUT).
     * @param string               $url     Fully-qualified request URL.
     * @param array<string, mixed> $options Arbitrary transport options
     *                                      (headers, timeout, body, etc.).
     *
     * @return Response                        A Response object containing
     *                                         the status code and raw body.
     */
    public function request(string $method, string $url, array $options = []): Response
    {
        // In real code the cURL handle would be configured & executed here.
        echo sprintf('CURL → %s %s …%s', $method, $url, PHP_EOL);

        return new Response(200, '{"data":"hello"}');
    }
}
