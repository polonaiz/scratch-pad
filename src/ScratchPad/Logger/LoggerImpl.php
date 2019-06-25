<?php


namespace ScratchPad\Logger;


trait LoggerImpl
{
    public function info(array $message)
    {
        $mergedMessage['level'] = 'info';
        $mergedMessage += $message;
        $this->log($mergedMessage);
    }

    public function notice(array $message)
    {
        $mergedMessage['level'] = 'notice';
        $mergedMessage += $message;
        $this->log($mergedMessage);
    }

    public function warning(array $message)
    {
        $mergedMessage['level'] = 'warning';
        $mergedMessage += $message;
        $this->log($mergedMessage);
    }

    public function error(array $message)
    {
        $mergedMessage['level'] = 'error';
        $mergedMessage += $message;
        $this->log($mergedMessage);
    }

    public function critical(array $message)
    {
        $mergedMessage['level'] = 'critical';
        $mergedMessage += $message;
        $this->log($mergedMessage);
    }
}