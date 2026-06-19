<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules\Secrets;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Rules\AbstractRule;

final class AwsKeyRule extends AbstractRule
{
    public function getName(): string     { return 'AWS Access Key'; }
    public function getCategory(): string { return 'aws_key'; }
    public function getRiskScore(): int   { return 9; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/AKIA[0-9A-Z]{16}/';
    }
}
