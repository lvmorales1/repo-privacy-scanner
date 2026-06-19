<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules\PersonalData;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Rules\AbstractRule;

final class CpfRule extends AbstractRule
{
    public function getName(): string     { return 'CPF (Brazilian Tax ID)'; }
    public function getCategory(): string { return 'cpf'; }
    public function getRiskScore(): int   { return 6; }
    protected function getType(): FindingType { return FindingType::PersonalData; }

    protected function getPattern(): string
    {
        // Requires formatted CPF (dots and dash) to avoid false positives on random 11-digit numbers
        return '/\b\d{3}\.\d{3}\.\d{3}-\d{2}\b/';
    }
}
