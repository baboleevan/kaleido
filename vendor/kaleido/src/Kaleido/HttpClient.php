<?php
namespace Kaleido;

use Kaleido\Payments\Inicis\Http\InicisHttpClient;
use Kaleido\Payments\Inicis\Http\InicisHttpInterface;

class HttpClient
{
    protected $httpClientHandler;	
	protected $url;
	protected $param;
    public static $requestCount = 0;
  
    public function __construct($url=null, $param=null)
    {
		$this->setHttpClientHandler(new InicisHttpClient($url, $param));		
    }
    
    public function setHttpClientHandler(InicisHttpInterface $httpClientHandler)
    {
        $this->httpClientHandler = $httpClientHandler;
    }
    
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }    
}