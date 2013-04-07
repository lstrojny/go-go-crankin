<?php
namespace GoGoCrankin\Reporter;

class NullProgressReporter implements ProgressReporterInterface
{
    public function beginReport()
    {
    }

    public function progress()
    {
    }

    public function endReport()
    {
    }
}
