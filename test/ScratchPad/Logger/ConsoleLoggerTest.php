<?php

namespace ScratchPad\Logger;

use PHPUnit\Framework\TestCase;

class ConsoleLoggerTest extends TestCase
{
	public function testLog()
	{
		$logger = new ConsoleLogger(['appendNewLine' => 3]);
		$logger->log(['type' => 'test1', 'key' => 'value'], ['format'=>'pretty', 'appendNewLine'=>1]);
		$logger->log(['type' => 'test2']);
		$logger->log(['type' => 'test2']);
		$this->assertTrue(true);
	}

	public function testFormat()
	{
		$logger = new ConsoleLogger([
			'format' => 'pretty'
		]);
		$logger->log([
			'type' => 'test1',
			'key1' => 'value1'
		], [
			'format' => 'compact'
		]);
		$logger->log([
			'type' => 'test1',
			'key1' => 'value1'
		]);
		$this->assertTrue(true);
	}

	public function testLogger()
	{
		Logger::setLogger(new ConsoleLogger());
		Logger::info(['type' => 'test1', 'key' => 'value'], ['format'=>'pretty', 'appendNewLine'=>1]);
		Logger::info(['type' => 'test2', 'key' => 'value'], ['format'=>'pretty', 'appendNewLine'=>1]);
	}
}
