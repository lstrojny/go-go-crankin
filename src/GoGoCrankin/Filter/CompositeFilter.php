<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;

final class CompositeFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @param FilterInterface[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function get($index)
    {
        return isset($this->filters[$index]) ? $this->filters[$index] : null;
    }

    public function shouldIgnore($error, $file, Position $position, $symbol)
    {
        foreach ($this->filters as $filter) {
            if ($filter->shouldIgnore($error, $file, $position, $symbol)) {
                return true;
            }
        }

        return false;
    }
}
