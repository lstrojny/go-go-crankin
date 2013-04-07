<?php
namespace GoGoCrankin\Integration;

use GoGoCrankin\Reporter\TextResultReporter;
use GoGoCrankin\Reporter\TextUiProgressReporter;
use GoGoCrankin\Runner\Runner;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $runner;

    public function setUp()
    {
        $this->runner = new Runner(
            __DIR__ . '/Fixtures/simple.js',
            new TextResultReporter(),
            new TextUiProgressReporter()
        );
    }

    public function testRun()
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
        $this->assertSame($output, $this->runner->run(static function() {}));
    }
}
