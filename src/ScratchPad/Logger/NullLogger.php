<?php

namespace ScratchPad\Logger;

class NullLogger implements LoggerInterface
{
    use LoggerImpl;

    public function log(array $message)
    {
    }
}