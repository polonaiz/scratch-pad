<?php

namespace ScratchPad\Logger;

class ConsoleLogger implements LoggerInterface
{
    use LoggerImpl;

    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function log(array $message)
    {
        $appendNewLine = $this->config['appendNewLine'] ?? 0;

        echo json_encode($message, JSON_UNESCAPED_UNICODE) . "\n";
        if($appendNewLine > 0)
        {
            echo str_repeat("\n", $appendNewLine);
        }
    }
}