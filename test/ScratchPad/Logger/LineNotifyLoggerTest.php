<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class LineNotifyLoggerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testLog()
    {
        $logger = new LineNotifyLogger([
            'token' => 'Y37YRSsvorinMUiuPNXGb7XE9Ha1fBnLjQUkzXRdiDg'
        ]);
        $logger->log([
            'test' => 'good'
        ]);

        $this->assertTrue(true);
    }

}
