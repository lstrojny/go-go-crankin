<?php
namespace GoGoCrankin\Configuration;

use GoGoCrankin\Filter\AbstractFilter;
use PHPUnit_Framework_TestCase as TestCase;

class XmlConfigurationReaderTest extends TestCase
{
    /** @var XmlConfigurationReader */
    public $reader;

    public function setUp()
    {
        $this->reader = new XmlConfigurationReader();
    }

    public function testValidationFilesRequired()
    {
        $this->setExpectedException('RuntimeException', 'Expecting an element , got nothing in ');
        $this->reader->read(__DIR__ . '/Fixtures/invalid_01.xml');
    }

    public function testValidationFiles_IncludeRequired()
    {
        $this->setExpectedException('RuntimeException', 'Expecting an element , got nothing in ');
        $this->reader->read(__DIR__ . '/Fixtures/invalid_02.xml');
    }

    public function testValidationFiles_Include_Directory_TextRequired()
    {
        $this->setExpectedException('RuntimeException', 'Element includes failed to validate content in ');
        $this->reader->read(__DIR__ . '/Fixtures/invalid_04.xml');
    }

    public function testValidationMinimal()
    {
        $configuration = $this->reader->read(__DIR__ . '/Fixtures/minimal.xml');
        $this->assertSame(['directory' => ['vendor/'], 'file' => [], 'regex' => []], $configuration->getIncludes());
        $this->assertSame(['directory' => [], 'file' => [], 'regex' => []], $configuration->getExcludes());
        $this->assertNull($configuration->getFilter()->get(0));
    }

    public function testReadingFullConfiguration_Filters()
    {
        $configuration = $this->reader->read(__DIR__ . '/Fixtures/full.xml');

        $composite = $configuration->getFilter();
        $this->assertInstanceOf('GoGoCrankin\Filter\CompositeFilter', $composite);

        /** @var $filter AbstractFilter */
        $filter = $composite->get(0);
        $this->assertInstanceof('GoGoCrankin\Filter\GlobFilter', $filter);
        $this->assertSame('file', $filter->getKey());
        $this->assertSame('*/Tests/*', $filter->getPattern());

        $filter = $composite->get(1);
        $this->assertInstanceof('GoGoCrankin\Filter\RegexFilter', $filter);
        $this->assertSame('symbol', $filter->getKey());
        $this->assertSame('/getSimpleMock/', $filter->getPattern());

        $filter = $composite->get(2);
        $this->assertInstanceof('GoGoCrankin\Filter\StringFilter', $filter);
        $this->assertSame('error', $filter->getKey());
        $this->assertSame('UndefinedConstant', $filter->getPattern());

        $filter = $composite->get(3);
        $this->assertInstanceof('GoGoCrankin\Filter\GlobFilter', $filter);
        $this->assertSame('symbol', $filter->getKey());
        $this->assertSame('UUID_*', $filter->getPattern());

        $filter = $composite->get(4);
        $this->assertInstanceof('GoGoCrankin\Filter\GlobFilter', $filter);
        $this->assertSame('symbol', $filter->getKey());
        $this->assertSame('uuid_*', $filter->getPattern());

        $filter = $composite->get(5);
        $this->assertInstanceof('GoGoCrankin\Filter\GlobFilter', $filter);
        $this->assertSame('file', $filter->getKey());
        $this->assertSame('*.inc', $filter->getPattern());

        $filter = $composite->get(6);
        $this->assertInstanceof('GoGoCrankin\Filter\RegexFilter', $filter);
        $this->assertSame('file', $filter->getKey());
        $this->assertSame('/\.php$/', $filter->getPattern());
    }

    public function testReadingFullConfiguration_Includes()
    {
        $configuration = $this->reader->read(__DIR__ . '/Fixtures/full.xml');

        $this->assertSame(
            [
                'directory' => ['app', 'vendor'],
                'file'     => ['File.php'],
                'regex'     => ['/.*/'],
            ],
            $configuration->getIncludes()
        );
    }

    public function testReadingFullConfiguration_Excludes()
    {
        $configuration = $this->reader->read(__DIR__ . '/Fixtures/full.xml');

        $this->assertSame(
            [
                'directory' => [],
                'file'     => [],
                'regex'     => ['@/ExcludePath/@', '/foo/'],
            ],
            $configuration->getExcludes()
        );
    }
}
