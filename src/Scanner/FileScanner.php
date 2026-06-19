<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Scanner;

use Lvmorales1\PrivacyScanner\Detectors\DetectorInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FileScanner
{
    private const EXCLUDED_DIRS = [
        'vendor',
        'node_modules',
        '.git',
        'storage',
        'bootstrap/cache',
        '.cache',
        'cache',
        'tmp',
        'temp',
        'dist',
        'build',
        '.next',
        '.nuxt',
    ];

    private const EXCLUDED_EXTENSIONS = [
        'lock', 'log',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp',
        'pdf', 'zip', 'gz', 'tar', 'rar', '7z',
        'woff', 'woff2', 'ttf', 'eot',
        'mp4', 'mp3', 'avi', 'mov',
        'exe', 'bin', 'so', 'dll',
    ];

    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /** @param DetectorInterface[] $detectors */
    public function __construct(private readonly array $detectors) {}

    public function scan(string $path): ScanResult
    {
        $result = new ScanResult();

        if (is_file($path)) {
            $this->processFile($path, basename($path), $result);
            return $result;
        }

        $finder = (new Finder())
            ->files()
            ->in($path)
            ->exclude(self::EXCLUDED_DIRS)
            ->ignoreVCSIgnored(true);

        foreach ($finder as $file) {
            if ($this->shouldSkip($file)) {
                continue;
            }

            $this->processFile($file->getRealPath(), $file->getRelativePathname(), $result);
        }

        return $result;
    }

    private function processFile(string $realPath, string $displayPath, ScanResult $result): void
    {
        $content = file_get_contents($realPath);

        if ($content === false) {
            return;
        }

        $result->incrementFilesScanned();

        foreach ($this->detectors as $detector) {
            $result->addFindings($detector->detect($displayPath, $content));
        }
    }

    private function shouldSkip(SplFileInfo $file): bool
    {
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return true;
        }

        return in_array($file->getExtension(), self::EXCLUDED_EXTENSIONS, strict: true);
    }
}
