<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class CompositeFilterTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $filter0;

     /**
     * @var MockObject
     */
    private $filter1;

    /**
     * @var CompositeFilter
     */
    private $composite;

    public function setUp()
    {
        $this->filter0 = $this->getMock('GoGoCrankin\Filter\FilterInterface');
        $this->filter1 = $this->getMock('GoGoCrankin\Filter\FilterInterface');
        $this->composite = new CompositeFilter([$this->filter0, $this->filter1]);
    }

    public function testGet()
    {
        $this->assertSame($this->filter0, $this->composite->get(0));
        $this->assertSame($this->filter1, $this->composite->get(1));
    }

    public function testTrueIfOneTrue()
    {
        $position = new Position();

        $this->filter0
            ->expects($this->once())
            ->method('shouldIgnore')
            ->with('Error', 'file.php', $position, 'symbol')
            ->will($this->returnValue(false));
        $this->filter1
            ->expects($this->once())
            ->method('shouldIgnore')
            ->with('Error', 'file.php', $position, 'symbol')
            ->will($this->returnValue(true));

        $this->assertTrue($this->composite->shouldIgnore('Error', 'file.php', $position, 'symbol'));
    }

    public function testFalseIfAllFalse()
    {
        $position = new Position();

        $this->filter0
            ->expects($this->once())
            ->method('shouldIgnore')
            ->with('Error', 'file.php', $position, 'symbol')
            ->will($this->returnValue(false));
        $this->filter1
            ->expects($this->once())
            ->method('shouldIgnore')
            ->with('Error', 'file.php', $position, 'symbol')
            ->will($this->returnValue(false));

        $this->assertFalse($this->composite->shouldIgnore('Error', 'file.php', $position, 'symbol'));
    }
}
