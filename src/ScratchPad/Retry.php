<?php

namespace ScratchPad;

class Retry
{
    /**
     * @param array $param
     * @return mixed
     * @throws \Throwable
     */
    public static function execute($param = [])
    {
        $maxExecutionCount = $param['maxExecutionCount'] ?? 1;
        $onExecute = $param['onExecute'];
        $onFail = $param['onFail'] ?? null;

        $executionCount = 0;
        $context = [
            'executionCount' => &$executionCount,
            'maxExecutionCount' => &$maxExecutionCount
        ];
        while ($executionCount < $maxExecutionCount)
        {
            $executionCount++;
            try
            {
                $result = $onExecute($context);
                if ($executionCount > 1 && isset($onRetrySuccess))
                {
                    $onRetrySuccess($context);
                }
                return $result;
            }
            catch (\Throwable $throwable)
            {
                if ($executionCount >= $maxExecutionCount)
                {
                    throw $throwable;
                }

                if (isset($onFail))
                {
                    $onFail($throwable, $context);
                }
                continue;
            }
        }
    }
}