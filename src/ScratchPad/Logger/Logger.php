<?php

namespace ScratchPad\Logger;

class Logger
{
    /** @var LoggerInterface */
    private static $logger;

    private function __construct()
    {
    }

    /**
     * @param $logger LoggerInterface
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    public static function getLogger()
    {
        return self::$logger;
    }

    private static function log(array $message)
    {
        self::$logger->log($message);
    }

    public static function info(array $message)
    {
        $mergedMessage['level'] = 'info';
        $mergedMessage += $message;
        self::log($mergedMessage);
    }

    public static function notice(array $message)
    {
        $mergedMessage['level'] = 'notice';
        $mergedMessage += $message;
        self::log($mergedMessage);
    }

    public static function warning(array $message)
    {
        $mergedMessage['level'] = 'warning';
        $mergedMessage += $message;
        self::log($mergedMessage);
    }

    public static function error(array $message)
    {
        $mergedMessage['level'] = 'error';
        $mergedMessage += $message;
        self::log($mergedMessage);
    }

    public static function critical(array $message)
    {
        $mergedMessage['level'] = 'critical';
        $mergedMessage += $message;
        self::log($mergedMessage);
    }
}