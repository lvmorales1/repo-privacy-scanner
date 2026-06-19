<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules\PersonalData;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Rules\AbstractRule;

final class BrazilianPhoneRule extends AbstractRule
{
    public function getName(): string     { return 'Brazilian Phone Number'; }
    public function getCategory(): string { return 'phone_br'; }
    public function getRiskScore(): int   { return 4; }
    protected function getType(): FindingType { return FindingType::PersonalData; }

    protected function getPattern(): string
    {
        return '/(?:\+55\s?)?(?:\(\d{2}\)|\d{2})\s?\d{4,5}[-\s]\d{4}/';
    }
}
