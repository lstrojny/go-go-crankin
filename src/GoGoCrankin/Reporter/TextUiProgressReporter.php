<?php
namespace GoGoCrankin\Reporter;

final class TextUiProgressReporter implements ProgressReporterInterface
{
    private $count = 1;

    private $char = '.';

    private $width = 80;

    public function start()
    {
        return PHP_EOL . 'go-go-crankin dev-master' . PHP_EOL . PHP_EOL;
    }

    public function stop()
    {
        return '';
    }

    public function beginReport($section, $context = null)
    {
        if ($context) {
            return PHP_EOL . $section . ' ' . $context . PHP_EOL;
        }

        return PHP_EOL . $section . PHP_EOL;
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
        return PHP_EOL;
    }
}
