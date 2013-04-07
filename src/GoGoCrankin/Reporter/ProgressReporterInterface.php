<?php
namespace GoGoCrankin\Reporter;

interface ProgressReporterInterface
{
    public function beginReport();

    public function progress();

    public function endReport();
}
