<?php

namespace ScratchPad\Logger;

use Scribe\LogEntry;
use Scribe\scribeClient;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

class ScribeLogger implements LoggerInterface
{
    private $host = 'localhost';
    private $port = 1463;
    private $category = 'scribe-logger-default-category';

    /** @var scribeClient */
    private $client = null;

    /** @var TFramedTransport */
    private $transport = null;

    public function __construct(array $config = [])
    {
        if (isset($config['host'])) $this->host = $config['host'];
        if (isset($config['port'])) $this->port = $config['port'];
        if (isset($config['category'])) $this->category = $config['category'];
    }

    private function reopenSession()
    {
        $this->closeSession();

        $socket = new TSocket($this->host, $this->port, TRUE);
        $this->transport = new TFramedTransport($socket);
        $protocol = new TBinaryProtocolAccelerated($this->transport, FALSE, FALSE);
        $this->client = new scribeClient($protocol, $protocol);
        $this->transport->open();
    }

    private function closeSession()
    {
        if (isset($this->client))
        {
            $this->transport->close();
            $this->client = null;
        }
    }

    /**
     * @param array $message
     * @throws \Exception
     */
    public function log(array $message)
    {
        try
        {
            $this->reopenSession();
            $this->client->Log(
                [
                    new LogEntry([
                        'category' => $this->category,
                        'message' => json_encode($message, JSON_UNESCAPED_UNICODE)
                    ]),
                ]);
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
}

