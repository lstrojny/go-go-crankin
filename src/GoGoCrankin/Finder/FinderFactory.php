<?php
namespace GoGoCrankin\Finder;

use GoGoCrankin\Configuration\Configuration;
use Symfony\Component\Finder\Finder;
use Functional as F;

final class FinderFactory implements FinderFactoryInterface
{
    public function createFinder(Configuration $configuration)
    {
        $includes = $configuration->getIncludes();
        $excludes = $configuration->getExcludes();

        $finder = Finder::create()
            ->in($includes['directory'])
            ->exclude($excludes['directory']);

        F\map($includes['file'], [$finder, 'path']);
        F\map($includes['regex'], [$finder, 'path']);
        F\map($excludes['file'], [$finder, 'notPath']);
        F\map($excludes['regex'], [$finder, 'notPath']);

        return $finder
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->files();
    }
}
