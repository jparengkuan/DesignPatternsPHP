<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Decorator;

/**
 * Immutable value-object representing a raw HTTP response.
 *
 * It purposefully stays minimal—just the numeric status code and the
 * unparsed body string—so decorators or higher-level clients can decide
 * whether to add header parsing, JSON decoding, streaming, etc.
 */
class Response
{
    /**
     * @param int    $statusCode Standard HTTP status (e.g. 200, 404, 500).
     * @param string $body       Raw body as received from the transport layer.
     */
    public function __construct(
        public int $statusCode,
        public string $body
    ) {
    }
}
