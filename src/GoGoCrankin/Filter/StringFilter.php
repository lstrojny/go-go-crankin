<?php
namespace GoGoCrankin\Filter;

final class StringFilter extends AbstractFilter
{
    protected function doMatch($value)
    {
        return (string) $this->pattern === (string) $value;
    }
}
