<?php

namespace Kaleido\Payments\Inicis;

interface InicisInterface
{
	public function getForm();
	public function	getTimestamp();
	public function makeSignature($signParam);	
	public function	makeHash();
}
