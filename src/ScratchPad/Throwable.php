<?php

namespace ScratchPad;

class Throwable
{
    /**
     * @param $t \Throwable
     * @return array|string
     */
    public static function getTraceSafe(\Throwable $t)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $jsonTrace = json_encode($t->getTrace());
        $jsonError = json_last_error();
        $trace = ($jsonError === JSON_ERROR_NONE) ?
            $t->getTrace() :
            $t->getTraceAsString();

        return $trace;
    }
}