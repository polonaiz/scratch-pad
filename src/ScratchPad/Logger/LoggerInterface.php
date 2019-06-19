<?php

namespace ScratchPad\Logger;

interface LoggerInterface
{
    /**
     * @param array $message
     */
    public function log(array $message);
}