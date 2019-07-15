<?php

namespace ScratchPad;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testExecuteWithRetry()
    {
        Helper::executeWithRetry([
            'maxTryCount' => 5,
            'onTry' => function ($context) {
                echo json_encode(['type' => 'onTry', 'context' => $context]) . PHP_EOL;
                if ($context['tryCount'] < 3)
                {
                    throw new \Exception('need more try');
                }
            },
            'onCatch' => function ($context) {
                echo json_encode(['type' => 'onCatch', 'context' => $context]) . PHP_EOL;
            },
            'onRetrySuccess' => function ($context) {
                echo json_encode(['type' => 'onRetrySuccess', 'context' => $context]) . PHP_EOL;
            },
        ]);

        try
        {
            Helper::executeWithRetry([
                'maxTryCount' => 2,
                'onTry' => function ($context) {
                    echo json_encode(['type' => 'onTry', 'context' => $context]) . PHP_EOL;
                    if ($context['tryCount'] < 3)
                    {
                        throw new \Exception('need more try');
                    }
                },
                'onCatch' => function ($context) {
                    echo json_encode(['type' => 'onCatch', 'context' => $context]) . PHP_EOL;
                },
                'onRetrySuccess' => function ($context) {
                    echo json_encode(['type' => 'onRetrySuccess', 'context' => $context]) . PHP_EOL;
                },
            ]);
            throw new \Exception("ExceptionExpected");
        }
        catch (\Exception $e)
        {
            echo json_encode(['type' => 'FinallyFailed']) . PHP_EOL;
        }

        $this->assertTrue(true);
    }
}
