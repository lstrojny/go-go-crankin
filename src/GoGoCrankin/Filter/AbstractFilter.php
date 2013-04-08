<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param string $key
     * @param string $pattern
     */
    public function __construct($key, $pattern)
    {
        $this->key = $key;
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    public function shouldIgnore($error, $file, Position $position, $symbol)
    {
        switch ($this->key) {
            case 'error':
                return $this->match($error);

            case 'file':
                return $this->match($file);

            case 'startLine':
                return $this->match($position->getStartLine());

            case 'endLine':
                return $this->match($position->getEndLine());

            case 'startColumn':
                return $this->match($position->getStartColumn());

            case 'endColumn':
                return $this->match($position->getEndColumn());

            case 'symbol':
                return $this->match($symbol);

            default:
                return false;
        }
    }

    protected function match($value)
    {
        /** Value is an empty value and should therefore be treated as not matching */
        if ($value === null) {
            return false;
        }

        return (bool) $this->doMatch($value);
    }

    /**
     * @param string $value
     * @return boolean
     */
    abstract protected function doMatch($value);
}
