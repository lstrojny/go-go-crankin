<?php
namespace GoGoCrankin\Finder;

use GoGoCrankin\Configuration\Configuration;
use Symfony\Component\Finder\Finder;

interface FinderFactoryInterface
{
    /** @return Finder */
    public function createFinder(Configuration $configuration);
}
