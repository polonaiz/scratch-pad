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
			'onExecute' => function() use (&$message, &$executionCount) {
				// Extract Basic Information
				$program = $message['program'];
				$pid = $message['pid'];
				$level = $message['level'];
				$color = $level === 'critical' ? '#ff0000' : '#36a64f';
				$timestamp = $message['timestamp'];
				$host = $message['host'];
				$channel = $this->config['channel'];
				$category = $this->config['category'];
				$userName = $this->config['username'] ?? 'unknown';

				unset($message['program']);
				unset($message['pid']);
				unset($message['level']);
				unset($message['timestamp']);
				unset($message['host']);

				if ($level !== 'critical') {
					$token = $this->config['webHookToken'] ?? null;
					$url = "https://hooks.slack.com/services/";
					$url .= $token;
					$httpHeader = [
						'Content-type: Application/json'
					];

					// Create Slack Message Block
					$data["channels"] = $channel;
					$data["username"] = "SlackNotifyLogger";
					$data["blocks"] = [
						$this->getHeaderBlock($program, $host, $pid, $level, $timestamp),
						$this->getMentionBlock($userName),
						["type" => "divider"]
					];
					$data["attachments"] = [$this->getAttachmentBlock($color, $message, $level)];

					$postFields = json_encode($data);
				}
				else {
					$filePath = "/data/log/scribe/default_primary/{$category}";
					$logFileNames = glob("{$filePath}/*");
					$currentLogFilePath = end($logFileNames);

					$file = new \CURLFile("{$currentLogFilePath}");
					$token = $this->config['uploadToken'];
					$url = "https://slack.com/api/files.upload";
					$httpHeader = [
						'Content-Type: multipart/form-data',
						"Authorization: Bearer {$token}"
					];
					$postFields = [
						'channels' => $channel,
						'file' => $file,
						'initial_comment' => "> Program: *{$program}*\n>Host: *{$host}*\n>Pid: *{$pid}*\n> Level: *`{$level}`*\n> Timestamp: *{$timestamp}*\n> Mention:<@{$userName}>		<https://jira.gamevilcom2us.com/jira/secure/RapidBoard.jspa?rapidView=567|This is jira link>"
					];
				}

				$this->request($url, $httpHeader, $postFields);
			}
		]);
	}

	public function getHeaderBlock($program, $host, $pid, $level, $timestamp): array
	{
		return [
			"type" => "section",
			"text" => [
				"type" => "mrkdwn",
				"text" => "> Program: *{$program}*\n>Host: *{$host}*\n>Pid: *{$pid}*\n> Level: *`{$level}`*\n> Timestamp: *{$timestamp}*"
			]
		];
	}

	public function getMentionBlock($userName): array
	{

		return [
			"type" => "section",
			"text" => [
				"type" => "mrkdwn",
				"text" => "> Mention:<@{$userName}>		<https://jira.gamevilcom2us.com/jira/secure/RapidBoard.jspa?rapidView=567|This is jira link>"
			]
		];
	}

	public function getAttachmentBlock($color, $message, $level): array
	{
		return [
			"mrkdwn_in" => ["text"],
			"color" => $color,
			"fields" => [
				[
					"title" => "Log details", "value" => json_encode($message, JSON_PRETTY_PRINT), "short" => false
				]
			],
			"footer" => "com2us BI tech team"
		];
	}

	/**
	 * @throws Exception
	 */
	public function request($url, $httpHeader, $postFields)
	{
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
					]));
			case 403:
				throw new Exception("FAILURE: " . json_encode([
						'message' => 'Action_prohibited',
						'info' => $info,
						'url' => $url,
						'httpHeader' => $httpHeader,
						'postFields' => $postFields,
						'status' => $statusCode,
						'output' => $output,
					]));
			case 404:
				throw new Exception("FAILURE: " . json_encode([
						'message' => 'Channel_not_found',
						'info' => $info,
						'url' => $url,
						'httpHeader' => $httpHeader,
						'postFields' => $postFields,
						'status' => $statusCode,
						'output' => $output,
					]));
			case 410:
				throw new Exception("FAILURE: " . json_encode([
						'message' => 'Channel_is_archived',
						'info' => $info,
						'url' => $url,
						'httpHeader' => $httpHeader,
						'postFields' => $postFields,
						'status' => $statusCode,
						'output' => $output,
					]));
			case 500:
				throw new Exception("FAILURE: " . json_encode([
						'message' => 'rollup_error',
						'info' => $info,
						'url' => $url,
						'httpHeader' => $httpHeader,
						'postFields' => $postFields,
						'status' => $statusCode,
						'output' => $output,
					]));
		}
	}
}