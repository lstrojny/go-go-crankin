<?php
namespace GoGoCrankin\Hphp;

use Symfony\Component\Finder\Shell\Command;

interface CommandFactoryInterface
{
    /**
     * @param string $filesFile
     * @param string $outputDirectory
     * @param array $options
     * @return Command
     */
    public function createAnalyzerCommand($filesFile, $outputDirectory, array $options = []);
}
