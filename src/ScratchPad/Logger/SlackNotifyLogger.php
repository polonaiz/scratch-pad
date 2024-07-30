<?php

namespace ScratchPad\Logger;

use Exception;
use ScratchPad\Retry;
use Throwable;

class SlackNotifyLogger implements LoggerInterface
{
	use LoggerImpl;

	private $config;

	public function __construct($config = [])
	{
		$this->config = $config;
	}

	/**
	 * @throws Throwable
	 */
	public function log(array $message, array $option = [])
	{
		Retry::execute([
			'maxExecutionCount' => 3,
			'onExecute' => function () use (&$message, &$executionCount) {
				// Extract Basic Information
				$program = $message['program'];
				$pid = $message['pid'];
				$level = $message['level'];
				$timestamp = $message['timestamp'];
				$host = $message['host'];
				$color = $level === 'critical' ? '#ff0000' : '#36a64f';

				$channel = $this->config['channel'];
				$category = $this->config['category'];
				$userName = $this->config['userId'] ?? 'unknown';
				$logFolderPath = $this->config['logFolderPath'];
				$link = $this->config['link'] ?? 'www.slack.com';

				unset($message['program']);
				unset($message['pid']);
				unset($message['level']);
				unset($message['timestamp']);
				unset($message['host']);

				$headerInfoStringFormat = "> Program: *{$program}*\n>Host: *{$host}*\n>Pid: *{$pid}*\n> Level: *`{$level}`*\n> Timestamp: *{$timestamp}*";
				$mentionInfoStringFormat = "> Mention:<@{$userName}> / <{$link}|This is link>";

				if ($level !== 'critical') {
					// Notification
					$token = $this->config['webHookToken'] ?? null;
					$url = "https://hooks.slack.com/services/";
					$url .= $token;

					$httpHeader = [
						'Content-type: Application/json'
					];

					$ConvertedMessageOfArrayFormatToString = json_encode($message, JSON_PRETTY_PRINT);
					$postFields = json_encode(
						[
							'channels' => $channel,
							'blocks' => [
								$this->createSectionBlock($headerInfoStringFormat),
								$this->createSectionBlock($mentionInfoStringFormat)
							],
							'attachments' => [
								$this->createAttachmentsBlock($color, $ConvertedMessageOfArrayFormatToString)
							]
						]
					);
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

					switch ($statusCode) // https://api.slack.com/changelog/2016-05-17-changes-to-errors-for-incoming-webhooks
					{
						case 400:
							throw new Exception("FAILURE: " . json_encode([
									'message' => 'Invalid_payload or user_not_found',
									'info' => $info,
									'url' => $url,
									'httpHeader' => $httpHeader,
									'postFields' => $postFields,
									'status' => $statusCode,
									'output' => $output,
								])
							);
						case 403:
							throw new Exception("FAILURE: " . json_encode([
									'message' => 'Action_prohibited',
									'info' => $info,
									'url' => $url,
									'httpHeader' => $httpHeader,
									'postFields' => $postFields,
									'status' => $statusCode,
									'output' => $output,
								])
							);
						case 404:
							throw new Exception("FAILURE: " . json_encode([
									'message' => 'Channel_not_found',
									'info' => $info,
									'url' => $url,
									'httpHeader' => $httpHeader,
									'postFields' => $postFields,
									'status' => $statusCode,
									'output' => $output,
								])
							);
						case 410:
							throw new Exception("FAILURE: " . json_encode([
									'message' => 'Channel_is_archived',
									'info' => $info,
									'url' => $url,
									'httpHeader' => $httpHeader,
									'postFields' => $postFields,
									'status' => $statusCode,
									'output' => $output,
								])
							);
						case 500:
							throw new Exception("FAILURE: " . json_encode([
									'message' => 'rollup_error',
									'info' => $info,
									'url' => $url,
									'httpHeader' => $httpHeader,
									'postFields' => $postFields,
									'status' => $statusCode,
									'output' => $output,
								])
							);
					}
				}
				else {
					// upload log file
					$token = $this->config['uploadToken'] ?? null;
					$url = "https://slack.com/api/files.upload";
					$logFileNames = glob("{$logFolderPath}/{$category}/*");
					$currentLogFilePath = end($logFileNames);
					$file = new \CURLFile("{$currentLogFilePath}");

					$httpHeader = [
						'Content-Type: multipart/form-data',
						"Authorization: Bearer {$token}"
					];

					$postFields = [
						'channels' => $channel,
						'file' => $file,
						'initial_comment' => $headerInfoStringFormat . "\n" . $mentionInfoStringFormat
					];

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
					$statusMessage = $output['error'] ?? null;

					if ($statusMessage !== null) // https://api.slack.com/methods/files.upload#errors
					{
						throw new Exception("FAILURE: " . json_encode([
								'message' => "slack upload failed",
								'info' => $info,
								'url' => $url,
								'httpHeader' => $httpHeader,
								'postFields' => $postFields,
								'status' => $statusMessage,
								'output' => $output
							])
						);
					}
				}
			}
		]);
	}

	public function createSectionBlock($text, $textType = null) : array
	{
		return [
			'type' => 'section',
			'text' => [
				'text' => $text,
				'type' => $textType ?? "mrkdwn"
			]
		];
	}

	public function createAttachmentsBlock($color, $message): array
	{
		return [
			"mrkdwn_in" => ["text"],
			"color" => $color,
			"fields" => [
				[
					"value" => $message, "short" => false
				]
			],
			"footer" => "footer"
		];
	}
}