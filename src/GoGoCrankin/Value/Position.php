<?php
namespace GoGoCrankin\Value;

class Position
{
    /**
     * @var integer
     */
    private $startLine;

    /**
     * @var integer
     */
    private $endLine;

    /**
     * @var integer
     */
    private $startColumn;

    /**
     * @var integer
     */
    private $endColumn;

    public function __construct($startLine = null, $endLine = null, $startColumn = null, $endColumn = null)
    {
        $this->startLine = $startLine;
        $this->endLine = $endLine;
        $this->startColumn = $startColumn;
        $this->endColumn = $endColumn;
    }

    /**
     * @param array $data
     * @return Position
     */
    public static function createFromArray(array $data)
    {
        $data = array_filter($data, 'is_int');

        $startLine = array_shift($data);
        $startCharacter = array_shift($data);
        $endLine = array_shift($data);
        $endCharacter = array_shift($data);

        return new static($startLine, $endLine, $startCharacter, $endCharacter);
    }

    /**
     * @return integer
     */
    public function getEndColumn()
    {
        return $this->endColumn;
    }

    /**
     * @return integer
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * @return integer
     */
    public function getStartColumn()
    {
        return $this->startColumn;
    }

    /**
     * @return integer
     */
    public function getStartLine()
    {
        return $this->startLine;
    }

    /**
     * @return boolean
     */
    public function isSingleLine()
    {
        return $this->startLine === $this->endLine || $this->endLine === null;
    }

    /**
     * @return boolean
     */
    public function isSingleCharacter()
    {
        return $this->startColumn === $this->endColumn || $this->endColumn === null;
    }

    /**
     * @return boolean
     */
    public function hasColumn()
    {
        return $this->startColumn !== null || $this->endColumn !== null;
    }
}

