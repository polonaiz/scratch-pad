<?php

namespace ScratchPad\Logger;

class MemoryLogger implements LoggerInterface
{
    public $logs = [];

    public function log(array $values)
    {
        $this->logs[] = $values;
    }
}