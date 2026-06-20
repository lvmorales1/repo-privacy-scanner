<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Commands;

use Lvmorales1\PrivacyScanner\Config\ScanConfig;
use Lvmorales1\PrivacyScanner\Detectors\PersonalDataDetector;
use Lvmorales1\PrivacyScanner\Detectors\SecretDetector;
use Lvmorales1\PrivacyScanner\Reporters\ConsoleReporter;
use Lvmorales1\PrivacyScanner\Reporters\JsonReporter;
use Lvmorales1\PrivacyScanner\Reporters\ReporterInterface;
use Lvmorales1\PrivacyScanner\Scanner\FileScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ScanCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('scan')
            ->setDescription('Scan a path for secrets and personal data')
            ->addArgument('path', InputArgument::REQUIRED, 'File or directory to scan')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format: console or json', 'console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $realPath = realpath($path);

        if ($realPath === false) {
            $output->writeln(sprintf('<error>Path not found: %s</error>', $path));
            return Command::FAILURE;
        }

        $scanner = new FileScanner([
            new SecretDetector(),
            new PersonalDataDetector(),
        ]);

        $config = ScanConfig::fromFile(getcwd() . '/privacy-scan.json');

        $result = $scanner->scan($realPath);

        $this->resolveReporter($input->getOption('format'))->report($result, $output);

        return $result->hasFailingFindings($config) ? Command::FAILURE : Command::SUCCESS;
    }

    private function resolveReporter(string $format): ReporterInterface
    {
        return match ($format) {
            'json'  => new JsonReporter(),
            default => new ConsoleReporter(),
        };
    }
}
