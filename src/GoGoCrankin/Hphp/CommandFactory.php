<?php
namespace GoGoCrankin\Hphp;

use Symfony\Component\Finder\Shell\Command;
use Symfony\Component\Finder\Shell\Shell;
use Functional as F;

final class CommandFactory implements CommandFactoryInterface
{
    private $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function createAnalyzerCommand($filesFile, $outputDirectory, array $options = [])
    {
        $command = new Command();

        $this->shell->testCommand('hphp')
            ? $command->cmd('hphp')
            : $command->cmd('hhvm')->add('--hphp');

        $command->add('-t analyze');

        F\map($options, [$command, 'arg']);

        return $command->add('--input-list')->arg($filesFile)
                ->add('--output-dir')->arg($outputDirectory)
                ->add('--log 4');
    }
}
