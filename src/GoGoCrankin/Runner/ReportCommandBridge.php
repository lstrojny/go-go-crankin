<?php
namespace GoGoCrankin\Runner;

use GoGoCrankin\Reporter\TextResultReporter;
use GoGoCrankin\Reporter\TextUiProgressReporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReportCommandBridge extends Command
{
    private $progressReporters = [
        false => 'GoGoCrankin\Reporter\TextUiProgressReporter',
        true  => 'GoGoCrankin\Reporter\NullProgressReporter',
    ];

    private $resultReporters = [
        'text'       => 'GoGoCrankin\Reporter\TextResultReporter',
        'checkstyle' => 'GoGoCrankin\Reporter\CheckstyleResultReporter',
    ];

    protected function configure()
    {
        $this
            ->setDescription('Generate a report from a CodeError.js file')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('reporter', null, InputOption::VALUE_REQUIRED, 'Specify a reporter. Either "text" or "checkstyle"', 'text');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new ReportGenerator(
            $input->getArgument('file'),
            $this->create($input, 'reporter', $this->resultReporters),
            $this->create($input, 'quiet', $this->progressReporters)
        );

        echo $runner->run(static function ($message) use ($output) {
            $output->write($message);
        });
    }

    private function create(InputInterface $input, $option, array $mapping)
    {
        $value = $input->getOption($option);

        if (!isset($mapping[$value])) {
            throw new \InvalidArgumentException(sprintf('Invalid argument "%s" for %s', $value, $option));
        }

        return new $mapping[$value];
    }
}
