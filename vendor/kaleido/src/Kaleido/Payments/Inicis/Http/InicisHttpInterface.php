<?php

namespace Kaleido\Payments\Inicis\Http;

interface InicisHttpInterface
{
	public function processHTTP($url, $param);
	public function getErrorCode();
	public function getErrorMsg();
	public function getBody();	
}
