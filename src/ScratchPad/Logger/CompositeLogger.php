<?php

namespace ScratchPad\Logger;

class CompositeLogger implements LoggerInterface
{
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
                $value = $value();
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

    private static $selectorAll;
    public static function getSelectorAll()
    {
        if (!isset(self::$selectorAll))
        {
            self::$selectorAll = function (/** @noinspection PhpUnusedParameterInspection */
                $message)
                {
                    return true;
                };
        }
        return self::$selectorAll;
    }

    private static $selectorLevelCritical;
    public static function getSelectorLevelCritical()
    {
        if (!isset(self::$selectorLevelCritical))
        {
            self::$selectorLevelCritical = function ($message)
                {
                    return isset($message['level']) && $message['level'] === 'critical';
                };
        }
        return self::$selectorLevelCritical;
    }
}
