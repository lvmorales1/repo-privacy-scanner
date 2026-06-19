<?php

declare(strict_types=1);

namespace PrivacyScanner\Detectors;

use PrivacyScanner\Rules\PersonalData\BrazilianPhoneRule;
use PrivacyScanner\Rules\PersonalData\CnpjRule;
use PrivacyScanner\Rules\PersonalData\CpfRule;
use PrivacyScanner\Rules\PersonalData\EmailRule;
use PrivacyScanner\Rules\RuleInterface;

final class PersonalDataDetector implements DetectorInterface
{
    /** @var RuleInterface[] */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new CpfRule(),
            new CnpjRule(),
            new BrazilianPhoneRule(),
            new EmailRule(),
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
