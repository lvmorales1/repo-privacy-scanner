<?php

declare(strict_types=1);

namespace PrivacyScanner\Enums;

enum Severity: string
{
    case Low = 'LOW';
    case Medium = 'MEDIUM';
    case High = 'HIGH';
    case Critical = 'CRITICAL';

    public static function fromScore(int $score): self
    {
        return match (true) {
            $score <= 3 => self::Low,
            $score <= 5 => self::Medium,
            $score <= 7 => self::High,
            default     => self::Critical,
        };
    }
}
