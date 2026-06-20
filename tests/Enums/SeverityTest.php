<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Tests\Enums;

use InvalidArgumentException;
use Lvmorales1\PrivacyScanner\Enums\Severity;
use PHPUnit\Framework\TestCase;

final class SeverityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('severityStringProvider')]
    public function test_from_string_returns_correct_case(string $input, Severity $expected): void
    {
        $this->assertSame($expected, Severity::fromString($input));
    }

    public static function severityStringProvider(): array
    {
        return [
            ['LOW',      Severity::Low],
            ['MEDIUM',   Severity::Medium],
            ['HIGH',     Severity::High],
            ['CRITICAL', Severity::Critical],
            ['low',      Severity::Low],
            ['Medium',   Severity::Medium],
        ];
    }

    public function test_from_string_throws_on_unknown_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Severity::fromString('UNKNOWN');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('levelOrderProvider')]
    public function test_level_is_strictly_ordered(Severity $lower, Severity $higher): void
    {
        $this->assertLessThan($higher->level(), $lower->level());
    }

    public static function levelOrderProvider(): array
    {
        return [
            [Severity::Low,    Severity::Medium],
            [Severity::Medium, Severity::High],
            [Severity::High,   Severity::Critical],
            [Severity::Low,    Severity::Critical],
        ];
    }

    public function test_same_severity_has_equal_level(): void
    {
        $this->assertSame(Severity::Medium->level(), Severity::Medium->level());
    }
}
