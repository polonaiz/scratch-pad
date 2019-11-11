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
                'category' => 'test'
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
