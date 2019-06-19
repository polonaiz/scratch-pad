<?php

namespace ScratchPad\Logger;

class MemoryLogger implements LoggerInterface
{
    public $logs = [];

    public function log(array $message)
    {
        $this->logs[] = $message;
    }
}