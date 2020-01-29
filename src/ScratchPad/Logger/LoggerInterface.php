<?php

namespace ScratchPad\Logger;

interface LoggerInterface
{
    public function log(array $message, array $option = []);

    public function info(array $message, array $option = []);

    public function notice(array $message, array $option = []);

    public function warning(array $message, array $option = []);

    public function error(array $message, array $option = []);

    public function critical(array $message, array $option = []);

}