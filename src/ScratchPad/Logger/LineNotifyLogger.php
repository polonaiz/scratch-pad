<?php

namespace ScratchPad\Logger;

use Exception;
use ScratchPad\Retry;
use Throwable;

class LineNotifyLogger implements LoggerInterface
{
    use LoggerImpl;

    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * @param array $message
     * @param array $option
     * @throws Throwable
     */
    public function log(array $message, array $option = [])
    {
        Retry::execute([
            'maxExecutionCount' => 5,
            'onExecute' => function() use (&$message, &$executionCount) {
                $url = "https://notify-api.line.me/api/notify";
                $token = $this->config['token'] ?? null;
                $httpHeader = [
                    "Authorization: Bearer {$token}"
                ];
                $postFields = "message=" . substr(json_encode($message, JSON_PRETTY_PRINT), 0, 1024);
                $connectTimeout = $this->config['connectTimeout'] ?? 5;
                $executeTimeout = $this->config['executionTimeout'] ?? 10;

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
                curl_setopt($curl, CURLOPT_TIMEOUT, $executeTimeout);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $output = curl_exec($curl);
                $info = curl_getinfo($curl);
                $statusCode = $info['http_code'];

                switch ($statusCode) // https://developers.line.biz/en/reference/messaging-api/#status-codes
                {
                    case 0:
                    case 500:
                        // required retry
                        throw new Exception("FAILURE: " . json_encode([
                                'message' => 'curl request failed',
                                'info' => $info,
                                'url' => $url,
                                'httpHeader' => $httpHeader,
                                'postFields' => $postFields,
                                'status' => $statusCode,
                                'output' => $output,
                            ]));
                    default:
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        $data = json_decode($output, true);
                        $status = $data['status'];
                        $message = $data['message'];
                }
            },
        ]);
    }
}