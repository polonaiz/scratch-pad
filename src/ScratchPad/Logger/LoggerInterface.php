<?php

namespace ScratchPad\Logger;

interface LoggerInterface
{
    /**
     * @param array $values
     */
    public function log(array $values);
}