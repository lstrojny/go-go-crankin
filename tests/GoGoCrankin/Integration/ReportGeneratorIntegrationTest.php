<?php
namespace GoGoCrankin\Integration;

use GoGoCrankin\Reporter\CheckstyleResultReporter;
use GoGoCrankin\Reporter\NullProgressReporter;
use GoGoCrankin\Reporter\TextResultReporter;
use GoGoCrankin\Reporter\TextUiProgressReporter;
use GoGoCrankin\Runner\ReportGenerator;
use PHPUnit_Framework_TestCase as TestCase;

class ReportGeneratorIntegrationTest extends TestCase
{
    public function testRunningReportGeneratorWithTextUiProgressReporterAndTextResultReporter()
    {
        $reportGenerator = new ReportGenerator(
            __DIR__ . '/Fixtures/simple.js',
            new TextResultReporter(),
            new TextUiProgressReporter()
        );

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
        $this->assertSame($output, $reportGenerator->run(static function ($output) use (&$progressReport) {
            $progressReport .= $output;
        }));

        $progressReportExpected = <<<'EOS'

Analyzing %s
....

EOS;

        $this->assertSame(sprintf($progressReportExpected, __DIR__ . '/Fixtures/simple.js'), $progressReport);
    }

    public function testRunningReportGeneratorWithNullProgressReporterAndCheckstyleReporter()
    {
        $reportGenerator = new ReportGenerator(
            __DIR__ . '/Fixtures/simple.js',
            new CheckstyleResultReporter(),
            new NullProgressReporter()
        );

        $output = <<<'EOS'
<?xml version="1.0"?>
<checkstyle version="1.0">
  <file name="src/UndeclaredVar1.php">
    <error line="1" column="2" severity="error" message="UseUndeclaredVariable: $this" source="UseUndeclaredVariable"/>
  </file>
  <file name="src/UndeclaredVar2.php">
    <error line="198" column="" severity="error" message="UseUndeclaredVariable: $var" source="UseUndeclaredVariable"/>
  </file>
  <file name="src/UndeclaredConstant1.php">
    <error line="32" column="33" severity="error" message="UseUndeclaredConstant: UUID_TYPE_RANDOM" source="UseUndeclaredConstant"/>
  </file>
  <file name="src/UndeclaredConstant2.php">
    <error line="32" column="33" severity="error" message="UseUndeclaredConstant: UUID_TYPE_RANDOM" source="UseUndeclaredConstant"/>
  </file>
</checkstyle>

EOS;
        $progressReport = '';
        $this->assertSame($output, $reportGenerator->run(function($output) use (&$progressReport) {
            $progressReport .= $output;
        }));

        $progressReportExpected = '';

        $this->assertSame(sprintf($progressReportExpected, __DIR__ . '/Fixtures/simple.js'), $progressReport);
    }
}
