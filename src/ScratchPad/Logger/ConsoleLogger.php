<?php

namespace ScratchPad\Logger;

class ConsoleLogger implements LoggerInterface
{
    use LoggerImpl;

    public function log(array $message)
    {
        echo json_encode($message, JSON_UNESCAPED_UNICODE) . "\n";
    }
}