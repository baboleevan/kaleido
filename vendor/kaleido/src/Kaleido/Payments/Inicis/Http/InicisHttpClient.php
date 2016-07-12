<?php
namespace Kaleido\Payments\Inicis\Http;

//$P_REQ_URL = $this->input->get_post('P_REQ_URL', TRUE);
//$explode_data = explode('/', $P_REQ_URL); 
//$host = $explode_data[2]; 
//$path = "/" . $explode_data[3] . "/" . $explode_data[4];

class InicisHttpClient implements InicisHttpInterface
{
	
	function __construct()
    {
        // a factory constructor should never be invoked
    }
	
	protected $sock=0;
  	protected $ssl;
  	protected $host;
  	protected $port;
  	protected $path;
  	protected $status;
  	protected $headers="";
  	protected $body="";
  	protected $reqeust;
  	protected $errorcode;
  	protected $errormsg;
	
	protected $connect_timeout = 5;
	protected $read_timeout = 15;

	public function processHTTP($url, $param) {
		
		$data = "";
		foreach ($param as $key => $value) {
			$key2 = urlencode($key);
			$value2 = urlencode($value);
			$data .= "&$key2=$value2";
		}
		
		$data = substr($data, 1); // remove leading "&"
		$url_data = parse_url($url);
		
				
		if ($url_data["scheme"]=="https")
		{
			$this->ssl = "ssl://";
			$this->port = 443;
		}
		
		$this->host = $url_data["host"];
		/*
		
				if (is_null($url_data["port"])) {
					$this->port = "80";
				} else {
					$this->port = $url_data["port"];
				}
				*/
		
		$this->path = $url_data["path"];
		
		if (!$this->sock = @fsockopen($this->ssl.$this->host, $this->port, $errno, $errstr, $this->connect_timeout)) {
			
      		switch($errno) {
      			case -3:
        			$this->errormsg = 'Socket creation failed (-3)';
        		case -4:
          			$this->errormsg = 'DNS lookup failure (-4)';
        		case -5:
          			$this->errormsg = 'Connection refused or timed out (-5)';
        		default:
          			$this->errormsg = 'Connection failed ('.$errno.')';
          			$this->errormsg .= ' '.$errstr;
      		}
			return false;
    	}
				
    	$this->headers="";
   		$this->body="";

		/*Write*/
		$request  = "POST ".$this->path." HTTP/1.0\r\n";
		$request .= "Connection: close\r\n";
		$request .= "Host: ".$this->host."\r\n";
		$request .= "Content-type: application/x-www-form-urlencoded\r\n";
		$request .= "Content-length: ".strlen($data)."\r\n";
		$request .= "Accept: */*\r\n";
		$request .= "\r\n";
		$request .= $data."\r\n";
		$request .= "\r\n";
		fwrite($this->sock, $request);
		
		/*Read*/
		stream_set_blocking($this->sock, FALSE ); 
		$atStart = true;
		$IsHeader = true;
		$timeout = false;
		$start_time= time();
		while ( !feof($this->sock) && !$timeout ) {
			$line = fgets($this->sock, 4096);
			$diff=time()-$start_time;
			if( $diff >= $this->read_timeout){
				$timeout = true;
			}
			if( $IsHeader ) {
				if( $line == "" ) {
					continue;
				}
				if( substr( $line, 0, 2 ) == "\r\n" ) {
					$IsHeader = false;
					continue;
				}
  				$this->headers .= $line;
            	if ($atStart) {
                	$atStart = false;
                	if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {
                    	$this->errormsg = "Status code line invalid: ".htmlentities($line).$m[1].$m[2].$m[3];
						fclose( $this->sock );
                    	return false;
                	}
                	$http_version = $m[1];
                	$this->status = $m[2];
                	$status_string = $m[3];
                	continue;
				}
            }
			else {
 				$this->body .= $line;
			}
		}
		
		
		fclose( $this->sock );

		if( $timeout )
		{
			$this->errorcode = READ_TIMEOUT_ERR;
            $this->errormsg = "Socket Timeout(".$diff."SEC)";
			return false;
		}
		
		return true;
		//	return false;
	}

	public function getErrorCode()
	{
		return $this->errorcode;
	}
	
	public function getErrorMsg()
	{
		return $this->errormsg;
	}

	public function getBody() 
	{
        return $this->body;
    }

}