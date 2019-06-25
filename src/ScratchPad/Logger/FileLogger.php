<?php

namespace ScratchPad\Logger;

class FileLogger implements LoggerInterface
{
    use LoggerImpl;

    private $config;

    public function __construct($config = [])
    {
        $config += [
            'truncate' => false,
        ];
        $this->config = $config;

        if($config['truncate'])
        {
            \file_put_contents($config['filename'], '');
        }
    }

    public function log(array $message)
    {
        \file_put_contents(
            $this->config['filename'],
            \json_encode($message, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );
    }
}
