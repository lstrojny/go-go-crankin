<?php
namespace GoGoCrankin\Runner;

use GoGoCrankin\Configuration\Configuration;
use GoGoCrankin\Configuration\ConfigurationReaderInterface;
use GoGoCrankin\Finder\FinderFactoryInterface;
use GoGoCrankin\Hphp\CommandFactory;
use GoGoCrankin\Reporter\ProgressReporterInterface;
use Symfony\Component\Finder\Finder;
use Functional as F;
use Symfony\Component\Process\Process;

class Analyzer
{
    /** @var string */
    private $configurationFile;

    /** @var ConfigurationReaderInterface */
    private $configurationReader;

    /** @var FinderFactoryInterface */
    private $finderFactory;

    /** @var CommandFactory */
    private $commandFactory;

    /** @var ProgressReporterInterface */
    private $progressReporter;

    public function __construct(
        $configurationFile,
        ConfigurationReaderInterface $configurationReader,
        FinderFactoryInterface $finderFactory,
        CommandFactory $commandFactory,
        ProgressReporterInterface $progressReporter
    )
    {
        $this->configurationFile = $configurationFile;
        $this->configurationReader = $configurationReader;
        $this->finderFactory = $finderFactory;
        $this->commandFactory = $commandFactory;
        $this->progressReporter = $progressReporter;
    }

    public function run(callable $output)
    {
        $configuration = $this->configurationReader->read($this->configurationFile);

        $finder = $this->finderFactory->createFinder($configuration);

        $output($this->progressReporter->beginReport('Building file list'));
        $tempFile = tmpfile();
        foreach ($finder as $file) {
            fwrite($tempFile, $file->getPathName() . "\n");
            $output($this->progressReporter->progress());
        }
        $output($this->progressReporter->endReport());

        $tempDir = tempnam(sys_get_temp_dir(), microtime(true));
        unlink($tempDir);
        mkdir($tempDir);

        $command = $this->commandFactory->createAnalyzerCommand(
            stream_get_meta_data($tempFile)['uri'],
            $tempDir
        );

        $process = new Process($command);
        $output($this->progressReporter->beginReport('Executing static analyzer'));
        $process->run(function ($type, $message) use ($output) {
            if ($type !== 'out') {
                return;
            }

            if (!preg_match('@^parsing /@', $message)) {
                return;
            }

            $output($this->progressReporter->progress());
        });
        $output($this->progressReporter->endReport());

        fclose($tempFile);
    }
}
