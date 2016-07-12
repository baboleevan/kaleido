<? if (!defined('BASEPATH')) 	exit('No direct script access allowed');

require realpath(FCPATH) . '/vendor/kaleido/src/Kaleido/autoload.php';

class Page extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this -> load -> model('common_model');		
		$this -> load -> helper('form');
		$this -> load -> helper('url');
		$this -> load -> library('pagination_custom_v3'); //ci 3 version
		$this -> load -> helper('pg');
	}

	public function _remap($method) {
		$this->segs = $this->uri->segment_array();
		if ($this -> input -> is_ajax_request()) {
			if (method_exists($this, $method)) {
				$this -> {"{$method}"}();
			}
		} else {
			//ajax가 아니면
			if (method_exists($this, $method)) {
				//원활한 테스트를 위해서 일단 헤더 푸터 주석 처리..
				//$this->load->view("/kaleido/common/header"); 
				//$this->load->view("/kaleido/common/aside_left");
				$this -> {"{$method}"}();
				//$this->load->view("/kaleido/common/footer");
			} else {
				$this->err_404();
			}
			//$this->output->enable_profiler(true);
		}
	}

	function index(){}//end index
	function err_404(){$this->load->view('/kaleido/common/not_found');}//404

	
	//결제 폼
	function order_form(){
		$data = array();
		
		//디폴트 이메일... 실제 적용시에는 공백으로 사용
		$data['mail_id'] = "you_mail_id";		//이메일 아이디
		$data['mail_domain'] = "hanmail.net";	//이메일 도메인
		
		//로그인 여부와 관련되어서 변경되는 파라메터들
		$data['readonly'] = "";
		$data['disabled'] = "";
		$data['display'] = "";
		$data['is_login'] = "F";
		
		//로그인 체크 여부
		if ($this -> session -> userdata('isLogin') != '') {
			$mail = explode('@', $this -> session -> userdata['email']);
			$data['mail_id'] = $mail[0];
			$data['mail_domain'] = $mail[1];
			$data['readonly'] = "readonly";
			$data['disabled'] = "disabled";
			$data['display'] = "display: none";
			$data['is_login'] = "T";
		}

		$data['use_point'] = 0;
		$data['mid'] ="";
		$data['oid'] = "";
		$data['timestamp'] ="";
		$data['total_price'] ="";
		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/order/form_v",$data);
		$this->load->view("/kaleido/common/footer");
	}
	
	//ajax temp order create
	function ajax_inicis(){
		$input = array();
		$data = inicis_init(); //이니시스 컨피그 값 -- helper
		foreach($this->input->post(NULL, TRUE) as $key => $val) $input["{$key}"]  = $val;
		$data['input'] = $input;

		$data['oid']  = "";
		$data['price'] = $input["price"];

		$pg = new Kaleido\Pg();
		$inicis = $pg->getInicisHeandler($data);

		$data['timestamp'] = $inicis->getTimestamp();
		$data['oid']=$data['mid']."_".$data['timestamp'];
		$data['mkey'] = $inicis->makeHash($data['signKey'], $data['alg']);
		
		$params = array(
				"oid" => 	$data['oid'],
				"price" => 	$data['price'],
				"timestamp" => $data['timestamp']
		);
		
		$data['signature'] = $inicis->makeSignature($params);
		$data['sdf'] = date("YmdHis");
		$data['p_oid']=$data['mid']."_".$data['sdf'];		
		
		$data['inicis_returnUrl']	=	base_url()."kaleido/page/inicis_return";
		$data['inicis_popupUrl']	=	base_url()."kaleido/page/inicis_popup";
		$data['inicis_closeUrl']	=	base_url()."kaleido/page/inicis_close";
		$this->load->view("/kaleido/order/inicis_load_v",$data);		
	}
	
	//팝업 닫아주는 스크립트
	function inicis_pop(){
		echo '<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay_popup.js" charset="UTF-8"></script>';
	}
	
	//섀도우 스크린 닫아주는 스크립트
	function inicis_close(){
		echo '<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay_close.js" charset="UTF-8"></script>';
	}
	
	//이니시스 결제 처리
	function inicis_return(){
		$request = array(); //post 받아온 값들 // ci가 아닌경우 대체 $_REQUEST[] 으로 대체 
		foreach($this->input->post(NULL, TRUE) as $key => $val) $request["{$key}"]  = $val;		
		$data = inicis_init(); //이니시스 컨피그 값 -- helper
		
		//$signKey = "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; // 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지		
		//print_r($request); //궁금하면 볼것		
		
		$pg = new Kaleido\Pg();
		$inicis		=	$pg->getInicisHeandler(['oid' => $request['orderNumber'], 'signKey'=> $data['signKey'], 'alg'=>$data['alg']]); //std 
		//$http 		= 	$pg->getClient()->getHttpClientHandler(['oid' => $request['orderNumber'], 'signKey'=> $data['signKey'], 'alg'=>$data['alg']]); //http util
		
		 try {

            //#############################
            // 인증결과 파라미터 일괄 수신
            //#############################
            //		$var = $_REQUEST["data"];

            //#####################
            // 인증이 성공일 경우만
            //#####################
            if (strcmp("0000", $_REQUEST["resultCode"]) == 0) {

                echo "####인증성공/승인요청####";
                echo "<br/>";


                //############################################
                // 1.전문 필드 값 설정(***가맹점 개발수정***)
                //############################################

                $mid = $_REQUEST["mid"];     // 가맹점 ID 수신 받은 데이터로 설정

                

                $timestamp = $inicis->getTimestamp();   // util에 의해서 자동생성

                $charset = "UTF-8";        // 리턴형식[UTF-8,EUC-KR](가맹점 수정후 고정)

                $format = "JSON";        // 리턴형식[XML,JSON,NVP](가맹점 수정후 고정)
                // 추가적 noti가 필요한 경우(필수아님, 공백일 경우 미발송, 승인은 성공시, 실패시 모두 Noti발송됨) 미사용 
                //String notiUrl	= "";

                $authToken = $request["authToken"];   // 취소 요청 tid에 따라서 유동적(가맹점 수정후 고정)

                $authUrl = $request["authUrl"];    // 승인요청 API url(수신 받은 값으로 설정, 임의 세팅 금지)

                //$netCancel = $_REQUEST["netCancel"];   // 망취소 API url(수신 받은f값으로 설정, 임의 세팅 금지) //안줌...?

                ///$mKey = $util->makeHash(signKey, "sha256"); // 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)
                $mKey = hash($data['alg'], $data['signKey']); // -_- 그냥 hash 써도 되었던것.... 

                //#####################
                // 2.signature 생성
                //#####################
                $signParam["authToken"] = $authToken;  // 필수
                $signParam["timestamp"] = $timestamp;  // 필수
                // signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
                $signature = $inicis->makeSignature($signParam);


                //#####################
                // 3.API 요청 전문 생성
                //#####################
                $authMap["mid"] = $mid;   // 필수
                $authMap["authToken"] = $authToken; // 필수
                $authMap["signature"] = $signature; // 필수
                $authMap["timestamp"] = $timestamp; // 필수
                $authMap["charset"] = $charset;  // default=UTF-8
                $authMap["format"] = $format;  // default=XML
                
                try {

                    $httpUtil = new HttpClient();
					var_dump($httpUtil);

                    //#####################
                    // 4.API 통신 시작
                    //#####################

                    $authResultString = "";
                    if ($httpUtil->processHTTP($authUrl, $authMap)) {
                        $authResultString = $httpUtil->body;
                    } else {
                        echo "Http Connect Error\n";
                        echo $httpUtil->errormsg;

                        throw new Exception("Http Connect Error");
                    }

                    //############################################################
                    //5.API 통신결과 처리(***가맹점 개발수정***)
                    //############################################################
                    echo "## 승인 API 결과 ##";

                    $resultMap = json_decode($authResultString, true);
					
                    echo "<pre>";
                    echo "<table width='565' border='0' cellspacing='0' cellpadding='0'>";

                    if (strcmp("0000", $resultMap["resultCode"]) == 0) {
                        /*                         * ***************************************************************************
                         * 여기에 가맹점 내부 DB에 결제 결과를 반영하는 관련 프로그램 코드를 구현한다.  

                          [중요!] 승인내용에 이상이 없음을 확인한 뒤 가맹점 DB에 해당건이 정상처리 되었음을 반영함
                          처리중 에러 발생시 망취소를 한다.
                         * **************************************************************************** */
						
						/* db 데이터 넣으면 됩니다.  공통부분만 넣어봄.*/
						$insert_data['tid'] 		= $resultMap['tid'];
						$insert_data['payMethod'] 	= $resultMap['payMethod'];
						$insert_data['resultCode'] 	= $resultMap['resultCode'];
						$insert_data['resultMsg'] 	= $resultMap['resultMsg'];
						$insert_data['TotPrice'] 	= $resultMap['TotPrice'];
						$insert_data['MOID'] 		= $resultMap['MOID'];
						$insert_data['applDate'] 	= $resultMap['applDate'];
						$insert_data['applTime'] 	= $resultMap['applTime'];
						$insert_data['createDatetime'] 	= date('Y-m-d H:i:s');
						$insert_id = $this->common_model->insert("inicis_pay_result",$insert_data);//table, data[]
						if($insert_id) 
							alert('결제 성공!!',INICIS_URL."/inicis_order_list");
							//리턴 수신 값 로고 싶으면 alert 주석
						else{
							//alert('결제는 성공하였으나 DB 입력 실패...',INICIS_URL."/order_form");
						}

                        echo "<tr><th class='td01'><p>거래 성공 여부</p></th>";
                        echo "<td class='td02'><p>성공</p></td></tr>";
                    } else {
                        echo "<tr><th class='td01'><p>거래 성공 여부</p></th>";
                        echo "<td class='td02'><p>실패</p></td></tr>";
                    }

                    //공통 부분만
                    echo
                            "<tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>거래 번호</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["tid"] , $resultMap) ? $resultMap["tid"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>결제방법(지불수단)</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["payMethod"] , $resultMap) ? $resultMap["payMethod"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>결과 코드</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["resultCode"] , $resultMap) ? $resultMap["resultCode"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>결과 내용</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["resultMsg"] , $resultMap) ? $resultMap["resultMsg"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>결제완료금액</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["TotPrice"] , $resultMap) ? $resultMap["TotPrice"] : "null" ) . "원</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>주문 번호</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["MOID"] , $resultMap) ? $resultMap["MOID"] : "null" )  . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>승인날짜</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["applDate"] , $resultMap) ? $resultMap["applDate"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>승인시간</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["applTime"] , $resultMap) ? $resultMap["applTime"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>";

                    if (isset($resultMap["payMethod"]) && strcmp("VBank", $resultMap["payMethod"]) == 0) { //가상계좌
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>입금 계좌번호</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["VACT_Num"] , $resultMap) ? $resultMap["VACT_Num"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>입금 은행코드</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["VACT_BankCode"] , $resultMap) ? $resultMap["VACT_BankCode"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>입금 은행명</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["vactBankName"] , $resultMap) ? $resultMap["vactBankName"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>예금주 명</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["VACT_Name"] , $resultMap) ? $resultMap["VACT_Name"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>송금자 명</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["VACT_InputName"] , $resultMap) ? $resultMap["VACT_InputName"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>송금 일자</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["VACT_Date"] , $resultMap) ? $resultMap["VACT_Date"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>송금 시간</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["VACT_Time"] , $resultMap) ? $resultMap["VACT_Time"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("DirectBank", $resultMap["payMethod"]) == 0) { //실시간계좌이체
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>sjgdm
                            <tr><th class='td01'><p>은행코드</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["ACCT_BankCode"] , $resultMap) ? $resultMap["ACCT_BankCode"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>현금영수증 발급결과코드</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["CSHRResultCode"] , $resultMap) ? $resultMap["CSHR_ResultCode"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>현금영수증 발급구분코드</p> <font color=red><b>(0 - 소득공제용, 1 - 지출증빙용)</b></font></th>
                            <td class='td02'><p>" . @(in_array($resultMap["CSHR_Type"] , $resultMap) ? $resultMap["CSHR_Type"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("HPP", $resultMap["payMethod"]) == 0) { //휴대폰
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>통신사</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["HPP_Corp"] , $resultMap) ? $resultMap["HPP_Corp"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>결제장치</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["payDevice"] , $resultMap) ? $resultMap["payDevice"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>휴대폰번호</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["HPP_Num"] , $resultMap) ? $resultMap["HPP_Num"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("KWPY", $resultMap["payMethod"]) == 0) { //뱅크월렛 카카오
                        echo "<tr><th class='td01'><p>휴대폰번호</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["KWPY_CellPhone"] , $resultMap) ? $resultMap["KWPY_CellPhone"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>거래금액</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["KWPY_SalesAmount"] , $resultMap) ? $resultMap["KWPY_SalesAmount"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>공급가액</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["KWPY_Amount"] , $resultMap) ? $resultMap["KWPY_Amount"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>부가세</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["KWPY_Tax"] , $resultMap) ? $resultMap["KWPY_Tax"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>봉사료</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["KWPY_ServiceFee"] , $resultMap) ? $resultMap["KWPY_ServiceFee"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("Culture", $resultMap["payMethod"]) == 0) { //문화상품권
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>문화상품권승인일자</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["applDate"] , $resultMap) ? $resultMap["applDate"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>문화상품권 승인시간</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["applTime"] , $resultMap) ? $resultMap["applTime"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>
                            <tr><th class='td01'><p>문화상품권 승인번호</p></th>
                            <td class='td02'><p>" . @(in_array($resultMap["applNum"] , $resultMap) ? $resultMap["applNum"] : "null" ) . "</p></td></tr>
                            <tr><th class='line' colspan='2'><p></p></th></tr>";
					} else if (isset($resultMap["payMethod"]) && strcmp("DGCL", $resultMap["payMethod"]) == 0) { //게임문화상품권
                        //$sum = "0";
                        //$sum2 = "0";
                        //$sum3 = "0";
                        //$sum4 = "0";
                        //$sum5 = "0";
                        //$sum6 = "0";

                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>게임문화상품권승인금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GAMG_ApplPrice"] , $resultMap) ? $resultMap["GAMG_ApplPrice"] : "null" ) . "원</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>사용한 카드수</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GAMG_Cnt"] , $resultMap) ? $resultMap["GAMG_Cnt"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>사용한 카드번호</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GAMG_Num1"] , $resultMap) ? $resultMap["GAMG_Num1"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>카드잔액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GAMG_Price1"] , $resultMap) ? $resultMap["GAMG_Price1"] : "null" ) . "원</p></td></tr>";

                        if (!strcmp("", $resultMap["GAMG_Num2"]) == 0) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>사용한 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Num2"] , $resultMap) ? $resultMap["GAMG_Num2"] : "null" ) . "</p></td></tr>
								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>카드잔액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Price2"] , $resultMap) ? $resultMap["GAMG_Price2"] : "null" ) . "원</p></td></tr>";
                        }
                        if (!strcmp("", $resultMap["GAMG_Num3"]) == 0) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>사용한 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Num3"] , $resultMap) ? $resultMap["GAMG_Num3"] : "null" ) . "</p></td></tr>
								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>카드잔액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Price3"] , $resultMap) ? $resultMap["GAMG_Price3"] : "null" ) . "원</p></td></tr>";
                        }
                        if (!strcmp("", $resultMap["GAMG_Num4"]) == 0) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>사용한 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Num4"] , $resultMap) ? $resultMap["GAMG_Num4"] : "null" ) . "</p></td></tr>
								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>카드잔액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Price4"] , $resultMap) ? $resultMap["GAMG_Price4"] : "null" ) . "원</p></td></tr>";
                        }
                        if (!strcmp("", $resultMap["GAMG_Num5"]) == 0) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>사용한 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Num5"] , $resultMap) ? $resultMap["GAMG_Num5"] : "null" ) . "</p></td></tr>
								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>카드잔액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GAMG_Price5"] , $resultMap) ? $resultMap["GAMG_Price5"] : "null" ) . "원</p></td></tr>";
                        }
                        if (!strcmp("", $resultMap["GAMG_Num6"]) == 0) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
                        <tr><th class='td01'><p>사용한 카드번호</p></th>
                        <td class='td02'><p>" . @(in_array($resultMap["GAMG_Num6"] , $resultMap) ? $resultMap["GAMG_Num6"] : "null" ) . "</p></td></tr>
                        <tr><th class='line' colspan='2'><p></p></th></tr>
                        <tr><th class='td01'><p>카드잔액</p></th>
                        <td class='td02'><p>" . @(in_array($resultMap["GAMG_Price6"] , $resultMap) ? $resultMap["GAMG_Price6"] : "null" ) . "원</p></td></tr>";
                        }

                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("OCBPoint", $resultMap["payMethod"]) == 0) { //오케이 캐쉬백
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>지불구분</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["PayOption"] , $resultMap) ? $resultMap["PayOption"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>결제완료금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["applPrice"] , $resultMap) ? $resultMap["applPrice"] : "null" ) . "원</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>OCB 카드번호</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["OCB_Num"] , $resultMap) ? $resultMap["OCB_Num"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>적립 승인번호</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["OCB_SaveApplNum"] , $resultMap) ? $resultMap["OCB_SaveApplNum"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>사용 승인번호</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["OCB_PayApplNum"] , $resultMap) ? $resultMap["OCB_PayApplNum"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>OCB 지불 금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["OCB_PayPrice"] , $resultMap) ? $resultMap["OCB_PayPrice"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && (strcmp("GSPT", $resultMap["payMethod"]) == 0)) { //GSPoint
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>지불구분</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["PayOption"] , $resultMap) ? $resultMap["PayOption"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>GS 포인트 승인금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GSPT_ApplPrice"] , $resultMap) ? $resultMap["GSPT_ApplPrice"] : "null" ) . "원</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>GS 포인트 적립금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GSPT_SavePrice"] , $resultMap) ? $resultMap["GSPT_SavePrice"] : "null" ) . "원</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>GS 포인트 지불금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["GSPT_PayPrice"] , $resultMap) ? $resultMap["GSPT_PayPrice"] : "null" ) . "원</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("UPNT", $resultMap["payMethod"]) == 0) {  //U-포인트
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>U포인트 카드번호</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["UPoint_Num"] , $resultMap) ? $resultMap["UPoint_Num"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>가용포인트</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["UPoint_usablePoint"] , $resultMap) ? $resultMap["UPoint_usablePoint"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>포인트지불금액</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["UPoint_ApplPrice"] , $resultMap) ? $resultMap["UPoint_ApplPrice"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("KWPY", $resultMap["payMethod"]) == 0) {  //뱅크월렛 카카오
                        echo "<tr><th class='td01'><p>결제방법</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["payMethod"] , $resultMap) ? $resultMap["payMethod"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>결과 코드</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["resultCode"] , $resultMap) ? $resultMap["resultCode"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>결과 내용</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["resultMsg"] , $resultMap) ? $resultMap["resultMsg"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>거래 번호</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["tid"] , $resultMap) ? $resultMap["tid"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>주문 번호</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["MOID"] , $resultMap) ? $resultMap["MOID"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>결제완료금액</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["price"] , $resultMap) ? $resultMap["price"] : "null" ) . "원</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>사용일자</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["applDate"] , $resultMap) ? $resultMap["applDate"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>사용시간</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["applTime"] , $resultMap) ? $resultMap["applTime"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("YPAY", $resultMap["payMethod"]) == 0) { //엘로우 페이
                        //별도 응답 필드 없음
                    } else if (isset($resultMap["payMethod"]) && strcmp("TEEN", $resultMap["payMethod"]) == 0) { //틴캐시
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>틴캐시 승인번호</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["TEEN_ApplNum"] , $resultMap) ? $resultMap["TEEN_ApplNum"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>틴캐시아이디</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["TEEN_UserID"] , $resultMap) ? $resultMap["TEEN_UserID"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>틴캐시승인금액</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["TEEN_ApplPrice"] , $resultMap) ? $resultMap["TEEN_ApplPrice"] : "null" ) . "원</p></td></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("Bookcash", $resultMap["payMethod"]) == 0) { //도서문화상품권
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>도서상품권 승인번호</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["BCSH_ApplNum"] , $resultMap) ? $resultMap["BCSH_ApplNum"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>도서상품권 사용자ID</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["BCSH_UserID"] , $resultMap) ? $resultMap["BCSH_UserID"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>도서상품권 승인금액</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["BCSH_ApplPrice"] , $resultMap) ? $resultMap["BCSH_ApplPrice"] : "null" ) . "원</p></td></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("PhoneBill", $resultMap["payMethod"]) == 0) { //폰빌전화결제
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>승인전화번호</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["PHNB_Num"] , $resultMap) ? $resultMap["PHNB_Num"] : "null" ) . "</p></td></tr>
						<tr><th class='line' colspan='2'><p></p></th></tr>";
                    } else if (isset($resultMap["payMethod"]) && strcmp("Bill", $resultMap["payMethod"]) == 0) { //빌링결제
                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						<tr><th class='td01'><p>빌링키</p></th>
						<td class='td02'><p>" . @(in_array($resultMap["CARD_BillKey"] , $resultMap) ? $resultMap["CARD_BillKey"] : "null" ) . "</p></td></tr>";
                    } else { //카드
						
						$quota = @(in_array($resultMap["CARD_Quota"] , $resultMap) ? $resultMap["CARD_Quota"] : "null" );

                        if (isset($resultMap["EventCode"]) && !is_null($resultMap["EventCode"])) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>이벤트 코드</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["EventCode"] , $resultMap) ? $resultMap["EventCode"] : "null" ) . "</p></td></tr>";
                        }

                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>카드번호</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["CARD_Num"] , $resultMap) ? $resultMap["CARD_Num"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>할부기간</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["CARD_Quota"] , $resultMap) ? $resultMap["CARD_Quota"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>";

                        if (isset($resultMap["EventCode"]) && isset($resultMap["CARD_Interest"]) && (strcmp("1", $resultMap["CARD_Interest"]) == 0 || strcmp("1", $resultMap["EventCode"]) == 0 )) {

                            echo "<tr><th class='td01'><p>할부 유형</p></th>
								<td class='td02'><p>무이자</p></td></tr>";
                        //} else if (isset($resultMap["CARD_Interest"]) && $quota > 0 && !strcmp("1", $resultMap["CARD_Interest"]) == 0) {
                        } else if ($quota > 0 && isset($resultMap["CARD_Interest"]) && !strcmp("1", $resultMap["CARD_Interest"]) == 0) {
                            echo "<tr><th class='td01'><p>할부 유형</p></th>
								<td class='td02'><p>유이자 <font color='red'> *유이자로 표시되더라도 EventCode 및 EDI에 따라 무이자 처리가 될 수 있습니다.</font></p></td></tr>";
                        }

                        if (isset($resultMap["point"]) && strcmp("1", $resultMap["point"]) == 0) {

                            echo "<td class='td02'><p></p></td></tr>
							<tr><th class='td01'><p>포인트 사용 여부</p></th>
							<td class='td02'><p>사용</p></td></tr>";
                        } else {

                            echo "<td class='td02'><p></p></td></tr>
							<tr><th class='td01'><p>포인트 사용 여부</p></th>
							<td class='td02'><p>미사용</p></td></tr>";
                        }

                        echo "<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>카드 종류</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["CARD_Code"] , $resultMap) ? $resultMap["CARD_Code"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>카드 발급사</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["CARD_BankCode"] , $resultMap) ? $resultMap["CARD_BankCode"] : "null" ) . "</p></td></tr>

							<tr><th class='td01'><p>부분취소 가능여부</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["CARD_PRTC_CODE"] , $resultMap) ? $resultMap["CARD_PRTC_CODE"] : "null" ) . "</p></td></tr>
							<tr><th class='line' colspan='2'><p></p></th></tr>
							<tr><th class='td01'><p>체크카드 여부</p></th>
							<td class='td02'><p>" . @(in_array($resultMap["CARD_CheckFlag"] , $resultMap) ? $resultMap["CARD_CheckFlag"] : "null" ) . "</p></td></tr>";

                        if (isset($resultMap["OCB_Num"]) && !is_null($resultMap["OCB_Num"]) && !empty($resultMap["OCB_Num"])) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>OK CASHBAG 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["OCB_Num"] , $resultMap) ? $resultMap["OCB_Num"] : "null" ) . "</p></td></tr>
								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>OK CASHBAG 적립 승인번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["OCB_SaveApplNum"] , $resultMap) ? $resultMap["OCB_SaveApplNum"] : "null" ) . "</p></td></tr>
								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>OK CASHBAG 포인트지불금액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["OCB_PayPrice"] , $resultMap) ? $resultMap["OCB_PayPrice"] : "null" ) . "</p></td></tr>";
                        }
                        if (isset($resultMap["GSPT_Num"]) && !is_null($resultMap["GSPT_Num"]) && !empty($resultMap["GSPT_Num"])) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>GS&Point 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GSPT_Num"] , $resultMap) ? $resultMap["GSPT_Num"] : "null" ) . "</p></td></tr>

								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>GS&Point 잔여한도</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GSPT_Remains"] , $resultMap) ? $resultMap["GSPT_Remains"] : "null" ) . "</p></td></tr>

								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>GS&Point 승인금액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["GSPT_ApplPrice"] , $resultMap) ? $resultMap["GSPT_ApplPrice"] : "null" ) . "</p></td></tr>";
                        }

                        if (isset($resultMap["UNPT_CardNum"]) && !is_null($resultMap["UNPT_CardNum"]) && !empty($resultMap["UNPT_CardNum"])) {

                            echo "<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>U-Point 카드번호</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["UNPT_CardNum"] , $resultMap) ? $resultMap["UNPT_CardNum"] : "null" ) . "</p></td></tr>

								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>U-Point 가용포인트</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["UPNT_UsablePoint"] , $resultMap) ? $resultMap["UPNT_UsablePoint"] : "null" ) . "</p></td></tr>

								<tr><th class='line' colspan='2'><p></p></th></tr>
								<tr><th class='td01'><p>U-Point 포인트지불금액</p></th>
								<td class='td02'><p>" . @(in_array($resultMap["UPNT_PayPrice"] , $resultMap) ? $resultMap["UPNT_PayPrice"] : "null" ) . "</p></td></tr>";
                        }
                    }

                    echo "</table>
					<span style='padding-left : 100px;'></span>
					<form name='frm' method='post'> 
						<input type='hidden' name='tid' value='" . @(in_array($resultMap["tid"] , $resultMap) ? $resultMap["tid"] : "null" ) . "'/>
					</form>				
					</pre>";

                    // 수신결과를 파싱후 resultCode가 "0000"이면 승인성공 이외 실패
                    // 가맹점에서 스스로 파싱후 내부 DB 처리 후 화면에 결과 표시
                    // payViewType을 popup으로 해서 결제를 하셨을 경우
                    // 내부처리후 스크립트를 이용해 opener의 화면 전환처리를 하세요
                    //throw new Exception("강제 Exception");
					
					
					
					//------------------------/
                } catch (Exception $e) {
                    //    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                    //####################################
                    // 실패시 처리(***가맹점 개발수정***)
                    //####################################
                    //---- db 저장 실패시 등 예외처리----//
                    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                    echo $s;

                    //#####################
                    // 망취소 API
                    //#####################

                    $netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
                    if ($httpUtil->processHTTP($netCancel, $authMap)) {
                        $netcancelResultString = $httpUtil->body;
                    } else {
                        echo "Http Connect Error\n";
                        echo $httpUtil->errormsg;

                        throw new Exception("Http Connect Error");
                    }

                    echo "## 망취소 API 결과 ##";

                    $netcancelResultString = str_replace("<", "&lt;", $$netcancelResultString);
                    $netcancelResultString = str_replace(">", "&gt;", $$netcancelResultString);

                    echo "<pre>", $netcancelResultString . "</pre>";
                    // 취소 결과 확인
                }
            } else {

                //#############
                // 인증 실패시
                //#############
                echo "<br/>";
                echo "####인증실패####";

                echo "<pre>" . var_dump($_REQUEST) . "</pre>";
            }
        } catch (Exception $e) {
            $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            echo $s;
        } 
	}
	
	//이니시스 주문 리스트
	function inicis_order_list(){
		$input = array();
		foreach($this->input->post_get(NULL, TRUE) as $key => $val) $input["{$key}"]  = $val;
		if(!isset($input["page"])) $input["page"] = 1;
		if(!isset($input["pagelist"])) $input["pagelist"] = 30;
		$data['input'] = $input;
		$input['table'] = "inicis_pay_result"; // 외부에서 테이블명을 주입

		$db_data = $this->common_model->page_list_m($input);

		$link_url = "/".$this->segs[1]."/".$this->segs[2]."/".$this->segs[3]."/";
		$total_count = $db_data['total_cnt'];
		$data['total_count'] = $total_count;

		$config = $this->pagination_custom_v3->pagenation_bootstrap($input["page"], $total_count, $input["pagelist"], $link_url, $segment=4, $num_link=3);
		$config['base_url'] = BASE_URL.$link_url;
		$config['page_query_string'] = true; //쿼리 스트링
		$config['query_string_segment'] = 'page';
		$config['display_always'] = TRUE;      
		$config['use_fixed_page'] = TRUE;
		$config['fixed_page_num'] = 10;
		$this->pagination_custom_v3->initialize($config);
		
		$data['page_nation'] = $this->pagination_custom_v3->create_links();
		$data['lists'] = $db_data['page_list_m'];
		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/analytics/inicis_order_list_v",$data);
		$this->load->view("/kaleido/common/footer");
		
	}
	
	//이니시스 취소 처리
	function inicis_cancls(){}
	
	//이니시스 모바일 결제 
	function inicis_m_order(){}
	
	//kakao obj test
	function kakao(){		
		$data = array();
		$data['MID'] = "cnstest25m";
		$data['merchantEncKey'] = "10a3189211e1dfc6";
		$data['merchantHashKey'] = "10a3189211e1dfc6";
		$data['msgName'] = "/merchant/requestDealApprove.dev";
		$data['CNSPAY_WEB_SERVER_URL'] = "https://kmpay.lgcns.com:8443";
		$data['CnsPayDealRequestUrl'] = "https://pg.cnspay.co.kr:443";
		$data['cancelPwd'] = "123456";
		$data['LogDir'] = "D:/Log";		
		
		$pg = new Kaleido\Pg([$data]);
		$lgcns = $pg->getLgcnsHeandler();		
		//print_r($lgcns);
		$KmpayFunc = $lgcns->getKmpayFunc($data); 		
		$KmpayFunc->kmpayFunc("D:/Log");
		$KmpayFunc->writeLog("test"); //log test good!
		$ec = $KmpayFunc->parameterEncrypt('aa','tt');
	}
	
	//카카오페이 주문리스트
	function kakao_order_list(){
		$input = array();
		foreach($this->input->post_get(NULL, TRUE) as $key => $val) $input["{$key}"]  = $val;
		if(!isset($input["page"])) $input["page"] = 1;
		if(!isset($input["pagelist"])) $input["pagelist"] = 30;
		$data['input'] = $input;
		$input['table'] = "kakao_pay_result"; // 외부에서 테이블명을 주입

		$db_data = $this->common_model->page_list_m($input);

		$link_url = "/".$this->segs[1]."/".$this->segs[2]."/".$this->segs[3]."/";
		$total_count = $db_data['total_cnt'];
		$data['total_count'] = $total_count;

		$config = $this->pagination_custom_v3->pagenation_bootstrap($input["page"], $total_count, $input["pagelist"], $link_url, $segment=4, $num_link=3);
		$config['base_url'] = BASE_URL.$link_url;
		$config['page_query_string'] = true; //쿼리 스트링
		$config['query_string_segment'] = 'page';
		$config['display_always'] = TRUE;      
		$config['use_fixed_page'] = TRUE;
		$config['fixed_page_num'] = 10;
		$this->pagination_custom_v3->initialize($config);
		
		$data['page_nation'] = $this->pagination_custom_v3->create_links();
		$data['lists'] = $db_data['page_list_m'];
		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/analytics/kakao_order_list_v",$data);
		$this->load->view("/kaleido/common/footer");
		
	}
	
	//카카오페이	 폼
	function kakao_form() {
		$data = kakaopay_init(); //config init
		$pg = new Kaleido\Pg();
		$lgcns = $pg->getLgcnsHeandler([$data]);

		//가맹점서명키 (꼭 해당 가맹점키로 바꿔주세요)
		//$merchantKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
		
		$data['ediDate'] = date("YmdHis");  // 전문생성일시

		//상품가격 - hash 생성 시 필요하므로 화면로딩시 가지고 있어야 하는 값
		$data['Amt'] = 1000; //ps 이 가격은 추가로 입력 폼에서 변동이 되어도 상관 없다 그러니 고정으로 놔두면 됨

		////////위변조 처리/////////
		//결제요청용 키값
		$data['cnspay_lib'] = $lgcns->CnsPayWebConnector();
		$data['md_src'] = $data['ediDate'].$data['MID'].$data['Amt'];
		$data['salt'] = hash("sha256",$data['merchantKey'].$data['md_src'],false);
		
		$data['hash_input'] = $lgcns->makeHashInputString($data['salt']);		
		$data['hash_calc'] = hash("sha256", $data['hash_input'], false);
		$data['hash_String'] = base64_encode($data['hash_calc']);
		
		//기본값 -- 딱히 변경 금지
		$data['AuthFlg'] = "10";
		$data['currency'] = "KRW";
		$data['remoteaddr'] = $_SERVER['REMOTE_ADDR'];
		$data['serveraddr'] = $_SERVER['SERVER_ADDR'];
		$data['merchantTxnNum'] = date("YmdHis",time());

		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/order/kakao_form_v",$data);
		$this->load->view("/kaleido/common/footer");
	}
	
	//request 체크...
	function _KMPayRequest($key) {
		return (isset($_REQUEST[$key])?$_REQUEST[$key]:"");
	}
	
	//txn 페이지 iframe 으로 구성되어있다
	function kakao_txnId(){		
		$data = kakaopay_init(); //config init
		$pg = new Kaleido\Pg();
		$lgcns = $pg->getLgcnsHeandler([$data]);
		$kmFunc = $lgcns->getKmpayFunc($data);
		
	
		// 로그 저장 위치 지정
		//$kmFunc = new kmpayFunc($LogDir);

		$kmFunc->setPhpVersion($data['phpVersion']);

		// TXN_ID를 요청하기 위한 PARAMETERR
		$REQUESTDEALAPPROVEURL = $this->_KMPayRequest("requestDealApproveUrl");	//인증 요청 경로
		$PR_TYPE = $this->_KMPayRequest("prType");												//결제 요청 타입
		$MERCHANT_ID = $this->_KMPayRequest("MID");											//가맹점 ID
		$MERCHANT_TXN_NUM = $this->_KMPayRequest("merchantTxnNum");				//가맹점 거래번호
		$CHANNEL_TYPE = $this->_KMPayRequest("channelType");
		$PRODUCT_NAME = $this->_KMPayRequest("GoodsName");								//상품명
		$AMOUNT = $this->_KMPayRequest("Amt");												//상품금액(총거래금액) (총거래금액 = 공급가액 + 부가세 + 봉사료)

		$CURRENCY = $this->_KMPayRequest("currency");											//거래통화(KRW/USD/JPY 등)
		$RETURN_URL = $this->_KMPayRequest("returnUrl");										//결제승인결과전송URL
		$CERTIFIED_FLAG = $this->_KMPayRequest("CERTIFIED_FLAG");							//가맹점 인증 구분값 ("N","NC")

		$OFFER_PERIOD_FLAG = $this->_KMPayRequest("OFFER_PERIOD_FLAG");							//상품제공기간 플래그
		$OFFER_PERIOD = $this->_KMPayRequest("OFFER_PERIOD");							//상품제공기간


		//무이자옵션
		$NOINTYN = $this->_KMPayRequest("noIntYN");											//무이자 설정
		$NOINTOPT = $this->_KMPayRequest("noIntOpt");										//무이자 옵션
		$MAX_INT =$this->_KMPayRequest("maxInt");												//최대할부개월
		$FIXEDINT = $this->_KMPayRequest("fixedInt");												//고정할부개월
		$POINT_USE_YN = $this->_KMPayRequest("pointUseYn");								//카드사포인트사용여부
		$POSSICARD = $this->_KMPayRequest("possiCard");										//결제가능카드설정
		$BLOCK_CARD = $this->_KMPayRequest("blockCard");									//금지카드설정

		// ENC KEY와 HASH KEY는 가맹점에서 생성한 KEY 로 SETTING 한다.
		$merchantEncKey = $this->_KMPayRequest("merchantEncKey");
		$merchantHashKey = $this->_KMPayRequest("merchantHashKey");
		$hashTarget = $MERCHANT_ID.$MERCHANT_TXN_NUM.str_pad($AMOUNT,7,"0",STR_PAD_LEFT);

		// payHash 생성
		$payHash = strtoupper(hash("sha256", $hashTarget.$merchantHashKey, false));

		//json string 생성
		//$strJsonString = new JsonString($LogDir);
		$strJsonString = $lgcns->JsonString($data);


		$strJsonString->setValue("PR_TYPE", $PR_TYPE);
		$strJsonString->setValue("CHANNEL_TYPE", $CHANNEL_TYPE);
		$strJsonString->setValue("MERCHANT_ID", $MERCHANT_ID);
		$strJsonString->setValue("MERCHANT_TXN_NUM", $MERCHANT_TXN_NUM);
		$strJsonString->setValue("PRODUCT_NAME", $PRODUCT_NAME);

		$strJsonString->setValue("AMOUNT", $AMOUNT);

		$strJsonString->setValue("CURRENCY", $CURRENCY);
		$strJsonString->setValue("CERTIFIED_FLAG", $CERTIFIED_FLAG);

		$strJsonString->setValue("OFFER_PERIOD_FLAG", $OFFER_PERIOD_FLAG);
		$strJsonString->setValue("OFFER_PERIOD", $OFFER_PERIOD);

		$strJsonString->setValue("NO_INT_YN", $NOINTYN);
		$strJsonString->setValue("NO_INT_OPT", $NOINTOPT);
		$strJsonString->setValue("MAX_INT", $MAX_INT);
		$strJsonString->setValue("FIXED_INT", $FIXEDINT);

		$strJsonString->setValue("POINT_USE_YN", $POINT_USE_YN);
		$strJsonString->setValue("POSSI_CARD", $POSSICARD);
		$strJsonString->setValue("BLOCK_CARD", $BLOCK_CARD);

		$strJsonString->setValue("PAYMENT_HASH", $payHash);

		// 결과값을 담는 부분
		$resultCode = "500";
		$resultMsg = "기타오류";
		$txnId = "";
		$prDt = "";
		$strValid = "";
		$rawResult = "";

		// Data 검증
		//$dataValidator = new KMPayDataValidator($strJsonString->getArrayValue());
		$dataValidator = $lgcns->KMPayDataValidator($strJsonString->getArrayValue());		
		$strValid = $dataValidator->resultValid;
			//alert($strValid,"");
		if (strlen($strValid) > 0) {
			$arrVal = explode(",", $strValid);
			if (count($arrVal) == 3) {
				$resultCode = $arrVal[1];
				$resultMsg = $arrVal[2];
			} else {
				$resultCode = $strValid;
				$resultMsg = $strValid;
			}
		}

		// Data에 이상 없는 경우
		if (strlen($strValid) == 0) {
			// CBC 암호화
			$paramStr = $strJsonString->getJsonString();
			$kmFunc->writeLog("Request");
			$kmFunc->writeLog($paramStr);
			$kmFunc->writeLog($strJsonString->getArrayValue());
			$encryptStr = $kmFunc->parameterEncrypt($merchantEncKey, $paramStr);
			$payReqResult = $kmFunc->connMPayDLP($REQUESTDEALAPPROVEURL, $MERCHANT_ID, $encryptStr);
			$rawResult = $payReqResult;
			$resultString = $kmFunc->parameterDecrypt($merchantEncKey, $payReqResult);

			//$resultJSONObject = new JsonString($LogDir);		
			$resultJSONObject = $lgcns->JsonString($data);

			if (substr($resultString, 0, 1) == "{") {
			  $resultJSONObject->setJsonString($resultString);
			  $resultCode = $resultJSONObject->getValue("RESULT_CODE");
					$resultMsg = $resultJSONObject->getValue("RESULT_MSG");
					if ($resultCode == "00") {
				$txnId = $resultJSONObject->getValue("TXN_ID");
					$prDt = $resultJSONObject->getValue("PR_DT");
				}
			}
			else {
				$tmpArrVal = explode(",", $rawResult);
				if (count($tmpArrVal) == 3 && $tmpArrVal[0] == "_FAIL_") {
					$resultCode = $tmpArrVal[1];
					$resultMsg = $tmpArrVal[2];
				} else {
					$resultCode = $strValid;
					$resultMsg = $strValid;
				}
			}


			$kmFunc->writeLog("Result");
			$kmFunc->writeLog($resultString);
			$kmFunc->writeLog($resultJSONObject->getArrayValue());

		}
		$data['resultCode'] = $resultCode;
		$data['resultMsg'] = $resultMsg;
		$data['txnId'] = $txnId;
		$data['prDt'] = $prDt;
		$this->load->view("/kaleido/order/kakao_txnid_v",$data);
	}
	
	//result page
	function kakaopayLiteResult(){
		$data = kakaopay_init(); //config init
		$pg = new Kaleido\Pg();
		//$lgcns = $pg->getLgcnsHeandler();
		
		// 로그 저장 위치 지정
		//$connector = new CnsPayWebConnector($LogDir);		
		$connector = $pg->getLgcnsHeandler([$data]);
		$connector->CnsActionUrl($data['CnsPayDealRequestUrl']);//$CnsPayDealRequestUrl
		$connector->CnsPayVersion($data['phpVersion']);//$phpVersion

		// 요청 페이지 파라메터 셋팅
		//$connector->setRequestData($_REQUEST);
		$connector->setRequestData($this->input->get_post(NULL, true)); //ci style

		// 추가 파라메터 셋팅
		$connector->addRequestData("actionType", "PY0");  						// actionType : CL0 취소, PY0 승인, CI0 조회
		$connector->addRequestData("MallIP", $_SERVER['REMOTE_ADDR']);	// 가맹점 고유 ip
		$connector->addRequestData("CancelPwd", $data['cancelPwd']);

		//가맹점키 셋팅 (MID 별로 틀림)
		$connector->addRequestData("EncodeKey", $data['MID']);

		// 4. CNSPAY Lite 서버 접속하여 처리
		$connector->requestAction();

		// 5. 결과 처리
		$insert_data['resultCode'] 	= $resultCode = $connector->getResultData("ResultCode"); 			// 결과코드 (정상 :3001 , 그 외 에러)
		$insert_data['resultMsg'] 	= $resultMsg = $connector->getResultData("ResultMsg");   			// 결과메시지
		$insert_data['authDate'] 	= $authDate = $connector->getResultData("AuthDate");   				// 승인일시 YYMMDDHH24mmss
		$insert_data['authCode'] 	= $authCode = $connector->getResultData("AuthCode");   				// 승인번호
		$insert_data['buyerName'] 	= $buyerName = $connector->getResultData("BuyerName");   			// 구매자명
		$insert_data['goodsName'] 	= $goodsName = $connector->getResultData("GoodsName"); 				// 상품명
		$insert_data['payMethod'] 	= $payMethod = $connector->getResultData("PayMethod");  			// 결제수단
		$insert_data['mid'] 		= $mid = $connector->getResultData("MID");  									// 가맹점ID
		$insert_data['tid'] 		= $tid = $connector->getResultData("TID");  									// 거래ID
		$insert_data['moid'] 		= $moid = $connector->getResultData("Moid");  								// 주문번호
		$insert_data['amt'] 		= $amt = $connector->getResultData("Amt");  									// 금액
		$insert_data['cardCode'] 	= $cardCode = $connector->getResultData("CardCode");					// 카드사 코드
		$insert_data['cardName'] 	= $cardName = $connector->getResultData("CardName");  	 			// 결제카드사명
		$insert_data['cardQuota'] 	= $cardQuota = $connector->getResultData("CardQuota"); 				// 할부개월수 ex) 00:일시불,02:2개월
		$insert_data['cardInterest']= $cardInterest = $connector->getResultData("CardInterest");	// 무이자 여부 (0:일반, 1:무이자)
		$insert_data['cardCl'] 		= $cardCl = $connector->getResultData("CardCl");             	// 체크카드여부 (0:일반, 1:체크카드)
		$insert_data['cardBin'] 	= $cardBin = $connector->getResultData("CardBin");           	// 카드BIN번호
		$insert_data['cardPoint'] 	= $cardPoint = $connector->getResultData("CardPoint");       	// 카드사포인트사용여부 (0:미사용, 1:포인트사용, 2:세이브포인트사용)

		//부인방지토큰값
		$insert_data['NON_REP_TOKEN'] = $nonRepToken =$_REQUEST["NON_REP_TOKEN"];

		$paySuccess = false;												// 결제 성공 여부    

		$insert_data['resultMsg'] 	= $resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
		$insert_data['cardCode'] 	= $cardName = iconv("euc-kr", "utf-8", $cardName);
		$insert_data['goodsName'] 	= $goodsName = iconv("euc-kr", "utf-8", $goodsName);
		$insert_data['buyerName'] 	= $buyerName = iconv("euc-kr", "utf-8", $buyerName);
		$insert_data['cardCode'] 	= $cardCode = iconv("euc-kr", "utf-8", $cardCode);
		$insert_data['cardName'] 	= $cardName = iconv("euc-kr", "UTF-8//IGNORE", $cardName);////iconv("EUC-KR","UTF-8//IGNORE",$str); 
		

		/** 위의 응답 데이터 외에도 전문 Header와 개별부 데이터 Get 가능 */
		if($payMethod == "CARD"){	//신용카드
			if($resultCode == "3001") $paySuccess = true;				// 결과코드 (정상 :3001 , 그 외 에러)
		}
		if($paySuccess) {
		   // 결제 성공시 DB처리 하세요.
			$insert_data['paySuccess'] = $paySuccess;
			$insert_data['createDatetime'] = date("Y-m-d H:i:s"); //입력시간
			$insert_id = $this->common_model->insert("kakao_pay_result",$insert_data); //table, data[]			
			if($insert_id) alert('결제 성공!!',KAKAO_URL."/kakao_order_list");
		}else{
		   // 결제 실패시 DB처리 하세요.
			$insert_data['paySuccess'] = $paySuccess;
			$insert_data['createDatetime'] = date("Y-m-d H:i:s"); //입력시간
			$insert_id = $this->common_model->insert("kakao_pay_result",$insert_data); //table, data[]			
			alert('결제 실패!!',KAKAO_URL."/kakao_form");
		}


	}
	
	//정보조회 페이지
	function kakaopayInfoRequest(){
		$data = array();
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/order/kakao_info_v",$data);
		$this->load->view("/kaleido/common/footer");
	}
	
	//정보조회 리턴
	function kakaopayInfoResult(){
		$data = kakaopay_init(); //config init
		$pg = new Kaleido\Pg();
		
		// 로그 저장 위치 지정				
		$connector = $pg->getLgcnsHeandler([$data]);		
		//$connector = new CnsPayWebConnector($LogDir);

		// 상점키 암호화
		$ediDate = date("YmdHis");
		$TID = $_REQUEST["TID"];
		$md_src = $ediDate.$data['MID'].$TID;
		$salt = hash("sha256",$data['merchantKey'].$md_src,false);
		$hash_input = $connector->makeHashInputString($salt);
		$hash_calc = hash("sha256", $hash_input, false);
		$hash_String = base64_encode($hash_calc);

		//결제 처리 경로(DB 또는 Config 파일로 관리한다.)
		$connector->CnsActionUrl($data['CnsPayDealRequestUrl']);
		$connector->CnsPayVersion($data['phpVersion']);
		// 요청 페이지 파라메터 셋팅
		//$connector->setRequestData($_REQUEST);
		$connector->setRequestData($this->input->get_post(NULL, true)); //ci style

		$connector->addRequestData("actionType", "CI0");
		$connector->addRequestData("PayMethod", "TID_INFO");

		$connector->addRequestData("MID", $data['MID']);
		$connector->addRequestData("EncodeKey", $data['merchantKey']);
		$connector->addRequestData("EdiDate", $ediDate);
		$connector->addRequestData("EncryptData", $hash_String);

		// CNSPAY Lite 서버 접속하여 처리
		$connector->requestAction();
		// 결과 처리
		$data['paySuccess'] 	= $paySuccess = false;										// 결제 상태 조회 성공 여부
		$data['resultCode'] 	= $resultCode = $connector->getResultData("ResultCode");	// 결과코드 (정상 :00 , 그 외 에러)
		$data['resultMsg']		= $resultMsg = $connector->getResultData("ResultMsg");  	// 결과메시지
		$data['TID'] 			= $TID = $connector->getResultData("TID");   				// 거래 ID
		$data['StateCd'] 		= $StateCd = $connector->getResultData("StateCd");   		// 거래상태 (0:승인, 1:전체취소, 2:부분취소 - 여러 건 부분취소 후 잔액이 0원일 경우 1:전체취소 로 응답)
		$data['AppAmt'] 		= $AppAmt = $connector->getResultData("AppAmt");   			// 승인금액
		$data['CcAmt'] 			= $CcAmt = $connector->getResultData("CcAmt");   			// 취소금액
		$data['RemainAmt'] 		= $RemainAmt = $connector->getResultData("RemainAmt");   // 승인잔액
		$data['CancelYn'] 		= $CancelYn = $connector->getResultData("CancelYn");   		// 요청 취소건 취소결과 (Y:성공, N:실패)
		$data['resultMsg'] 		= $resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
		if ($resultCode != null) {
			if ($resultCode == "00") {
				$paySuccess = true;		// 결제 상태 조회 성공 여부
			}	
		}

		$StateNm = "";
		if ($paySuccess) {
			// 결제 상태 조회 성공시 DB처리 하세요.
			if ($StateCd == "0") {
				$data['StateNm'] = $StateNm = "승인";
			} else if ($StateCd == "1") {
				$data['StateNm'] = $StateNm = "전체취소";
			} else if ($StateCd == "2") {
				$data['StateNm'] = $StateNm = "부분취소";
			}  
		} else {
		   $data['StateNm'] = $StateNm = "처리실패";
		   // 결제 상태 조회 실패시 DB처리 하세요.
		}
		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/order/kakao_infoResult_v",$data);
		$this->load->view("/kaleido/common/footer");
	}
	
	//주문 캔슬
	function kakaopayCancel(){
		$data = kakaopay_init(); //config init		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/order/kakao_cancel_v",$data);
		$this->load->view("/kaleido/common/footer");
	}
	//주문캔슬 리턴
	function kakaopayCancelResult(){
		$data = kakaopay_init(); //config init
		$pg = new Kaleido\Pg(); //obj create
		
		// 로그 저장 위치 지정
		//$connector = new CnsPayWebConnector($LogDir);		
		$connector = $pg->getLgcnsHeandler([$data]);
		$connector->CnsActionUrl($data['CnsPayDealRequestUrl']);//$CnsPayDealRequestUrl
		$connector->CnsPayVersion($data['phpVersion']);//$phpVersion
		
		$connector->CnsActionUrl($data['CnsPayDealRequestUrl']);
		$connector->CnsPayVersion($data['phpVersion']);
		$connector->setRequestData($_REQUEST);
		$connector->addRequestData("actionType", "CL0");
		$connector->addRequestData("CancelPwd", $data['cancelPwd']);
		$connector->addRequestData("CancelIP", $_SERVER['REMOTE_ADDR']);

		//가맹점키 셋팅 (MID 별로 틀림) 
		$connector->addRequestData("EncodeKey", $data['merchantKey']);

		// 4. CNSPAY Lite 서버 접속하여 처리
		$connector->requestAction();

		// 5. 결과 처리
		$data['resultCode'] = $resultCode = $connector->getResultData("ResultCode"); 		// 결과코드 (정상 :2001(취소성공), 그 외 에러)
		$data['resultMsg'] = $resultMsg = $connector->getResultData("ResultMsg");   		// 결과메시지
		$data['cancelAmt'] = $cancelAmt = $connector->getResultData("CancelAmt");   		// 취소금액
		$data['cancelDate'] = $cancelDate = $connector->getResultData("CancelDate"); 		// 취소일
		$data['cancelTime'] = $cancelTime = $connector->getResultData("CancelTime");   	// 취소시간
		$data['CancelNum'] = $CancelNum = $connector->getResultData("CancelNum");   		// 취소번호
		$data['payMethod'] = $payMethod = $connector->getResultData("PayMethod");  		// 취소 결제수단
		$data['mid'] = $mid = 	$connector->getResultData("MID");             		// MID
		$data['tid'] = $tid = $connector->getResultData("TID");               		// TID
		$data['errorCD'] = $errorCD = $connector->getResultData("ErrorCD");        	// 상세 에러코드
		$data['errorMsg'] = $errorMsg = $connector->getResultData("ErrorMsg");      	// 상세 에러메시지
		$data['authDate'] = $authDate = $cancelDate . $cancelTime;										// 취소거래시간
		$data['authDate'] = $authDate = $connector->makeDateString($authDate);
		$data['stateCD'] = $stateCD = $connector->getResultData("StateCD");         	// 거래상태코드 (0: 승인, 1:전취소, 2:후취소)
		$data['ccPartCl'] = $ccPartCl = $connector->getResultData("CcPartCl");        // 부분취소 가능여부 (0:부분취소불가, 1:부분취소가능)
		$data['PreCancelCode'] = $PreCancelCode = $connector->getResultData("PreCancelCode");        // 부분취소 가능여부 (0:부분취소불가, 1:부분취소가능)
		$data['errorMsg'] = $errorMsg = iconv("euc-kr", "utf-8", $errorMsg);
		$data['resultMsg'] = $resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
		
		$this->load->view("/kaleido/common/header");
		$this->load->view("/kaleido/common/aside_left");
		$this->load->view("/kaleido/order/kakao_cancelResult_v",$data);
		$this->load->view("/kaleido/common/footer");
	}
	
	
	
} //end controllers
