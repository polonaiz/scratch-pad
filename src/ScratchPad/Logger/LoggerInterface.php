<?php

namespace ScratchPad\Logger;

interface LoggerInterface
{
    public function log(array $message);

    public function info(array $message);

    public function notice(array $message);

    public function warning(array $message);

    public function error(array $message);

    public function critical(array $message);

}