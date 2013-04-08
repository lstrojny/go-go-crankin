<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;
use PHPUnit_Framework_TestCase as TestCase;

class StringFilterTest extends TestCase
{
    /**
     * @var Position
     */
    private $position;

    public function setUp()
    {
        $this->position = new Position(12, 13, 1, 2);
    }

    public function getConstructorArguments()
    {
        $arguments = [
            ['error', 'Test'],
            ['file', 'file.php'],
        ];

        return array_merge($arguments, $this->getLineConstructorArguments());
    }

    public function getLineConstructorArguments()
    {
        return [
            ['startLine', '12'],
            ['endLine', '13'],
            ['startColumn', '1'],
            ['endColumn', '2'],
        ];
    }

    /** @dataProvider getConstructorArguments */
    public function testFilter($key, $pattern)
    {
        $filter = new StringFilter($key, $pattern);
        $this->assertTrue($filter->shouldIgnore('Test', 'file.php', $this->position, '$var'));
        $this->assertFalse($filter->shouldIgnore('', '', new Position(), ''));
    }

    /** @dataProvider getLineConstructorArguments */
    public function testNullValuesInPositionAlwaysMatchPositive($key, $pattern)
    {
        $filter = new StringFilter($key, $pattern);
        $this->assertFalse($filter->shouldIgnore('Test', 'file.php', new Position(), '$var'));
    }
}
