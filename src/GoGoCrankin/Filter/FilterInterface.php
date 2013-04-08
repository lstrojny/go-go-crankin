<?php
namespace GoGoCrankin\Filter;

use GoGoCrankin\Value\Position;

interface FilterInterface
{
    /**
     * @param string $error
     * @param string $file
     * @param Position $position
     * @param string $symbol
     * @return bool
     */
    public function shouldIgnore($error, $file, Position $position, $symbol);
}
