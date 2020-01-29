<?php


namespace ScratchPad\Logger;


trait LoggerImpl
{
    public function info(array $message, array $option = [])
    {
        $mergedMessage['level'] = 'info';
        $mergedMessage += $message;
        $this->log($mergedMessage, $option);
    }

    public function notice(array $message, array $option = [])
    {
        $mergedMessage['level'] = 'notice';
        $mergedMessage += $message;
        $this->log($mergedMessage, $option);
    }

    public function warning(array $message, array $option = [])
    {
        $mergedMessage['level'] = 'warning';
        $mergedMessage += $message;
        $this->log($mergedMessage, $option);
    }

    public function error(array $message, array $option = [])
    {
        $mergedMessage['level'] = 'error';
        $mergedMessage += $message;
        $this->log($mergedMessage, $option);
    }

    public function critical(array $message, array $option = [])
    {
        $mergedMessage['level'] = 'critical';
        $mergedMessage += $message;
        $this->log($mergedMessage, $option);
    }
}