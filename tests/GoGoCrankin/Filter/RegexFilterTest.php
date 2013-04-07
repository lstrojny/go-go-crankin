<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;
use PHPUnit_Framework_TestCase as TestCase;

class RegexFilterTest extends TestCase
{
    /**
     * @var RegexFilter
     */
    private $filter;

    /**
     * @var Position
     */
    private $position;

    public function setUp()
    {
        $this->position = new Position(12, 12, 0, 0);
        $this->filter = new RegexFilter([
            'error'       => '/Test/',
            'file'        => '/\.php$/',
            'startLine'   => '/^12$/',
            'endLine'     => '/^12$/',
            'startColumn' => '/^0$/',
            'endColumn'   => '/^0$/',
            'symbol'      => '/^\$/',
        ]);
    }

    public function testFilter()
    {
        $this->assertTrue($this->filter->filter('BeforeTestAfter', 'test.php', $this->position, '$var'));
        $this->assertTrue($this->filter->filter('Test', 'test.php', $this->position, '$var'));

        $this->assertFalse($this->filter->filter('Test', 'test.php', new Position(), '$var'));
        $this->assertFalse($this->filter->filter('', '', new Position(), ''));
        $this->assertFalse($this->filter->filter('Test', 'test.php', new Position(1), '$var'));
        $this->assertFalse($this->filter->filter('Test', 'test.php', new Position(1, 2), '$var'));
        $this->assertFalse($this->filter->filter('Test', 'test.php', new Position(1, 2, 3), '$var'));
    }

    public function testNullValuesInPositionAlwaysMatchPositive()
    {
        $this->assertFalse($this->filter->filter('Test', 'test.php', new Position(null, null), '$var'));
        $this->assertFalse($this->filter->filter('Test', 'test.php', new Position(12, null), '$var'));
    }

    public function testPartialRegex()
    {
        $filter = new RegexFilter(['error' => '/Test/']);
        $this->assertTrue($filter->filter('Test', 'test.php', $this->position, '$var'));
    }

    public function testInvalidArrayKeys()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid array key "foo" given. Expected at least one of "error", "file", "startLine", "endLine", "startColumn", "endColumn", "symbol"'
        );
        new RegexFilter(['foo' => '']);
    }
}
