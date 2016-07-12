<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
<title>CNSPay 결제 샘플 페이지</title>
<meta name="viewport" content="user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, shrink-to-fit=no" />

<!-- OpenSource Library -->
<script src="<?php echo ($CnsPayDealRequestUrl) ?>/dlp/scripts/lib/easyXDM.min.js" type="text/javascript"></script>
<script src="<?php echo ($CnsPayDealRequestUrl) ?>/dlp/scripts/lib/json3.min.js" type="text/javascript"></script>

<!-- JQuery에 대한 부분은 site마다 버전이 다를수 있음 -->
<!-- jquery 플러그는 각자 알아서 잘... -->


<!-- DLP창에 대한 KaKaoPay Library -->
<script type="text/javascript" src="<?php echo ($CNSPAY_WEB_SERVER_URL) ?>/js/dlp/client/kakaopayDlpConf.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo ($CNSPAY_WEB_SERVER_URL) ?>/js/dlp/client/kakaopayDlp.min.js" charset="utf-8"></script>

<link href="https://pg.cnspay.co.kr:443/dlp/css/kakaopayDlp.css" rel="stylesheet" type="text/css" />


<script type="text/javascript">
	function cnspay() {
		// TO-DO : 가맹점에서 해줘야할 부분(TXN_ID)과 KaKaoPay DLP 호출 API
		// 결과코드가 00(정상처리되었습니다.)
		if(document.payForm.resultCode.value == '00') {
			// TO-DO : 가맹점에서 해줘야할 부분(TXN_ID)과 KaKaoPay DLP 호출 API
		    kakaopayDlp.setTxnId(document.payForm.txnId.value);		 
			<? if($this->agent->is_mobile()){ //모바일 여부?>
        		kakaopayDlp.setChannelType('WPM', 'TMS'); // PC결제
			<? } else { ?>
        		kakaopayDlp.setChannelType('MPM', 'WEB'); // 모바일 웹(브라우저)결제 
        		kakaopayDlp.addRequestParams({ MOBILE_NUM : '010-1234-5678'}); // 초기값 세팅
			<? } ?>
        
        kakaopayDlp.callDlp('kakaopay_layer', document.payForm, submitFunc);
		} else {
			alert('[RESULT_CODE] : ' + document.payForm.resultCode.value + '\n[RESULT_MSG] : ' + document.payForm.resultMsg.value);
		}
	}
	
	function getTxnId() {
		// form에 iframe 주소 세팅
		document.payForm.target = "txnIdGetterFrame";
		document.payForm.action = "<?=KAKAO_URL?>/kakao_txnId";
		//document.payForm.action = "/kakao/getTxnId.php";
		document.payForm.acceptCharset = "utf-8";
	  if (document.payForm.canHaveHTML) { // detect IE
	      document.charset = payForm.acceptCharset;
	  }
	    
		// post로 iframe 페이지 호출
		document.payForm.submit();
		// payForm의 타겟, action을 수정한다
		
		document.payForm.target = "";
		document.payForm.action = "<?=KAKAO_URL?>/kakaopayLiteResult";
		document.payForm.acceptCharset = "utf-8";
	  if (document.payForm.canHaveHTML) { // detect IE
	      document.charset = payForm.acceptCharset;
	  }	  
		// getTxnId.jsp의 onload 이벤트를 통해 cnspay() 호출		
	}
	
	var submitFunc = function cnspaySubmit(data){
		
		if(data.RESULT_CODE === '00') {            
            // 부인방지토큰은 기본적으로 name="NON_REP_TOKEN"인 input박스에 들어가게 되며, 아래와 같은 방법으로 꺼내서 쓸 수도 있다.
            // 해당값은 가군인증을 위해 돌려주는 값으로서, 가맹점과 카카오페이 양측에서 저장하고 있어야 한다.
            // var temp = data.NON_REP_TOKEN;
            document.payForm.submit();            
		} else if(data.RESLUT_CODE === 'KKP_SER_002') {
			// X버튼 눌렀을때의 이벤트 처리 코드 등록
			alert('[RESULT_CODE] : ' + data.RESULT_CODE + '\n[RESULT_MSG] : ' + data.RESULT_MSG);
		} else {
			alert('[RESULT_CODE] : ' + data.RESULT_CODE + '\n[RESULT_MSG] : ' + data.RESULT_MSG);
		}
	};

</script>
</head>

<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
			
			<form name="payForm" id="payForm" action="<?=KAKAO_URL?>/kakaopayLiteResult"  method="post" accept-charset = "">
			<!-- 결제 파라미터 목록 -->
			<b>결제 변수 목록(매뉴얼 참조)</b><br  />
			(*) 필수
			<ul>
				<li>(*)결제수단 : <input type="checkbox" name="PayMethod" value="KAKAOPAY" checked="checked"/>KAKAOPAY 고정</li>
				<li>(*)상품명 : <input name="GoodsName" type="text" value="곰인형"/></li>
				<li>(*)상품가격 : <input name="Amt" type="text" value="<?php echo($Amt); ?>"/></li>
				<li>공급가액 : <input name="SupplyAmt" type="text" value="0"/></li>
				<li>부가세 : <input name="GoodsVat" type="text" value="0"/></li>
				<li>봉사료 : <input name="ServiceAmt" type="text" value="0"/></li>
				<li>(*)상품갯수 : <input name="GoodsCnt" type="text"  value="1" readonly="readonly" style="background-color: #e2e2e2;" />고정</li>
				<li>(*)가맹점ID : <input name="MID" type="text" value="<?php echo($MID); ?>" /></li>
				<li>(*)인증플래그 : <input name="AuthFlg" type="text" value="10" readonly="readonly" style="background-color: #e2e2e2;" /> 고정</li>
				<li>(*)EdiDate : <input name="EdiDate" type="text" value="<?php echo($ediDate); ?>" readonly="readonly" style="background-color: #e2e2e2;"/></li>
				<li>(*)EncryptData : <input name="EncryptData" type="text" value="<?php echo($hash_String); ?>" readonly="readonly" style="background-color: #e2e2e2;"/></li>
				<li>구매자 이메일 : <input name="BuyerEmail" type="text" value="test@abc.com"/></li>
				<li>(*)구매자명 : <input name="BuyerName" type="text" value="테스터"/></li>
			</ul>
			<br />


			<!-- 인증 파라미터 목록 -->
			<b>인증 변수 목록(매뉴얼 참조)</b><br />
			<ul>
				<li>상품제공기간 플래그 : <input name="OFFER_PERIOD_FLAG" type="text" value="Y"/></li>
				<li>상품제공기간 : <input name="OFFER_PERIOD" type="text" value="제품표시일까지"/></li>
				<li>(*)인증구분 : <input type="text" name="CERTIFIED_FLAG" value="CN" readonly="readonly" style="background-color: #e2e2e2;" /> 고정</li>
				<li>(*)거래통화 : <input type="text" name="currency" value="KRW" readonly="readonly" style="background-color: #e2e2e2;" /> 고정</li>
				<li>(*)가맹점 암호화키 : <input type="text" name="merchantEncKey" value="<?php echo($merchantEncKey); ?>" /></li>
				<li>(*)가맹점 해쉬키 : <input type="text" name="merchantHashKey" value="<?php echo($merchantHashKey); ?>" /></li>
				<li>(*)TXN_ID 요청URL : <input type="text" name="requestDealApproveUrl" value="<?php echo($CNSPAY_WEB_SERVER_URL.$msgName); ?>" /></li>
				<li>
				(*)결제요청타입 :  
				<select name ="prType">
					<option value="MPM">MPM</option>
					<option value="WPM" selected="selected">WPM</option>
				</select>
				<br />MPM : 모바일결제, WPM : PC결제
				</li>
				<li>
				(*)채널타입 :  
				<select name ="channelType">
					<option value="2">2</option>
					<option value="4" selected="selected">4</option>
				</select>
				<br />2: 모바일결제, 4: PC결제
				</li>
				<li>(*)가맹점 거래번호 : <input type="text" name="merchantTxnNum" value="<?php echo($merchantTxnNum)?>" /></li>

			</ul>
			<br />

			<!-- 인증 파라미터 중 할부결제시 사용하는 파라미터 목록 -->
			<!-- 파라미터 입력형태는 매뉴얼 참조  -->
			<b>할부결제시 선택변수 목록</b><br />
			- 옳은 값들을 넣지 않으면 무이자를 사용하지 않는것으로 한다.<br />

			<b>카드코드(매뉴얼 참조)</b><br />
			- 비씨:01, 국민:02, 외환:03, 삼성:04, 신한:06, 현대:07, 롯데:08, 한미:11, 씨티:11, <br /> 
			NH채움(농협):12, 수협:13, 신협:13, 우리:15, 하나SK:16, 주택:18, 조흥(강원):19, <br />
			광주:21, 전북:22, 제주:23, 해외비자:25, 해외마스터:26, 해외다이너스:27, <br />
			해외AMX:28, 해외JCB:29, 해외디스커버:30, 은련:34
			<ul>
				<li>카드선택 : <input type="text" name="possiCard" value="" /> ex) 06</li>
				<li>할부개월 : <input type="text" name="fixedInt" value="" /> ex) 03</li>
				<li>최대할부개월 : <input type="text" name="maxInt" value="" /> ex) 24</li>
				<li>
				무이자 사용여부 :
				<select class="require" name="noIntYN" onchange="javascript:noIntYNonChange();">
					<option value="N">사용안함</option>
					<option value="Y">사용</option>
				</select>
				</li>
				<!-- 결제수단코드 + 카드코드 + - + 무이자 개월 ex) CC01-02:03:05:09 -->
				<li>무이자옵션 : <input type="text" name="noIntOpt" value="" /> ex) CC01-02:03:05</li>
				<li>
				카드사포인트사용여부 : 
				<select name ="pointUseYn">
					<option value="N">카드사 포인트 사용안함</option>
					<option value="Y">카드사 포인트 사용</option>
				</select>
				</li>
				<li>금지카드설정 : <input type="text" name="blockCard" value=""/> ex) 01:04:11</li>
				<li>특정제한카드 BIN : <input type="text" name="blockBin" value=""/></li>
			</ul>

			<div class="btns">
				<a href="javascript:getTxnId();">결제 요청하기</a>
			</div>

			<br />
			<br />
			<br />
			======================================================================<br />
			<b>getTxnId 응답</b><br />
			resultcode<input id="resultCode" type="text" value=""/><br/>
			resultmsg<input id="resultMsg" type="text" value=""/><br/>
			txnId<input id="txnId" type="text" value=""/><br/>
			prDt<input id="prDt" type="text" value=""/><br/>
			<br/>
			<br/>
			<!-- DLP호출에 대한 응답 -->
			<b>DLP 응답</b><br />
			SPU : <input type="text" name="SPU" value=""/><br/>
			SPU_SIGN_TOKEN : <input type="text" name="SPU_SIGN_TOKEN" value=""/><br/>
			MPAY_PUB : <input type="text" name="MPAY_PUB" value=""/><br/>
			NON_REP_TOKEN : <input type="text" name="NON_REP_TOKEN" value=""/><br/>
		</form>

		<!-- TODO :  LayerPopup의 Target DIV 생성 -->
		<div id="kakaopay_layer"  style="display: none"></div>

		<iframe name="txnIdGetterFrame" id="txnIdGetterFrame" src=""  width="0" height="0"></iframe>
			
		</div>
	</div>
</div>
	