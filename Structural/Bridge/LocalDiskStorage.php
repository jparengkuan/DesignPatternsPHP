<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Bridge;

/**
 * Local‑filesystem implementation of {@see StorageAdapter}.
 *
 * Serves as the *Concrete Implementor* in the Bridge pattern, handling low‑level
 * I/O so that higher‑level abstractions (e.g. {@see MediaFile}) remain storage‑agnostic.
 *
 * All files are stored beneath the private uploads directory defined by
 * {@see self::UPLOADS_DIR}. Adjust the constant to move the location or inject
 * configuration through a wrapper in production.
 *
 * @package DesignPatterns\Structural\Bridge
 */
class LocalDiskStorage implements StorageAdapter
{
    /**
     * Absolute directory path where uploaded files are persisted (with trailing slash).
     */
    private const UPLOADS_DIR = '/var/www/html/Structural/Bridge/Uploads/';

    /** {@inheritDoc} */
    public function put(string $path, string $contents): void
    {
        if (file_put_contents(self::UPLOADS_DIR . $path, $contents) === false) {
            throw new \RuntimeException("Unable to write file: {$path}");
        }
    }

    /** {@inheritDoc} */
    public function get(string $path): string
    {
        $result = @file_get_contents(self::UPLOADS_DIR . $path);
        if ($result === false) {
            throw new \RuntimeException("Unable to read file: {$path}");
        }

        return $result;
    }

    /** {@inheritDoc} */
    public function delete(string $path): void
    {
        if (!@unlink(self::UPLOADS_DIR . $path)) {
            throw new \RuntimeException("Unable to delete file: {$path}");
        }
    }
}
