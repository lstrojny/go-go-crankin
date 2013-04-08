<?php
namespace GoGoCrankin\Configuration;

use GoGoCrankin\Filter\FilterInterface;

class Configuration
{
    private $filter;

    private $includes;

    private $excludes;

    public function __construct(FilterInterface $filter, array $includes, array $excludes)
    {
        $this->filter = $filter;
        $this->includes = $includes;
        $this->excludes = $excludes;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getIncludes()
    {
        return $this->includes;
    }

    public function getExcludes()
    {
        return $this->excludes;
    }
}
