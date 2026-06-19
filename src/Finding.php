<?php

declare(strict_types=1);

namespace PrivacyScanner;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Enums\Severity;

final class Finding
{
    public function __construct(
        public readonly string $file,
        public readonly int $line,
        public readonly FindingType $type,
        public readonly string $category,
        public readonly string $label,
        public readonly string $masked,
        public readonly int $riskScore,
        public readonly Severity $severity,
    ) {}
}
