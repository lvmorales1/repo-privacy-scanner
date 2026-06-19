<?php

declare(strict_types=1);

namespace PrivacyScanner\Detectors;

use PrivacyScanner\Rules\RuleInterface;
use PrivacyScanner\Rules\Secrets\AwsKeyRule;
use PrivacyScanner\Rules\Secrets\DatabaseUrlRule;
use PrivacyScanner\Rules\Secrets\GithubTokenRule;
use PrivacyScanner\Rules\Secrets\JwtRule;
use PrivacyScanner\Rules\Secrets\OpenAiKeyRule;
use PrivacyScanner\Rules\Secrets\SshPrivateKeyRule;
use PrivacyScanner\Rules\Secrets\StripeKeyRule;

final class SecretDetector implements DetectorInterface
{
    /** @var RuleInterface[] */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new SshPrivateKeyRule(),
            new AwsKeyRule(),
            new StripeKeyRule(),
            new GithubTokenRule(),
            new OpenAiKeyRule(),
            new DatabaseUrlRule(),
            new JwtRule(),
        ];
    }

    public function detect(string $filePath, string $content): array
    {
        $findings = [];
        $lines = explode("\n", $content);

        foreach ($lines as $index => $line) {
            foreach ($this->rules as $rule) {
                $finding = $rule->check($line, $index + 1, $filePath);

                if ($finding !== null) {
                    $findings[] = $finding;
                }
            }
        }

        return $findings;
    }
}
