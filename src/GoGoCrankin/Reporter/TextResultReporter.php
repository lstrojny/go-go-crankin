<?php
namespace GoGoCrankin\Reporter;

use GoGoCrankin\Value\Position;

class TextResultReporter implements ResultReporterInterface
{
    private $lines = [];

    public function beginSection($error)
    {
        $this->lines[] = '';
        $this->lines[] = $error . ':';
        $this->lines[] = str_repeat('-', strlen($error) + 1);
    }

    public function reportViolation($error, $file, Position $position, $symbol)
    {
        $this->lines[] = sprintf('%s, %s    %s', $file, $this->formatPosition($position), $symbol);
    }

    public function endSection($error)
    {
    }

    public function getString()
    {
        return join(PHP_EOL, $this->lines);
    }

    public function beginReport()
    {
        $this->lines = [];
        $this->lines[] = 'HipHop static code analysis report';
    }

    public function endReport()
    {
        $this->lines[] = '';
    }

    private function formatPosition(Position $position)
    {
        $string = 'line ';
        $string .= $this->formatRange($position->isSingleLine(), $position->getStartLine(), $position->getEndLine());


        if ($position->hasColumn()) {
            $string .= ', character ';
            $string .= $this->formatRange(
                $position->isSingleCharacter(),
                $position->getStartColumn(),
                $position->getEndColumn()
            );
        }

        return $string;
    }

    /**
     * @param boolean $isSingle
     * @param string $from
     * @param string $to
     * @return string
     */
    private function formatRange($isSingle, $from, $to)
    {
        return $isSingle ? $from : sprintf('%d - %d', $from, $to);
    }
}
