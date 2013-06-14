<?php
namespace GoGoCrankin\Reporter;

final class NullProgressReporter implements ProgressReporterInterface
{
    public function beginReport($section, $context = null)
    {
    }

    public function progress()
    {
    }

    public function endReport()
    {
    }

    public function start()
    {
    }

    public function stop()
    {
    }
}
