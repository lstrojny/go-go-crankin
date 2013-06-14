<?php
namespace GoGoCrankin\Reporter;

interface ProgressReporterInterface
{
    public function start();

    public function beginReport($section, $context = null);

    public function progress();

    public function endReport();

    public function stop();
}
