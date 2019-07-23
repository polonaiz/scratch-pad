<?php

namespace ScratchPad\Exception;


class Exception
{
    public static function convertNonFatalErrorToException()
    {
        set_error_handler
        (
            function ($errno, $errstr, $errfile, $errline)
            {
                throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        );
    }

    public static function registerErrorHandlerToShutdownFunction(callable $callable)
    {
        register_shutdown_function
        (
            function () use ($callable)
            {
                $error = error_get_last();
                if (isset($error))
                {
                    $callable($error);
                }
            }
        );
    }

    /**
     * @param $e \Exception
     * @return array|string
     */
    public static function getTraceSafe($e)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $jsonTrace = json_encode($e->getTrace());
        $jsonError = json_last_error();
        $trace = ($jsonError === JSON_ERROR_NONE) ?
            $e->getTrace() :
            $e->getTraceAsString();

        return $trace;
    }
}