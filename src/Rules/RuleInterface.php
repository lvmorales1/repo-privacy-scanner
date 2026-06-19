<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules;

use PrivacyScanner\Finding;

interface RuleInterface
{
    public function getName(): string;

    public function getCategory(): string;

    public function getRiskScore(): int;

    public function check(string $line, int $lineNumber, string $filePath): ?Finding;
}
