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

    public static function info(array $message, array $option = [])
    {
        self::$logger->info($message, $option);
    }

    public static function notice(array $message, array $option = [])
    {
        self::$logger->notice($message, $option);
    }

    public static function warning(array $message, array $option = [])
    {
        self::$logger->warning($message, $option);
    }

    public static function error(array $message, array $option = [])
    {
        self::$logger->error($message, $option);
    }

    public static function critical(array $message, array $option = [])
    {
        self::$logger->critical($message, $option);
    }
}