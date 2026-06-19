<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Enums\Severity;
use PrivacyScanner\Finding;

abstract class AbstractRule implements RuleInterface
{
    abstract protected function getPattern(): string;

    abstract protected function getType(): FindingType;

    public function check(string $line, int $lineNumber, string $filePath): ?Finding
    {
        if (!preg_match($this->getPattern(), $line, $matches)) {
            return null;
        }

        $sensitive = $matches['value'] ?? $matches[0];

        return new Finding(
            file: $filePath,
            line: $lineNumber,
            type: $this->getType(),
            category: $this->getCategory(),
            label: $this->getName(),
            masked: $this->mask($sensitive),
            riskScore: $this->getRiskScore(),
            severity: Severity::fromScore($this->getRiskScore()),
        );
    }

    private function mask(string $value): string
    {
        $len = strlen($value);

        if ($len <= 8) {
            return str_repeat('*', $len);
        }

        return substr($value, 0, 4) . str_repeat('*', $len - 8) . substr($value, -4);
    }
}
