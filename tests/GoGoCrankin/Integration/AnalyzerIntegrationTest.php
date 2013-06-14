<?php
namespace GoGoCrankin\Integration;

use GoGoCrankin\Configuration\XmlConfigurationReader;
use GoGoCrankin\Finder\FinderFactory;
use GoGoCrankin\Hphp\CommandFactory;
use GoGoCrankin\Reporter\TextUiProgressReporter;
use GoGoCrankin\Runner\Analyzer;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Finder\Shell\Shell;
use Symfony\Component\Process\Process;

class AnalyzerIntegrationTest extends TestCase
{
    /** @var Analyzer */
    private $analyzer;

    public function setUp()
    {
        $this->analyzer = new Analyzer(
            __DIR__ . '/Fixtures/config.xml',
            new XmlConfigurationReader(),
            new FinderFactory(),
            new CommandFactory(new Shell()),
            new TextUiProgressReporter()
        );
    }

    public function testRunningAnalyzer()
    {
        $expectedProgressOutput = <<<'EOS'

Building file list
..

Executing static analyzer
..
EOS;

        $actualProgressOutput = '';
        $this->analyzer->run(static function ($output) use (&$actualProgressOutput) {
            $actualProgressOutput .= $output;
        });

        $this->assertSame($expectedProgressOutput, $actualProgressOutput);
    }
}
