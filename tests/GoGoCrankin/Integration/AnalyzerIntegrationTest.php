<?php
namespace GoGoCrankin\Integration;

use GoGoCrankin\Configuration\XmlConfigurationReader;
use GoGoCrankin\Finder\FinderFactory;
use GoGoCrankin\Hphp\CommandFactory;
use GoGoCrankin\Reporter\NullProgressReporter;
use GoGoCrankin\Reporter\TextUiProgressReporter;
use GoGoCrankin\Runner\Analyzer;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Finder\Shell\Shell;
use Symfony\Component\Process\Process;

class AnalyzerIntegrationTest extends TestCase
{
    public function testAnalyzerRunWithTextUiProgressReporter()
    {
        $analyzer = new Analyzer(
            __DIR__ . '/Fixtures/config.xml',
            new XmlConfigurationReader(),
            new FinderFactory(),
            new CommandFactory(new Shell()),
            new TextUiProgressReporter()
        );

        $expectedProgressOutput = <<<'EOS'

Building file list
..

Executing static analyzer
..
EOS;

        $actualProgressOutput = '';
        $analyzer->run(static function ($output) use (&$actualProgressOutput) {
            $actualProgressOutput .= $output;
        });

        $this->assertSame($expectedProgressOutput, $actualProgressOutput);
    }

    public function testAnalyzerRunWithNullProgressReporter()
    {
        $analyzer = new Analyzer(
            __DIR__ . '/Fixtures/config.xml',
            new XmlConfigurationReader(),
            new FinderFactory(),
            new CommandFactory(new Shell()),
            new NullProgressReporter()
        );

        $actualProgressOutput = '';
        $analyzer->run(static function ($output) use (&$actualProgressOutput) {
            $actualProgressOutput .= $output;
        });

        $this->assertSame('', $actualProgressOutput);
    }
}
