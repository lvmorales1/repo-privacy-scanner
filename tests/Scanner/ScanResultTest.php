<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Tests\Scanner;

use Lvmorales1\PrivacyScanner\Config\ScanConfig;
use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Enums\Severity;
use Lvmorales1\PrivacyScanner\Finding;
use Lvmorales1\PrivacyScanner\Scanner\ScanResult;
use PHPUnit\Framework\TestCase;

final class ScanResultTest extends TestCase
{
    public function test_empty_result_has_zero_score(): void
    {
        $result = new ScanResult();

        $this->assertSame(0, $result->getRiskScore());
        $this->assertTrue($result->isEmpty());
    }

    public function test_risk_score_sums_all_findings(): void
    {
        $result = new ScanResult();
        $result->addFindings([
            $this->makeFinding(riskScore: 9),
            $this->makeFinding(riskScore: 6),
        ]);

        $this->assertSame(15, $result->getRiskScore());
    }

    public function test_risk_score_is_capped_at_100(): void
    {
        $result = new ScanResult();
        $findings = array_fill(0, 20, $this->makeFinding(riskScore: 10));
        $result->addFindings($findings);

        $this->assertSame(100, $result->getRiskScore());
    }

    public function test_files_scanned_counter(): void
    {
        $result = new ScanResult();
        $result->incrementFilesScanned();
        $result->incrementFilesScanned();
        $result->incrementFilesScanned();

        $this->assertSame(3, $result->getFilesScanned());
    }

    public function test_findings_are_returned_in_order(): void
    {
        $result = new ScanResult();

        $first  = $this->makeFinding(riskScore: 9);
        $second = $this->makeFinding(riskScore: 6);

        $result->addFindings([$first, $second]);

        $this->assertSame([$first, $second], $result->getFindings());
    }

    public function test_has_failing_findings_with_default_config(): void
    {
        $result = new ScanResult();
        $result->addFindings([$this->makeFinding(riskScore: 5)]);

        $this->assertTrue($result->hasFailingFindings(new ScanConfig()));
    }

    public function test_no_failing_findings_when_empty(): void
    {
        $result = new ScanResult();

        $this->assertFalse($result->hasFailingFindings(new ScanConfig()));
    }

    public function test_has_failing_findings_respects_min_score(): void
    {
        $result = new ScanResult();
        $result->addFindings([$this->makeFinding(riskScore: 4)]);

        $this->assertFalse($result->hasFailingFindings(new ScanConfig(failOnScore: 5)));
        $this->assertTrue($result->hasFailingFindings(new ScanConfig(failOnScore: 4)));
    }

    public function test_has_failing_findings_respects_min_severity(): void
    {
        $result = new ScanResult();
        $result->addFindings([$this->makeFinding(riskScore: 4)]); // MEDIUM

        $this->assertFalse($result->hasFailingFindings(new ScanConfig(failOnSeverity: 'HIGH')));
        $this->assertTrue($result->hasFailingFindings(new ScanConfig(failOnSeverity: 'MEDIUM')));
    }

    public function test_has_failing_findings_requires_both_thresholds(): void
    {
        $result = new ScanResult();
        $result->addFindings([$this->makeFinding(riskScore: 4)]); // score 4, MEDIUM

        // score too high but severity ok → not failing
        $this->assertFalse($result->hasFailingFindings(new ScanConfig(failOnScore: 9, failOnSeverity: 'LOW')));
        // score ok but severity too high → not failing
        $this->assertFalse($result->hasFailingFindings(new ScanConfig(failOnScore: 1, failOnSeverity: 'HIGH')));
        // both ok → failing
        $this->assertTrue($result->hasFailingFindings(new ScanConfig(failOnScore: 4, failOnSeverity: 'MEDIUM')));
    }

    public function test_has_failing_findings_when_only_some_findings_qualify(): void
    {
        $result = new ScanResult();
        $result->addFindings([
            $this->makeFinding(riskScore: 2), // LOW — below HIGH threshold
            $this->makeFinding(riskScore: 9), // CRITICAL — above HIGH threshold
        ]);

        $this->assertTrue($result->hasFailingFindings(new ScanConfig(failOnSeverity: 'HIGH')));
    }

    private function makeFinding(int $riskScore): Finding
    {
        return new Finding(
            file: 'test.php',
            line: 1,
            type: FindingType::Secret,
            category: 'test',
            label: 'Test Finding',
            masked: '****',
            riskScore: $riskScore,
            severity: Severity::fromScore($riskScore),
        );
    }
}
