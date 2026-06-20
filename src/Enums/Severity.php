<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Enums;

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

    public static function fromString(string $value): self
    {
        return match (strtoupper($value)) {
            'LOW'      => self::Low,
            'MEDIUM'   => self::Medium,
            'HIGH'     => self::High,
            'CRITICAL' => self::Critical,
            default    => throw new \InvalidArgumentException("Unknown severity: {$value}"),
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::Low      => 1,
            self::Medium   => 2,
            self::High     => 3,
            self::Critical => 4,
        };
    }
}
