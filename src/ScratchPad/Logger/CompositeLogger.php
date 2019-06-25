<?php

namespace ScratchPad\Logger;

class CompositeLogger implements LoggerInterface
{
    use LoggerImpl;

    /**
     * @var array
     */
    private $config = [];

    /**
     * CompositeLogger constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config +=
            [
                'loggerFilterPairs' => [],
                'defaults' => []
            ];
        $this->config = $config;
    }

    public function log(array $message)
    {
        //
        $mergedMessage = $this->config['defaults'];

        //evaluate defaults
        foreach ($mergedMessage as $key => &$value)
        {
            if (is_callable($value))
            {
                $value = $value();
            }
        }

        //add key-value to merged
        $mergedMessage += $message;

        //
        foreach ($this->config['loggerFilterPairs'] as $loggerFilterPair)
        {
            /** @var $logger LoggerInterface */
            $logger = $loggerFilterPair['logger'];
            $filter = $loggerFilterPair['filter'];
            if ($filter($mergedMessage))
            {
                $logger->log($mergedMessage);
            }
        }
    }


    private static $stamper;

    public static function getTimeStamper()
    {
        if (!isset(self::$stamper))
        {
            self::$stamper = function ()
                {
                    return (new \DateTime())->format('Y-m-d H:i:s.u T');
                };
        }
        return self::$stamper;
    }

    public static function getSelectorAll()
    {
        return function (/** @noinspection PhpUnusedParameterInspection */
            $message)
            {
                return true;
            };
    }

    public static function getSelectorLevelCritical()
    {
        return self::getSelectorLevel(['critical']);
    }

    public static function getSelectorLevel(array $levels)
    {
        return function ($message) use ($levels)
            {
                return isset($message['level']) && in_array($message['level'], $levels);
            };
    }
}
