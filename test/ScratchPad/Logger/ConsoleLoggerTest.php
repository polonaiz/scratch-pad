<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class ConsoleLoggerTest extends TestCase
{
    public function testLog()
    {
        $logger = new ConsoleLogger(['appendNewLine' => 3]);
        $logger->log(['type' => 'test1']);
        $logger->log(['type' => 'test2']);
    }
}
