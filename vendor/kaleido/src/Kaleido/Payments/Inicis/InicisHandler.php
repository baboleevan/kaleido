<?php

namespace Kaleido\Payments\Inicis;




class InicisHandler implements InicisInterface
{
	
	protected $signKey;
	protected $alg;
	//protected $oid;
	protected $price;

	
	public function __construct($config)
    {
		//print_r($config);
		$this->signKey = isset($config["signKey"])? $config["signKey"]: "";
		$this->alg = (isset($config["alg"]))? $config["alg"] : "sha256";

		//$this->oid = (isset($config["oid"]))? $config["oid"] : "";
		$this->price = (isset($config["price"]))? $config["price"] : 1000;

	}

	
	//--inicis std ----------------------

	/*
		// timezone 을 설정하지 않으면 getTimestapme() 실행시 오류가 발생한다.
		// php.ini 에 timezone 설정이 되어 잇으면 아래 코드가 필요없다. 
		// php 5.3 timezone을 기본 설정 -- 밑에 코드는 에러방지용
	*/
	public function getTimestamp()	
	{
		date_default_timezone_set('Asia/Seoul');
		
		$milliseconds = round(microtime(true) * 1000);	
		$tempValue1 = round($milliseconds/1000);		//max integer 자릿수가 9이므로 뒤 3자리를 뺀다
		$tempValue2 = round(microtime(false) * 1000);	//뒤 3자리를 저장
		switch (strlen($tempValue2)) {
			case '3':
				break;
			case '2':
				$tempValue2 = "0".$tempValue2;
				break;
			case '1':
				$tempValue2 = "00".$tempValue2;
				break;
			default:
				$tempValue2 = "000";
				break;
		}
		
		return "".$tempValue1.$tempValue2;
	}

	/*
	*** 위변조 방지체크를 signature 생성 ***

	mid, price, timestamp 3개의 키와 값을	
	key=value 형식으로 하여 '&'로 연결한 하여 SHA-256 Hash로 생성 된값	
	ex) mid=INIpayTest&price=819000&timestamp=2012-02-01 09:19:04.004

	* key기준 알파벳 정렬
	* timestamp는 반드시 signature생성에 사용한 timestamp 값을 timestamp input에 그데로 사용하여야함
	*/	
	public function makeSignature($signParam = null) 
	{	
		ksort($signParam);
		$string = "";
		foreach ($signParam as $key => $value) {
			$string .= "&$key=$value";
		}		
		$string = substr($string, 1); // remove leading "&"			
		$sign = $this->makeHash($string,  $this->alg);
		return $sign;
	}
		
	public function makeHash($string = null, $alg =null) 
	{
		if(!$string) $string = $this->signKey; 
		if(!$alg) $alg = $this->alg;
		$ret = openssl_digest($string, $alg);
		return $ret;
	}
	
    /**
     * @inheritdoc
     */
    public function getForm()
    {
        return $this->getHttpScheme() . '://' . $this->getHostName() . $this->getServerVar('REQUEST_URI');
    }

    /**
     * Get the currently active URL scheme.
     *
     * @return string
     */
    protected function getHttpScheme()
    {
        return $this->isBehindSsl() ? 'https' : 'http';
    }

    /**
     * Tries to detect if the server is running behind an SSL.
     *
     * @return boolean
     */
    protected function isBehindSsl()
    {
        // Check for proxy first
        $protocol = $this->getHeader('X_FORWARDED_PROTO');
        if ($protocol) {
            return $this->protocolWithActiveSsl($protocol);
        }

        $protocol = $this->getServerVar('HTTPS');
        if ($protocol) {
            return $this->protocolWithActiveSsl($protocol);
        }

        return (string)$this->getServerVar('SERVER_PORT') === '443';
    }

    /**
     * Detects an active SSL protocol value.
     *
     * @param string $protocol
     *
     * @return boolean
     */
    protected function protocolWithActiveSsl($protocol)
    {
        $protocol = strtolower((string)$protocol);

        return in_array($protocol, ['on', '1', 'https', 'ssl'], true);
    }

    /**
     * Tries to detect the host name of the server.
     *
     * Some elements adapted from
     *
     * @see https://github.com/symfony/HttpFoundation/blob/master/Request.php
     *
     * @return string
     */
    protected function getHostName()
    {
        // Check for proxy first
        if ($header = $this->getHeader('X_FORWARDED_HOST') && $this->isValidForwardedHost($header)) {
            $elements = explode(',', $header);
            $host = $elements[count($elements) - 1];
        } elseif (!$host = $this->getHeader('HOST')) {
            if (!$host = $this->getServerVar('SERVER_NAME')) {
                $host = $this->getServerVar('SERVER_ADDR');
            }
        }

        // trim and remove port number from host
        // host is lowercase as per RFC 952/2181
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        // Port number
        $scheme = $this->getHttpScheme();
        $port = $this->getCurrentPort();
        $appendPort = ':' . $port;

        // Don't append port number if a normal port.
        if (($scheme == 'http' && $port == '80') || ($scheme == 'https' && $port == '443')) {
            $appendPort = '';
        }

        return $host . $appendPort;
    }

    protected function getCurrentPort()
    {
        // Check for proxy first
        $port = $this->getHeader('X_FORWARDED_PORT');
        if ($port) {
            return (string)$port;
        }

        $protocol = (string)$this->getHeader('X_FORWARDED_PROTO');
        if ($protocol === 'https') {
            return '443';
        }

        return (string)$this->getServerVar('SERVER_PORT');
    }

    /**
     * Returns the a value from the $_SERVER super global.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getServerVar($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }

    /**
     * Gets a value from the HTTP request headers.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getHeader($key)
    {
        return $this->getServerVar('HTTP_' . $key);
    }

    /**
     * Checks if the value in X_FORWARDED_HOST is a valid hostname
     * Could prevent unintended redirections
     *
     * @param string $header
     *
     * @return boolean
     */
    protected function isValidForwardedHost($header)
    {
        $elements = explode(',', $header);
        $host = $elements[count($elements) - 1];
        
        return preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host) //valid chars check
            && 0 < strlen($host) && strlen($host) < 254 //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host); //length of each label
    }
}
