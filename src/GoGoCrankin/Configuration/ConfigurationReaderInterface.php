<?php
namespace GoGoCrankin\Configuration;

interface ConfigurationReaderInterface
{
    /**
     * @param string $fileName
     * @return Configuration
     */
    public function read($fileName);
}
