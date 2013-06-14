<?php
namespace GoGoCrankin\Hphp;

use PHPUnit_Framework_TestCase as TestCase;

class CommandFactoryTest extends TestCase
{
    private $shell;

    private $commandFactory;

    public function setUp()
    {
        $this->shell = $this->getMockBuilder('Symfony\Component\Finder\Shell\Shell')
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandFactory = new CommandFactory($this->shell);
    }

    public function testBuildLegacyCommand()
    {
        $this->hphpCommandExists(true);
        $command = $this->commandFactory->createAnalyzerCommand('input.txt', 'output-dir');

        $this->assertInstanceOf('Symfony\Component\Finder\Shell\Command', $command);
        $this->assertSame(
            "hphp -t analyze --input-list 'input.txt' --output-dir 'output-dir' --log 4",
            (string) $command
        );
    }

    public function testBuildCurrentCommand()
    {
        $this->hphpCommandExists(false);
        $command = $this->commandFactory->createAnalyzerCommand('input.txt', 'output-dir');

        $this->assertInstanceOf('Symfony\Component\Finder\Shell\Command', $command);
        $this->assertSame(
            "hhvm --hphp -t analyze --input-list 'input.txt' --output-dir 'output-dir' --log 4",
            (string) $command
        );
    }

    public function testSpecifyingHipHopOptionsForLegacy()
    {
        $this->hphpCommandExists(true);
        $command = $this->commandFactory->createAnalyzerCommand('input.txt', 'output-dir', ['-v EnableHipHopSyntax=true']);

        $this->assertInstanceOf('Symfony\Component\Finder\Shell\Command', $command);
        $this->assertSame(
            "hphp -t analyze '-v EnableHipHopSyntax=true' --input-list 'input.txt' --output-dir 'output-dir' --log 4",
            (string) $command
        );
    }

    public function testSpecifyingHipHopOptions()
    {
        $this->hphpCommandExists(false);
        $command = $this->commandFactory->createAnalyzerCommand('input.txt', 'output-dir', ['-v EnableHipHopSyntax=true']);

        $this->assertInstanceOf('Symfony\Component\Finder\Shell\Command', $command);
        $this->assertSame(
            "hhvm --hphp -t analyze '-v EnableHipHopSyntax=true' --input-list 'input.txt' --output-dir 'output-dir' --log 4",
            (string) $command
        );
    }

    private function hphpCommandExists($flag)
    {
        $this->shell
            ->expects($this->once())
            ->method('testCommand')
            ->with('hphp')
            ->will($this->returnValue($flag));
    }
}
