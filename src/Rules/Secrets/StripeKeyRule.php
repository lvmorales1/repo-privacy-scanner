<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules\Secrets;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Rules\AbstractRule;

final class StripeKeyRule extends AbstractRule
{
    public function getName(): string     { return 'Stripe Live Key'; }
    public function getCategory(): string { return 'stripe_key'; }
    public function getRiskScore(): int   { return 9; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/[sr]k_live_[A-Za-z0-9]{24,}/';
    }
}
