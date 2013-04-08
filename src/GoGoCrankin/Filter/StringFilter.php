<?php
namespace GoGoCrankin\Filter;

class StringFilter extends AbstractFilter
{
    protected function doMatch($value)
    {
        return (string) $this->pattern === (string) $value;
    }
}
