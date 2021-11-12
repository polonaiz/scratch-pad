<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class SlackNotifyLoggerTest extends TestCase
{
	public function testNoticeLog()
	{
		$logger = new SlackNotifyLogger([
			'channel' => 'Slack_test',
			'username' => 'U02DLMVRYPP',
			'webHookToken' => 'T02E2B4RYN5/B02M5KA0V36/Rwv4xO5wuuCkrUTmPgdZDAeE',
			'uploadToken' => 'xoxb-2478378882753-2695000162598-1q1e7H1FmPIxQ3RaBo4RVYII',
			'category' => 'Slack_test',
			'logFolderPath' => 'Assets',
			'link' => 'www.slack.com'
		]);
		$logger->notice([
			"timestamp" => "2021-11-03 09:18:32.984287 KST",
			"host" => "pizzaseol",
			"program" => "Slack_test",
			"pid" => 1,
			"level" => "notice",
			"type" => "executionEnd"
		]);

		$this->assertTrue(true);
	}

	public function testFileUpload()
	{
		$fileName = 'Assets/Slack_test/Slack_test_log';
		$file = new \CURLFile($fileName);
		$token = 'xoxb-2478378882753-2695000162598-1q1e7H1FmPIxQ3RaBo4RVYII';
		$url = "https://slack.com/api/files.upload";
		$httpHeader = [
			'Content-Type: multipart/form-data',
			"Authorization: Bearer {$token}"
		];

		$connectTimeout = 5;
		$executeTimeout = 10;
		$curl = curl_init();

		$postFields = [
			'channels' => 'U02DLMVRYPP',
			'file' => $file,
			'initial_comment' => $fileName
		];

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

		if ($statusCode !== 200) {
			$this->fail();
		}
		$this->assertTrue(true);
	}

	public function testCriticalLog()
	{
		$logger = new SlackNotifyLogger([
			'channel' => 'Slack_test',
			'username' => 'U02DLMVRYPP',
			'webHookToken' => 'T02E2B4RYN5/B02M5KA0V36/Rwv4xO5wuuCkrUTmPgdZDAeE',
			'uploadToken' => 'xoxb-2478378882753-2695000162598-1q1e7H1FmPIxQ3RaBo4RVYII',
			'category' => 'Slack_test',
			'logFolderPath' => 'Assets',
			'link' => 'www.slack.com'
		]);

		$logger->critical([
			"timestamp" => "2021-11-03 09:12:32.984287 KST",
			"host" => "pizzaseol",
			"program" => "Slack_test",
			"pid" => 1,
			"level" => "critical",
		]);

		$this->assertTrue(true);
	}
}