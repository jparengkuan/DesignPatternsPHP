<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Bridge;

use RuntimeException;

/**
 * In-memory stand-in for a real FTP adapter.
 *
 * This class fulfils the {@see StorageAdapter} contract without opening a
 * network socket, making it ideal for unit tests or local development.
 *
 * Because its constructor signature mirrors a real FTP adapter, you can swap
 * it in via dependency-injection or configuration without touching client code
 * in the {@link MediaFile} hierarchy.
 *
 * @implements StorageAdapter
 */
class FtpStorage implements StorageAdapter
{
    /**
     * Simulated remote filesystem.
     *
     * The array key is the *normalised* file path, the value is the raw file
     * contents.
     *
     * @var array<string,string>
     */
    private array $files = [];

    /**
     * Accepts the same parameters as a real FTP adapter but ignores them.
     *
     * @param string $host     FTP host (ignored).
     * @param string $username FTP username (ignored).
     * @param string $password FTP password (ignored).
     * @param int    $port     FTP port    (ignored).
     * @param int    $timeout  Connection timeout in seconds (ignored).
     * @param bool   $passive  Whether to use passive mode (ignored).
     */
    public function __construct(
        string $host = 'dummy',
        string $username = 'anonymous',
        string $password = '',
        int $port = 21,
        int $timeout = 90,
        bool $passive = true
    ) {
        // no real connection
    }

    /** {@inheritDoc} */
    public function put(string $path, string $contents): void
    {
        $this->files[$this->normalise($path)] = $contents;
    }

    /** {@inheritDoc} */
    public function get(string $path): string
    {
        $key = $this->normalise($path);

        if (!array_key_exists($key, $this->files)) {
            throw new RuntimeException("Dummy FTP: {$path} not found");
        }

        return $this->files[$key];
    }

    /** {@inheritDoc} */
    public function delete(string $path): void
    {
        $key = $this->normalise($path);

        if (!array_key_exists($key, $this->files)) {
            throw new RuntimeException("Dummy FTP: cannot delete missing {$path}");
        }

        unset($this->files[$key]);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * Collapse duplicate slashes and trim any leading slash.
     */
    private function normalise(string $path): string
    {
        return ltrim(preg_replace('#/+#', '/', $path), '/');
    }
}
