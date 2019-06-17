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