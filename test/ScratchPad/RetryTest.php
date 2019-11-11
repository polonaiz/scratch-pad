<?php

namespace ScratchPad;

use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    /**
     * @throws \Throwable
     */
    public function testExecuteOnce()
    {
        //
        $executionCount = 0;

        //
        Retry::execute([
            'onExecute' => function ($context) use (&$executionCount)
                {
                    $executionCount++;
                }
        ]);

        //
        $this->assertEquals(1, $executionCount);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteOnceOccurException()
    {
        $this->expectException('exception');

        //
        Retry::execute([
            'onExecute' => function ($context)
                {
                    throw new \Exception('exception');
                }
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteAgainOnFail()
    {
        //
        $executionCount = 0;

        //
        Retry::execute([
            'onExecute' => function ($context) use (&$executionCount)
                {
                    $executionCount++;
                    if ($context['executionCount'] !== $executionCount)
                    {
                        throw new \Exception(\json_encode([
                            'type' => 'executionCountNotEqual',
                        ]));
                    }
                    if ($context['executionCount'] < 3)
                    {
                        throw new \Exception(\json_encode([
                            'type' => 'executionCountNotEnough',
                            'executionCount' => $context['executionCount']
                        ]));
                    }
                },
            'maxExecutionCount' => 5
        ]);

        $this->assertEquals(3, $executionCount);
    }

    /**
     * @throws \Throwable
     */
    public function testExecuteAgainOnFailWithDelay()
    {
        //
        $executionCount = null;
        $initialTime = \time();

        //
        Retry::execute([
            'onExecute' => function ($context) use (&$initialTime, &$executionCount)
                {
                    $executionCount = $context['executionCount'];
                    if ($initialTime + 2 > \time())
                    {
                        throw new \Exception(\json_encode([
                            'type' => 'needMoreTimeElapsed',
                            'context' => $context
                        ]));
                    }
                },
            'onFail' => function (\Throwable $throwable, $context)
                {
                    usleep(1000000);
                },
            'maxExecutionCount' => 10
        ]);

        $this->assertEquals(3, $executionCount);
    }
}
