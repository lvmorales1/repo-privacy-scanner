<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules\PersonalData;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Rules\AbstractRule;

final class CnpjRule extends AbstractRule
{
    public function getName(): string     { return 'CNPJ (Brazilian Company ID)'; }
    public function getCategory(): string { return 'cnpj'; }
    public function getRiskScore(): int   { return 5; }
    protected function getType(): FindingType { return FindingType::PersonalData; }

    protected function getPattern(): string
    {
        return '/\b\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}\b/';
    }
}
