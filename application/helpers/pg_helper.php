<? if (!defined('BASEPATH')) exit('No direct script access allowed');

function kakaopay_init(){
	$data = array();
	$data['MID'] = "cnstest25m";			//상점아이디 - 해당값을 변경해서 사용하세요
	$data['merchantEncKey'] = "10a3189211e1dfc6";
	$data['merchantHashKey'] = "10a3189211e1dfc6";
	$data['msgName'] = "/merchant/requestDealApprove.dev";
	$data['CNSPAY_WEB_SERVER_URL'] = "https://kmpay.lgcns.com:8443";
	$data['CnsPayDealRequestUrl'] = "https://pg.cnspay.co.kr:443";
	$data['cancelPwd'] = "123456";			//취소 패스워드 - 해당값을 변경해서 사용하세요
	$data['LogDir'] = "D:/xampp2/htdocs/kakao/log";				//로그 위치 - 해당값을 변경해서 사용하세요
	$data['LogPath'] = "D:/xampp2/htdocs/kakao/log/";			//로그 경로 - 해당값을 변경해서 사용하세요
	$data['phpVersion'] = "PLP-0.1.1.3";
	$data['merchantKey'] = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";	//상점 키 - 해당값을 변경해서 사용하세요
	return $data;
}

function inicis_init(){
	$data = array();
	$data['signKey'] = "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; //상점 키 - 해당값을 변경해서 사용하세요
	$data['mid'] = "INIpayTest";	//상점아이디 - 해당값을 변경해서 사용하세요
	$data['alg'] = 'sha256';		//알고리즘 타입 
	return $data;
}