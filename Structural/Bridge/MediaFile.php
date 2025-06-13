<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Bridge;

/**
 * Base abstraction in the Bridge pattern for any file-like asset
 * (images, PDFs, videos, etc.) that can live on various storage back-ends.
 *
 * Concrete subclasses (e.g. ImageFile, PdfDocument) decide *how the file is
 * rendered*, while the injected {@see StorageAdapter} decides *where and how
 * the bytes are stored*.
 */
abstract class MediaFile
{
    /**
     * @param StorageAdapter $storageAdapter Concrete implementation that persists the file.
     * @param string         $path            Path or key under which the file is stored.
     */
    public function __construct(
        protected StorageAdapter $storageAdapter,
        protected string $path
    ) {
    }

    /**
     * Produce the HTML (or other representation) that embeds or links to the file.
     *
     * @return string Rendered representation, e.g. an `<img>` tag or a download link.
     */
    abstract public function render(): string;

    /**
     * Delete the underlying file via the configured {@see StorageAdapter}.
     *
     * @throws \RuntimeException If deletion fails in the storage layer.
     */
    public function remove(): void
    {
        $this->storageAdapter->delete($this->path);
    }
}
