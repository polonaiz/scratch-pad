<?php

namespace ScratchPad;

class Helper
{
    /**
     * @param array $param
     * @return mixed
     * @throws \Exception
     */
    public static function executeWithRetry($param = [])
    {
        $maxTryCount = $param['maxTryCount'];
        $onTry = $param['onTry'];
        $onCatch = $param['onCatch'];
        $onRetrySuccess = $param['onRetrySuccess'];

        $tryCount = 0;
        $context = [
            'tryCount' => &$tryCount,
            'maxTryCount' => &$maxTryCount
        ];
        while ($tryCount < $maxTryCount)
        {
            $tryCount++;
            try
            {
                $result = $onTry($context);
                if($tryCount > 1)
                {
                    $onRetrySuccess($context);
                }
                return $result;
            }
            catch (\Exception $e)
            {
                if ($tryCount < $maxTryCount)
                {
                    $onCatch($context);
                    continue;
                }
                throw $e;
            }
        }
    }
}