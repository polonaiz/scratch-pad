<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class SlackNotifyLoggerTest extends TestCase
{
	public function testNoticeLog()
	{
		// Please write down 3 information
		$userId = 'Your member Id';
		$slackWebHookToken = 'Your Web Hook Token';
		$slackOAuthToken = 'Your OAuth Token';

		$channel ='Slack_test';
		$category = 'Slack_test';
		$logFolderPath = 'Assets';
		$link = null;

		$logger = new SlackNotifyLogger([
			'channel' => $channel,
			'userId' => $userId,
			'webHookToken' => $slackWebHookToken,
			'uploadToken' => $slackOAuthToken,
			'category' => $category,
			'logFolderPath' => $logFolderPath,
			'link' => $link
		]);
		$logger->notice([
			"timestamp" => "2021-01-01 00:00:01.000000 KST",
			"host" => "host",
			"program" => "program",
			"pid" => 1,
			"level" => "notice",
			"type" => "executionEnd"
		]);

		$this->assertTrue(true);
	}

	public function testFileUpload()
	{
		//  Please write down 2 information
		$userId = 'Your member Id';
		$slackOAuthToken = 'Your OAuth Token';

		$fileName = 'Assets/Slack_test/Slack_test_log';
		$file = new \CURLFile($fileName);
		$url = "https://slack.com/api/files.upload";
		$httpHeader = [
			'Content-Type: multipart/form-data',
			"Authorization: Bearer {$slackOAuthToken}"
		];

		$connectTimeout = 5;
		$executeTimeout = 10;
		$curl = curl_init();

		$postFields = [
			'channels' => $userId,
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
		// Please write down 3 information
		$userId = 'Your member Id';
		$slackWebHookToken = 'Your Web Hook Token';
		$slackOAuthToken = 'Your OAuth Token';

		$channel ='Slack_test';
		$category = 'Slack_test';
		$logFolderPath = 'Assets';
		$link = null;

		$logger = new SlackNotifyLogger([
			'channel' => $channel,
			'userId' => $userId,
			'webHookToken' => $slackWebHookToken,
			'uploadToken' => $slackOAuthToken,
			'category' => $category,
			'logFolderPath' => $logFolderPath,
			'link' => $link
		]);

		$logger->critical([
			"timestamp" => "2021-01-01 00:00:01.000000 KST",
			"host" => "host",
			"program" => "program",
			"pid" => 1,
			"level" => "critical",
		]);

		$this->assertTrue(true);
	}
}