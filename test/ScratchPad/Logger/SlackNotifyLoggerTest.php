<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class SlackNotifyLoggerTest extends TestCase
{
	public function testNoticeLog()
	{
		$logger = new SlackNotifyLogger([
			'channel' => 'appsflyer-datalocker-collector-log',
			'username' => 'U02DLMVRYPP',
			'webHookToken' => 'T02E2B4RYN5/B02MSPM2T9N/Ss9EWRDg959tC3bvs2YjGI7v',
			'uploadToken' => 'xoxb-2478378882753-2695000162598-ypLg7a598UuThVGQzZvlDXtu',
			'category' => 'appsflyer-datalocker-collector-log'
		]);
		$logger->notice([
			"timestamp" => "2021-11-03 09:18:32.984287 KST",
			"host" => "bied-batch-01.com2us.kr",
			"program" => "collector-google-play-console",
			"pid" => 30630,
			"level" => "notice",
			"type" => "executionEnd",
			"executionTimeSec" => 4651,
			'test' => 'good',
			'name' => 'pizzaseol',
			'message' => "Do you know what he wants?"
		]);

		$this->assertTrue(true);
	}

	public function testFileUpload()
	{
		$fileName = 'Assets/appsflyer-datalocker-collector-log-2021-11-10_00002';
		$file = new \CURLFile($fileName);
		$token = 'xoxb-2478378882753-2695000162598-ypLg7a598UuThVGQzZvlDXtu';
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
			'channel' => 'appsflyer-datalocker-collector-log',
			'username' => 'U02DLMVRYPP',
			'webHookToken' => 'T02E2B4RYN5/B02MSPM2T9N/Ss9EWRDg959tC3bvs2YjGI7v',
			'uploadToken' => 'xoxb-2478378882753-2695000162598-ypLg7a598UuThVGQzZvlDXtu',
			'category' => 'appsflyer-datalocker-collector-log'
		]);

		$logger->critical([
			"timestamp" => "2021-11-03 09:18:32.984287 KST",
			"host" => "bied-batch-01.com2us.kr",
			"program" => "collector-google-play-console",
			"pid" => 30630,
			"level" => "notice"
		]);

		$this->assertTrue(true);
	}
}