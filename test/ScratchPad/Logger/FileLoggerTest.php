<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class FileLoggerTest extends TestCase
{
    public function testLog()
    {
        $filename = '/tmp/test';
        if(file_exists($filename))
        {
            unlink($filename);
        }

        $logger = new FileLogger([
            'filename' => $filename
        ]);

        $logger->log(['k' => 'v']);
        $this->assertEquals(
            "{\"k\":\"v\"}\n",
            \file_get_contents($filename)
        );
        unset($logger);

        $logger = new FileLogger([
            'filename' => $filename,
            'truncate' => true,
        ]);
        $logger->log(['k' => 'v']);
        $this->assertEquals(
            "{\"k\":\"v\"}\n",
            \file_get_contents($filename)
        );
        $logger->log(['k' => 'v']);
        $this->assertEquals(
            "{\"k\":\"v\"}\n{\"k\":\"v\"}\n",
            \file_get_contents($filename)
        );

    }
}
