<?php
namespace GoGoCrankin\Filter;

class GlobFilter extends AbstractFilter
{
    protected function doMatch($value)
    {
        return fnmatch($this->pattern, $value);
    }
}
