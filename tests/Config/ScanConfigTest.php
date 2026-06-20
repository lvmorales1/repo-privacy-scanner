<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Tests\Config;

use InvalidArgumentException;
use Lvmorales1\PrivacyScanner\Config\ScanConfig;
use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Enums\Severity;
use Lvmorales1\PrivacyScanner\Finding;
use PHPUnit\Framework\TestCase;

final class ScanConfigTest extends TestCase
{
    public function test_defaults_when_no_file_exists(): void
    {
        $config = ScanConfig::fromFile('/nonexistent/privacy-scan.json');

        $this->assertSame(1, $config->failOnScore);
        $this->assertSame(Severity::Low, $config->failOnSeverity);
    }

    public function test_loads_fail_on_score_from_file(): void
    {
        $path = $this->writeConfig(['fail_on_score' => 5]);

        $config = ScanConfig::fromFile($path);

        $this->assertSame(5, $config->failOnScore);
    }

    public function test_loads_fail_on_severity_from_file(): void
    {
        $path = $this->writeConfig(['fail_on_severity' => 'HIGH']);

        $config = ScanConfig::fromFile($path);

        $this->assertSame(Severity::High, $config->failOnSeverity);
    }

    public function test_loads_both_thresholds_from_file(): void
    {
        $path = $this->writeConfig(['fail_on_score' => 6, 'fail_on_severity' => 'MEDIUM']);

        $config = ScanConfig::fromFile($path);

        $this->assertSame(6, $config->failOnScore);
        $this->assertSame(Severity::Medium, $config->failOnSeverity);
    }

    public function test_missing_keys_fall_back_to_defaults(): void
    {
        $path = $this->writeConfig([]);

        $config = ScanConfig::fromFile($path);

        $this->assertSame(1, $config->failOnScore);
        $this->assertSame(Severity::Low, $config->failOnSeverity);
    }

    public function test_invalid_severity_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ScanConfig(1, 'UNKNOWN');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isFailingProvider')]
    public function test_is_failing(int $minScore, string $minSeverity, int $findingScore, bool $expected): void
    {
        $config  = new ScanConfig($minScore, $minSeverity);
        $finding = $this->makeFinding($findingScore);

        $this->assertSame($expected, $config->isFailing($finding));
    }

    public static function isFailingProvider(): array
    {
        return [
            // score meets threshold, severity meets threshold → failing
            'both thresholds met' => [1, 'LOW', 5, true],
            // score below threshold → not failing
            'score below threshold' => [6, 'LOW', 5, false],
            // severity below threshold → not failing
            'severity below threshold' => [1, 'HIGH', 5, false],
            // both below threshold → not failing
            'both below threshold' => [9, 'CRITICAL', 5, false],
            // score exactly at threshold → failing
            'score exactly at threshold' => [5, 'LOW', 5, true],
            // critical finding meets high threshold
            'critical meets high threshold' => [1, 'HIGH', 9, true],
        ];
    }

    private function writeConfig(array $data): string
    {
        $path = tempnam(sys_get_temp_dir(), 'privacy-scan-') . '.json';
        file_put_contents($path, json_encode($data));

        return $path;
    }

    private function makeFinding(int $riskScore): Finding
    {
        return new Finding(
            file: 'test.php',
            line: 1,
            type: FindingType::Secret,
            category: 'test',
            label: 'Test',
            masked: '****',
            riskScore: $riskScore,
            severity: Severity::fromScore($riskScore),
        );
    }
}
