<?php
namespace GoGoCrankin\Integration;

use GoGoCrankin\Reporter\TextResultReporter;
use GoGoCrankin\Reporter\TextUiProgressReporter;
use GoGoCrankin\Runner\ReportGenerator;
use PHPUnit_Framework_TestCase as TestCase;

class ReportGeneratorIntegrationTest extends TestCase
{
    /** @var ReportGenerator */
    private $reportGenerator;

    public function setUp()
    {
        $this->reportGenerator = new ReportGenerator(
            __DIR__ . '/Fixtures/simple.js',
            new TextResultReporter(),
            new TextUiProgressReporter()
        );
    }

    public function testRunningReportGenerator()
    {
        $output = <<<'EOS'
HipHop static code analysis report

UseUndeclaredVariable:
----------------------
src/UndeclaredVar1.php, line 1, character 2    $this
src/UndeclaredVar2.php, line 198    $var

UseUndeclaredConstant:
----------------------
src/UndeclaredConstant1.php, line 32, character 33 - 48    UUID_TYPE_RANDOM
src/UndeclaredConstant2.php, line 32, character 33 - 48    UUID_TYPE_RANDOM

EOS;
        $progressReport = '';
        $this->assertSame($output, $this->reportGenerator->run(function($output) use (&$progressReport) {
            $progressReport .= $output;
        }));

        $progressReportExpected = <<<'EOS'

Analyzing %s
....

EOS;

        $this->assertSame(sprintf($progressReportExpected, __DIR__ . '/Fixtures/simple.js'), $progressReport);
    }
}
