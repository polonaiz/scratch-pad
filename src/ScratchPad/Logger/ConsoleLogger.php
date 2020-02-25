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

    public function log(array $message, array $option = [])
    {
        $appendNewLine = $option['appendNewLine'] ?? $this->config['appendNewLine'] ?? 0;
        $format = $option['format'] ?? $this->config['format'] ?? 'compact';

        $options = JSON_UNESCAPED_UNICODE;
        if($format === 'pretty')
		{
			$options |= JSON_PRETTY_PRINT;
		}

        echo \json_encode($message, $options) . "\n";
        if($appendNewLine > 0)
        {
            echo \str_repeat("\n", $appendNewLine);
        }
    }
}