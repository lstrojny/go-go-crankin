<?php
namespace GoGoCrankin\Reporter;

class TextUiProgressReporter implements ProgressReporterInterface
{
    private $count = 1;

    private $char = '.';

    private $width = 80;

    public function beginReport()
    {
        return PHP_EOL . 'go-go-cranking dev-master' . PHP_EOL . PHP_EOL;
    }

    public function progress()
    {
        if (($this->count++ % $this->width) === 0) {
            $this->count = 1;
            return $this->char . PHP_EOL;
        }

        return $this->char;
    }

    public function endReport()
    {
        return PHP_EOL . PHP_EOL;
    }
}
