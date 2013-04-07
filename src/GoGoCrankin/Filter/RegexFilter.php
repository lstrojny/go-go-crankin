<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;

class RegexFilter implements FilterInterface
{
    private $regularExpressions = [];

    private $expressionKeys = ['error', 'file', 'startLine', 'endLine', 'startColumn', 'endColumn', 'symbol'];

    public function __construct(array $regularExpressions)
    {
        array_walk($regularExpressions, [$this, 'assertArrayKey']);
        array_walk($regularExpressions, [$this, 'assertRegex']);
        $this->regularExpressions = $regularExpressions;
    }

    public function filter($error, $file, Position $position, $symbol)
    {
        return !$this->match('error', $error)
            && !$this->match('file', $file)
            && !$this->match('startLine', $position->getStartLine())
            && !$this->match('endLine', $position->getEndLine())
            && !$this->match('startColumn', $position->getStartColumn())
            && !$this->match('endColumn', $position->getEndColumn())
            && !$this->match('symbol', $symbol);
    }

    private function match($regexName, $subject)
    {
        if ($subject === null) {
            return true;
        }

        if (!isset($this->regularExpressions[$regexName])) {
            return false;
        }

        /** Append PCRE_DOLLAR_ENDONLY */
        $regex = $this->regularExpressions[$regexName] . 'D';

        return !preg_match($regex, $subject);
    }

    private function assertRegex($regex)
    {
        if (substr($regex, 0, 1) !== substr($regex, -1)) {
            throw new \InvalidArgumentException(sprintf('Invalid regex "%s"', $regex));
        }

        return $regex;
    }

    private function assertArrayKey($_, $key)
    {
        if (!in_array($key, $this->expressionKeys)) {
            throw new \InvalidARgumentException(
                sprintf(
                    'Invalid array key "%s" given. Expected at least one of "%s"',
                    $key,
                    join('", "', $this->expressionKeys)
                )
            );
        }
    }
}
