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
        self::$logger->info($message);
    }

    public static function notice(array $message)
    {
        self::$logger->notice($message);
    }

    public static function warning(array $message)
    {
        self::$logger->warning($message);
    }

    public static function error(array $message)
    {
        self::$logger->error($message);
    }

    public static function critical(array $message)
    {
        self::$logger->critical($message);
    }
}