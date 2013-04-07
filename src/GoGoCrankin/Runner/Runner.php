<?php
namespace GoGoCrankin\Runner;

use GoGoCrankin\Reporter\ProgressReporterInterface;
use GoGoCrankin\Reporter\ResultReporterInterface;
use GoGoCrankin\Value\Position;

class Runner
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var ResultReporterInterface
     */
    private $resultReporter;

    /**
     * @var ProgressReporterInterface
     */
    private $progressReporter;

    public function __construct(
        $file,
        ResultReporterInterface $resultReporter,
        ProgressReporterInterface $progressReporter
    )
    {
        $this->file = $file;
        $this->resultReporter = $resultReporter;
        $this->progressReporter = $progressReporter;
    }

    public function run(callable $output)
    {
        if (!file_exists($this->file)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found', $this->file));
        }

        if (!is_file($this->file)) {
            throw new \InvalidArgumentException(sprintf('Invalid file "%s" given', $this->file));
        }

        $report = json_decode(file_get_contents($this->file), true);
        if ($report === null) {
            throw new \InvalidArgumentException(sprintf('Could not read "%s" as JSON', $this->file));
        }

        if (!isset($report[1])) {
            throw new \InvalidArgumentException('Invalid report format');
        }

        $output($this->progressReporter->beginReport());
        $this->resultReporter->beginReport();
        foreach ($report[1] as $error => $violations) {
            $this->resultReporter->beginSection($error);

            foreach ($violations as $violation) {
                if (!isset($violation['c1']) || !isset($violation['d'])) {
                    throw new \InvalidArgumentException('Invalid report format');
                }

                $position = Position::createFromArray($violation['c1']);
                $this->resultReporter->reportViolation($error, $violation['c1'][0], $position, $violation['d']);

                $output($this->progressReporter->progress());
            }

            $this->resultReporter->endSection($error);
        }
        $this->resultReporter->endReport();
        $output($this->progressReporter->endReport());

        return $this->resultReporter->getString();
    }
}
