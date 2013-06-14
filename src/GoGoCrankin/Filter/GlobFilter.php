<?php
namespace GoGoCrankin\Filter;

final class GlobFilter extends AbstractFilter
{
    protected function doMatch($value)
    {
        return fnmatch($this->pattern, $value);
    }
}
