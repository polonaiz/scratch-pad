<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testDefaults()
    {
        Logger::setLogger($memoryLogger = new MemoryLogger());

        Logger::info(['type' => 'test']);
        Logger::warning(['type' => 'noData']);

        $this->assertEquals("info", $memoryLogger->logs[0]['level']);
        $this->assertEquals("warning", $memoryLogger->logs[1]['level']);
        $this->assertEquals("noData", $memoryLogger->logs[1]['type']);
//        echo json_encode($memoryLogger->logs);
    }

}
