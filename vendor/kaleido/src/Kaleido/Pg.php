<?php
namespace Kaleido;

use Kaleido\Payments\Inicis\InicisHandler;
use Kaleido\Payments\Inicis\InicisInterface;

use Kaleido\Payments\Lgcns\LgcnsHandler;
use Kaleido\Payments\Lgcns\LgcnsInterface;

class Pg 
{
	protected $inicisHandler;
	protected $client;

	protected $lgcnsHandler;
	protected $gettrait;

	public function __construct(array $config = [])
	{ 
		//print_r($config);
		//inicis web
		$this->setInicisHeandler(new InicisHandler($config));
		//inicis http obj... 
		$this->client = new HttpClient(null,null,null);
		
		//lgcns		
		$this->setLgcnsHeandler(new LgcnsHandler($config));		
	}
	
	//inicis std
	function getInicisHeandler()
	{
		return $this->inicisHandler;
	}

	private	function setInicisHeandler(InicisInterface $inicisHandler)
	{
		$this->inicisHandler = $inicisHandler;
	}
	
	public function getClient()
    {
        return $this->client;
    }	
	
	//kakao pay
	function getLgcnsHeandler()
	{
		return $this->lgcnsHandler;
	}

	private	function setLgcnsHeandler(LgcnsInterface $lgcnsHandler)
	{
		$this->lgcnsHandler = $lgcnsHandler;
	}
}