<?php

namespace Kaleido\Payments\Lgcns;

interface LgcnsInterface
{
	//public function test();
	public function CnsActionUrl($url);
	public function CnsPayVersion($ver);
	public function CnsPayWebConnector();
	public function setRequestData($request);
	public function addRequestData($key, $value);
	public function getResultData($key);	
	public function requestAction();
	public function writeLog($strLogText);
	public function makeDateString($sDate);
	public function makeHashInputString($salt);
	
	//public function getKmpayFunc($data);
	
}
