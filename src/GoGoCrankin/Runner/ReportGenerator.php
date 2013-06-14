<?php
namespace GoGoCrankin\Runner;

use GoGoCrankin\Reporter\ProgressReporterInterface;
use GoGoCrankin\Reporter\ResultReporterInterface;
use GoGoCrankin\Value\Position;

class ReportGenerator
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
        $report = json_decode(file_get_contents($this->file), true);

        $output($this->progressReporter->beginReport('Analyzing', $this->file));
        $this->resultReporter->beginReport();
        foreach ($report[1] as $error => $violations) {
            $this->resultReporter->beginSection($error);

            foreach ($violations as $violation) {

                $position = Position::createFromArray($violation['c1']);
                $this->resultReporter->reportViolation($error, $violation['c1'][0], $position, $violation['d']);

                $output($this->progressReporter->progress());
            }

            $this->resultReporter->endSection($error);
        }
        $this->resultReporter->endReport();
        $output($this->progressReporter->endReport('Analyzing', $this->file));

        return $this->resultReporter->getString();
    }
}
