<?php

declare(strict_types=1);

namespace PrivacyScanner\Detectors;

use PrivacyScanner\Finding;

interface DetectorInterface
{
    /** @return Finding[] */
    public function detect(string $filePath, string $content): array;
}
