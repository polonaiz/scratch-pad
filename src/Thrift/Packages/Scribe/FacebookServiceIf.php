<?php

namespace Scribe;

interface FacebookServiceIf {
	public function getName();
	public function getVersion();
	public function getStatus();
	public function getStatusDetails();
	public function getCounters();
	public function getCounter($key);
	public function setOption($key, $value);
	public function getOption($key);
	public function getOptions();
	public function getCpuProfile($profileDurationInSec);
	public function aliveSince();
	public function reinitialize();
	public function shutdown();
}
