<?php
namespace GoGoCrankin\Reporter;

use GoGoCrankin\Value\Position;

interface ResultReporterInterface
{
    public function beginReport();

    public function endReport();

    public function beginSection($error);

    public function reportViolation($error, $file, Position $position, $symbol);

    public function endSection($error);

    public function getString();
}
