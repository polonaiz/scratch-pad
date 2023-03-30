<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class ScribeLoggerTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function testLog()
    {
        try
        {
            $logger = new ScribeLogger([
                'host' => '172.17.0.1',
                'category' => 'test',
                'retryWaitMin' => 1,
                'retryWaitMax' => 1,
                'retryMaxCount' => 3,
            ]);
            $logger->info(['type' => 'test']);
        }
        catch (\Throwable $t)
        {
            throw $t;
        }

        $this->assertTrue(true);
    }
}
