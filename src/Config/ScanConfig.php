<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Config;

use Lvmorales1\PrivacyScanner\Enums\Severity;
use Lvmorales1\PrivacyScanner\Finding;

final class ScanConfig
{
    public readonly Severity $failOnSeverity;

    public function __construct(
        public readonly int $failOnScore = 1,
        string $failOnSeverity = 'LOW',
    ) {
        $this->failOnSeverity = Severity::fromString($failOnSeverity);
    }

    public static function fromFile(string $path): self
    {
        if (!file_exists($path)) {
            return new self();
        }

        $data = json_decode(file_get_contents($path), true) ?? [];

        return new self(
            failOnScore:    (int) ($data['fail_on_score']    ?? 1),
            failOnSeverity: (string) ($data['fail_on_severity'] ?? 'LOW'),
        );
    }

    public function isFailing(Finding $finding): bool
    {
        return $finding->riskScore >= $this->failOnScore
            && $finding->severity->level() >= $this->failOnSeverity->level();
    }
}
