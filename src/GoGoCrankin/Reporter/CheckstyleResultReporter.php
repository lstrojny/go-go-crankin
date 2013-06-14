<?php
namespace GoGoCrankin\Reporter;

use DOMDocument;
use DOMNode;
use GoGoCrankin\Value\Position;

final class CheckstyleResultReporter implements ResultReporterInterface
{
    /**
     * @var DOMDocument
     */
    private $doc;

    /**
     * @var DOMNode
     */
    private $checkstyleNode;

    /**
     * @var string
     */
    private $source;

    /**
     * @var DOMNode[]
     */
    private $fileNodes = [];

    public function __construct()
    {
        $this->doc = new DOMDocument('1.0');
        $this->doc->formatOutput = true;
    }

    public function beginSection($section)
    {
        $this->source = $section;
    }

    public function reportViolation($error, $file, Position $position, $symbol)
    {
        $errorNode = $this->doc->createElement('error');
        $errorNode->setAttribute('line', $position->getStartLine());
        $errorNode->setAttribute('column', $position->getStartColumn());
        $errorNode->setAttribute('severity', 'error');
        $errorNode->setAttribute('message', sprintf('%s: %s', $error, $symbol));
        $errorNode->setAttribute('source', $this->source);

        if (!isset($this->fileNodes[$file])) {
            $this->fileNodes[$file] = $this->doc->createElement('file');
            $this->fileNodes[$file]->setAttribute('name', $file);
            $this->checkstyleNode->appendChild($this->fileNodes[$file]);
        }
        $this->fileNodes[$file]->appendChild($errorNode);
    }

    public function endSection($error)
    {
    }

    public function getString()
    {
        return $this->doc->saveXML();
    }

    public function beginReport()
    {
        $this->checkstyleNode = $this->doc->createElement('checkstyle');
        $this->checkstyleNode->setAttribute('version', '1.0');
        $this->doc->appendChild($this->checkstyleNode);
        $this->fileNodes = [];
    }

    public function endReport()
    {
    }
}
