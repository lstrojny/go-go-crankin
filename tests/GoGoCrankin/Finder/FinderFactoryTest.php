<?php
namespace GoGoCrankin\Finder;

use GoGoCrankin\Configuration\Configuration;
use PHPUnit_Framework_TestCase as TestCase;
use Functional as F;

class FinderFactoryTest extends TestCase
{
    /** @var Configuration */
    private $configuration;

    /** @var FinderFactory */
    private $finderFactory;

    public function setUp()
    {
        $this->configuration = $this->getMockBuilder('GoGoCrankin\Configuration\Configuration')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();

        $includes = [
            'directory' => [__DIR__ . '/Fixtures/inc-dir1', __DIR__ . '/Fixtures/inc-dir2'],
            'file' => ['included.txt'],
            'regex' => ['/\.php$/'],
        ];
        $excludes = [
            'directory' => ['ex-dir1', 'ex-dir2'],
            'file'      => ['not-included.txt'],
            'regex'     => ['/regex-excluded.*/'],
        ];
        $this->configuration
            ->expects($this->any())
            ->method('getIncludes')
            ->will($this->returnValue($includes));
        $this->configuration
            ->expects($this->any())
            ->method('getExcludes')
            ->will($this->returnValue($excludes));

        $this->finderFactory = new FinderFactory();
    }

    public function testCreatingFinder()
    {
        $finder = $this->finderFactory->createFinder($this->configuration);

        $fileList = F\invoke($finder, 'getPathName');
        $this->assertCount(5, $fileList);

        $prefix = __DIR__ . '/Fixtures/';
        $this->assertContains($prefix . 'inc-dir1/dir1/included4.php', $fileList);
        $this->assertContains($prefix . 'inc-dir1/included1.php', $fileList);
        $this->assertContains($prefix . 'inc-dir2/dir2/included3.php', $fileList);
        $this->assertContains($prefix . 'inc-dir2/included2.php', $fileList);
    }
}
