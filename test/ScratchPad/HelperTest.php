<?php

namespace ScratchPad;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testExecuteWithRetryNormalCase()
    {
        //
        $tryCount = null;
        $normalTask = function ($context) use (&$tryCount)
            {
                $tryCount = $context['tryCount'];
            };

        //
        Helper::executeWithRetry([
            'onTry' => $normalTask
        ]);

        //
        $this->assertEquals(1, $tryCount);
    }

    /**
     * @throws \Exception
     */
    public function testExecuteWithRetry2()
    {
        //
        $tryCount = null;
        $underThirdTryFailingTask = function ($context) use (&$tryCount)
            {
                $tryCount = $context['tryCount'];
                if ($tryCount < 3)
                {
                    throw new \Exception("need more try: current try count : {$tryCount}");
                }
            };

        //
        Helper::executeWithRetry([
            'onTry' => $underThirdTryFailingTask,
            'maxTryCount' => 5
        ]);

        $this->assertEquals(3, $tryCount);
    }

    /**
     * @throws \Exception
     */
    public function testExecuteWithDelayedRetry()
    {
        //
        $tryCount = null;
        $initialTime = \time();
        $task = function ($context) use (&$initialTime, &$tryCount)
            {
                $tryCount = $context['tryCount'];

                $time = \time();
                if ($initialTime + 2 > $time)
                {
                    throw new \Exception(\json_encode([
                        'type' => 'tryLater',
                        'context' => $context
                    ]));
                }
            };

        //
        Helper::executeWithRetry([
            'onTry' => $task,
            'onCatch' => function ()
                {
                    usleep(1000000);
                },
            'maxTryCount' => 10
        ]);

        $this->assertEquals(3, $tryCount);
    }
}
