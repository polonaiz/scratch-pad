<?php

namespace ScratchPad\Logger;

use Exception;
use ScratchPad\Retry;
use Throwable;

class JandiNotifyLogger implements LoggerInterface
{
    use LoggerImpl;

    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function log(array $message, array $option = [])
    {
        Retry::execute([
            'maxExecutionCount' => 1,
            'onExecute' => function () use (&$message, &$executionCount)
            {
                $url = $this->config['url'];
                $httpHeader = [
                    'Accept: application/vnd.tosslab.jandi-v2+json',
                    'Content-type: Application/json'
                ];
                $newMessage = implode("\n", array_map(function ($key, $val) {
                    return sprintf("%s='%s'", $key, $val);
                },
                    $message,
                    array_keys($message)
                ));
                $data["body"] = "Critical Error Log";
                $data["connectInfo"] = [['title' => "logInfo", 'description' => $newMessage]];
                $postFields = json_encode($data);

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

                // https://support.jandi.com/hc/ko/articles/210952203-%EC%9E%94%EB%94%94-%EC%BB%A4%EB%84%A5%ED%8A%B8-%EC%9D%B8%EC%BB%A4%EB%B0%8D-%EC%9B%B9%ED%9B%85-Incoming-Webhook-%EC%9C%BC%EB%A1%9C-%EC%99%B8%EB%B6%80-%EB%8D%B0%EC%9D%B4%ED%84%B0%EB%A5%BC-%EC%9E%94%EB%94%94-%EB%A9%94%EC%8B%9C%EC%A7%80%EB%A1%9C-%EC%88%98%EC%8B%A0%ED%95%98%EA%B8%B0
                /*
                 * Limit
                   - 60 requests / min
                   - 500 requests / 10 min
                   - 제한에 걸렸을 경우 응답코드: 429
                 */
                switch ($statusCode) {
                    case 429:
                        throw new Exception("FAILURE: " . json_encode([
                                'message' => 'Too Many Requests',
                                'info' => $info,
                                'url' => $url,
                                'httpHeader' => $httpHeader,
                                'postFields' => $postFields,
                                'status' => $statusCode,
                                'output' => $output,
                            ]));
                    case 400:
                        throw new Exception("FAILURE: " . json_encode([
                                'message' => 'Invalid Format, Deleted Webhook The webhook Token',
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