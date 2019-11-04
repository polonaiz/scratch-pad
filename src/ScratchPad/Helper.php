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
        $maxTryCount = $param['maxTryCount'] ?? 1;
        $onTry = $param['onTry'];
        $onCatch = $param['onCatch'] ?? null;
        $onRetrySuccess = $param['onRetrySuccess'] ?? null;

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
                if($tryCount > 1 && isset($onRetrySuccess))
                {
                    $onRetrySuccess($context);
                }
                return $result;
            }
            catch (\Exception $e)
            {
                if ($tryCount < $maxTryCount)
                {
                    if(isset($onCatch))
                    {
                        $onCatch($context);
                    }
                    continue;
                }
                throw $e;
            }
        }
    }
}