<?php

declare(strict_types=1);

namespace PrivacyScanner\Rules\Secrets;

use PrivacyScanner\Enums\FindingType;
use PrivacyScanner\Rules\AbstractRule;

final class SshPrivateKeyRule extends AbstractRule
{
    public function getName(): string     { return 'SSH Private Key'; }
    public function getCategory(): string { return 'ssh_private_key'; }
    public function getRiskScore(): int   { return 10; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/-----BEGIN (RSA|EC|DSA|OPENSSH) PRIVATE KEY-----/';
    }
}
