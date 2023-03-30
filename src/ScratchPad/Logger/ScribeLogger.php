<?php

namespace ScratchPad\Logger;

use ScratchPad\Retry;
use Scribe\LogEntry;
use Scribe\scribeClient;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;

class ScribeLogger implements LoggerInterface
{
    use LoggerImpl;

    private $config;

    /** @var scribeClient */
    private $client = null;

    /** @var TFramedTransport */
    private $transport = null;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param array $message
	 * @param array $option
     * @throws \Throwable
     */
    public function log(array $message, array $option = [])
    {
        Retry::execute([
            'onExecute' => function ($context) use (&$message)
                {
                    if (!isset($this->client))
                    {
                        $host = $this->config['host'] ?? 'localhost';
                        $port = $this->config['port'] ?? 1463;
                        $socket = new TSocket($host, $port, TRUE);
                        $transport = new TFramedTransport($socket);
                        $protocol = new TBinaryProtocolAccelerated($transport, FALSE, FALSE);
                        $client = new scribeClient($protocol, $protocol);
                        $transport->open();

                        $this->transport = $transport;
                        $this->client = $client;
                    }

                    $this->client->Log([
                        new LogEntry([
                            'category' => $this->config['category'],
                            'message' => json_encode($message, JSON_UNESCAPED_UNICODE)
                        ])
                    ]);
                },
            'onFail' => function ($throwable, $context)
                {
                    // cleanup
                    if (isset($this->client))
                    {
                        $this->transport->close();
                        $this->transport = null;
                        $this->client = null;
                    }

                    // wait
                    sleep(rand($this->config['retryWaitMin'] ?? 3, $this->config['retryWaitMax'] ?? 5));
                },
            'maxExecutionCount' => $this->config['retryMaxCount'] ?? 3,
        ]);
    }
}

