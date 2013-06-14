<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;

final class RegexFilter extends AbstractFilter
{
    public function __construct($key, $pattern)
    {
        $this->assertRegex($pattern);
        parent::__construct($key, $pattern);
    }

    protected function doMatch($value)
    {
        /** Append PCRE_DOLLAR_ENDONLY */
        return preg_match($this->pattern . 'D', $value);
    }

    private function assertRegex($regex)
    {
        if (substr($regex, 0, 1) !== substr($regex, -1)) {
            throw new \InvalidArgumentException(sprintf('Invalid regex "%s"', $regex));
        }
    }
}
