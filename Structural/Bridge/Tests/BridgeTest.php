<?php

declare(strict_types=1);

/* --------------------------------------------------------------------------
 * Stub native file‑system functions **inside the production namespace** so
 * LocalDiskStorage never touches the real disk during unit tests.
 * ----------------------------------------------------------------------- */
namespace DesignPatterns\Structural\Bridge {

    /** @var array<string,string> */
    $GLOBALS['__memory_fs'] = [];

    function file_put_contents(string $filename, $data): int|false
    {
        $GLOBALS['__memory_fs'][$filename] = (string) $data;
        return is_string($data) ? strlen($data) : false;
    }

    function file_get_contents(string $filename): string|false
    {
        return $GLOBALS['__memory_fs'][$filename] ?? false;
    }

    function unlink(string $filename): bool
    {
        if (!array_key_exists($filename, $GLOBALS['__memory_fs'])) {
            return false;
        }
        unset($GLOBALS['__memory_fs'][$filename]);
        return true;
    }
}

/* --------------------------------------------------------------------------
 * Actual PHPUnit tests start in a **separate namespace** to avoid name clashes
 * with the production code and its stubs above.
 * ----------------------------------------------------------------------- */
namespace DesignPatterns\Structural\Bridge\Tests {

    use PHPUnit\Framework\TestCase;
    use RuntimeException;
    use DesignPatterns\Structural\Bridge\{StorageAdapter, LocalDiskStorage, FtpStorage, ImageFile, PdfDocument};

    /**
     * Full integration / unit test suite for the Bridge example.
     */
    class BridgeTest extends TestCase
    {
        /* ------------------------------------------------------------------
         * Data‑provider handing us every storage back‑end to avoid duplication.
         * ------------------------------------------------------------------ */
        public static function storageProvider(): array
        {
            return [
                'LocalDiskStorage' => [new LocalDiskStorage()],
                'FtpStorage'       => [new FtpStorage()],
            ];
        }

        /* ------------------------------------------------------------------
         * Core contract tests (put / get / delete) for **every** adapter.
         * ------------------------------------------------------------------ */

        /**
         * @dataProvider storageProvider
         */
        public function testPutAndGet(StorageAdapter $storage): void
        {
            $storage->put('foo/bar.txt', 'hello');
            $this->assertSame('hello', $storage->get('foo/bar.txt'));
        }

        /**
         * @dataProvider storageProvider
         */
        public function testDelete(StorageAdapter $storage): void
        {
            $storage->put('baz/qux.txt', 'gone');
            $storage->delete('baz/qux.txt');

            $this->expectException(RuntimeException::class);
            $storage->get('baz/qux.txt');
        }

        /**
         * @dataProvider storageProvider
         */
        public function testGetNonExistingThrows(StorageAdapter $storage): void
        {
            $this->expectException(RuntimeException::class);
            $storage->get('missing.txt');
        }

        /**
         * @dataProvider storageProvider
         */
        public function testDeleteNonExistingThrows(StorageAdapter $storage): void
        {
            $this->expectException(RuntimeException::class);
            $storage->delete('missing.txt');
        }

        /* ------------------------------------------------------------------
         * Media‑specific behaviour — make sure render() outputs the right HTML
         * and remove() truly delegates deletion to the chosen adapter.
         * ------------------------------------------------------------------ */
        public function testImageRender(): void
        {
            $storage = new FtpStorage();
            $storage->put('images/cat.png', '🐱');
            $img = new ImageFile($storage, 'images/cat.png');

            $this->assertSame(
                '<img src="/media/images/cat.png" alt="uploaded image">',
                $img->render()
            );
        }

        public function testPdfRender(): void
        {
            $storage = new FtpStorage();
            $storage->put('docs/report.pdf', '%PDF‑1.7');
            $pdf = new PdfDocument($storage, 'docs/report.pdf');

            $this->assertSame(
                '<a href="/media/docs/report.pdf" target="_blank">Download PDF</a>',
                $pdf->render()
            );
        }

        public function testMediaRemoveDelegatesToStorage(): void
        {
            $storage = new FtpStorage();
            $storage->put('tmp/todelete.txt', 'bye');
            $file = new PdfDocument($storage, 'tmp/todelete.txt');

            $file->remove();

            $this->expectException(RuntimeException::class);
            $storage->get('tmp/todelete.txt');
        }

        /* ------------------------------------------------------------------
         * Adapter‑specific quirks — e.g. path normalisation in FtpStorage.
         * ------------------------------------------------------------------ */
        public function testFtpPathNormalisation(): void
        {
            $ftp = new FtpStorage();
            $ftp->put('///odd//path//file.txt', 'data');

            // Should be retrievable via a clean path
            $this->assertSame('data', $ftp->get('odd/path/file.txt'));
        }
    }
}
