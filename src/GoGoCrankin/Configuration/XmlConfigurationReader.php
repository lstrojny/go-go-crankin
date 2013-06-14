<?php
namespace GoGoCrankin\Configuration;

declare(ticks=1);

use DOMDocument;
use DOMXPath;
use DOMElement;
use GoGoCrankin\Filter\CompositeFilter;
use LibXMLError;
use Exception;
use RuntimeException;

class XmlConfigurationReader implements ConfigurationReaderInterface
{
    private $filterMap = [
        'glob'   => 'GoGoCrankin\\Filter\\GlobFilter',
        'string' => 'GoGoCrankin\\Filter\\StringFilter',
        'regex'  => 'GoGoCrankin\\Filter\\RegexFilter',
    ];

    public function read($fileName)
    {
        $doc = $this->handleXmlErrors(static function () use ($fileName) {
            $doc = new DOMDocument('1.0');
            $doc->load($fileName);
            $doc->relaxNGValidate(__DIR__ . '/1.0/schema.rng');

            return $doc;
        });

        $xpath = new DOMXPath($doc);

        $includes = $excludes = [
            'directory' => [],
            'file'      => [],
            'regex'     => [],
        ];
        foreach ($this->handleXmlErrors([$xpath, 'query'], ['/go-go-crankin/files/includes/*']) as $includeNode) {
            /** @var $includeNode DOMElement */
            $includes[$includeNode->tagName][] = $this->processVariables($includeNode->tagName, $includeNode->textContent, $fileName);
        }
        foreach ($this->handleXmlErrors([$xpath, 'query'], ['/go-go-crankin/files/excludes/*']) as $excludeNode) {
            /** @var $excludeNode DOMElement */
            $excludes[$excludeNode->tagName][] = $this->processVariables($excludeNode->tagName, $excludeNode->textContent, $fileName);
        }

        $filters = [];
        foreach ($this->handleXmlErrors([$xpath, 'query'], ['/go-go-crankin/filters/*/*']) as $filterNode) {
            /** @var $filterNode DOMElement */
            $filters[] = $this->processFilters($filterNode);
        }

        return new Configuration(new CompositeFilter($filters), $includes, $excludes);
    }

    private function processFilters(DOMElement $filterNode)
    {
        $className = $this->filterMap[$filterNode->tagName];

        return new $className($filterNode->getAttribute('key'), $filterNode->textContent);
    }

    private function handleXmlErrors(callable $callback, array $arguments = [])
    {
        $endHandlingErrors = $this->beginHandlingErrors();
        try {
            $return = call_user_func_array($callback, $arguments);
        } catch (Exception $e) {
            $endHandlingErrors();
            throw $e;
        }
        $endHandlingErrors();
        return $return;
    }

    private function beginHandlingErrors()
    {
        $useInternalErrors = libxml_use_internal_errors(true);
        register_tick_function([&$this, 'tryThrowXmlErrors']);
        return function () use ($useInternalErrors) {
            libxml_use_internal_errors($useInternalErrors);
            unregister_tick_function([&$this, 'tryThrowXmlErrors']);
        };
    }

    private function processVariables($nodeType, $value, $fileName)
    {
        if ($nodeType === 'regex') {
            return $value;
        }

        $value = str_replace(['${basedir}', '${file}'], [dirname($fileName), $fileName], $value);

        return $value;
    }

    public function tryThrowXmlErrors()
    {
        $errors = libxml_get_errors();
        if (!$errors) {
            return;
        }

        $e = null;
        foreach ($errors as $error) {
            /** @var $error LibXMLError */
            $e = new RuntimeException(
                sprintf(
                    '%s in %s, line %d, column %d',
                    trim($error->message),
                    $error->file,
                    $error->line,
                    $error->column
                ),
                null,
                $e
            );
        }

        throw $e;
    }
}
