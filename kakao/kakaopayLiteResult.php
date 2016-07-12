<?php
include "incKakaopayCommon.php";
include "lgcns_CNSpay.php";

// 로그 저장 위치 지정
$connector = new CnsPayWebConnector($LogDir);
$connector->CnsActionUrl($CnsPayDealRequestUrl);
$connector->CnsPayVersion($phpVersion);

// 요청 페이지 파라메터 셋팅
$connector->setRequestData($_REQUEST);

// 추가 파라메터 셋팅
$connector->addRequestData("actionType", "PY0");  						// actionType : CL0 취소, PY0 승인, CI0 조회
$connector->addRequestData("MallIP", $_SERVER['REMOTE_ADDR']);	// 가맹점 고유 ip
$connector->addRequestData("CancelPwd", $cancelPwd);

//가맹점키 셋팅 (MID 별로 틀림)
$connector->addRequestData("EncodeKey", $merchantKey);

// 4. CNSPAY Lite 서버 접속하여 처리
$connector->requestAction();

// 5. 결과 처리

$resultCode = $connector->getResultData("ResultCode"); 			// 결과코드 (정상 :3001 , 그 외 에러)
$resultMsg = $connector->getResultData("ResultMsg");   			// 결과메시지
$authDate = $connector->getResultData("AuthDate");   				// 승인일시 YYMMDDHH24mmss
$authCode = $connector->getResultData("AuthCode");   				// 승인번호
$buyerName = $connector->getResultData("BuyerName");   			// 구매자명
$goodsName = $connector->getResultData("GoodsName"); 				// 상품명
$payMethod = $connector->getResultData("PayMethod");  			// 결제수단
$mid = $connector->getResultData("MID");  									// 가맹점ID
$tid = $connector->getResultData("TID");  									// 거래ID
$moid = $connector->getResultData("Moid");  								// 주문번호
$amt = $connector->getResultData("Amt");  									// 금액
$cardCode = $connector->getResultData("CardCode");					// 카드사 코드
$cardName = $connector->getResultData("CardName");  	 			// 결제카드사명
$cardQuota = $connector->getResultData("CardQuota"); 				// 할부개월수 ex) 00:일시불,02:2개월
$cardInterest = $connector->getResultData("CardInterest");	// 무이자 여부 (0:일반, 1:무이자)
$cardCl = $connector->getResultData("CardCl");             	// 체크카드여부 (0:일반, 1:체크카드)
$cardBin = $connector->getResultData("CardBin");           	// 카드BIN번호
$cardPoint = $connector->getResultData("CardPoint");       	// 카드사포인트사용여부 (0:미사용, 1:포인트사용, 2:세이브포인트사용)

//부인방지토큰값
$nonRepToken =$_REQUEST["NON_REP_TOKEN"];

$paySuccess = false;												// 결제 성공 여부    

$resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
$cardName = iconv("euc-kr", "utf-8", $cardName);
$goodsName = iconv("euc-kr", "utf-8", $goodsName);

/** 위의 응답 데이터 외에도 전문 Header와 개별부 데이터 Get 가능 */
if($payMethod == "CARD"){	//신용카드
	if($resultCode == "3001") $paySuccess = true;				// 결과코드 (정상 :3001 , 그 외 에러)
}
if($paySuccess) {
   // 결제 성공시 DB처리 하세요.
}else{
   // 결제 실패시 DB처리 하세요.
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
  <head>
  	<title>CNSPay 결제완료 샘플 페이지</title>
	</head>
	<body>
		
	  <b>결제 내역입니다.</b><br />
	  <ul>
	      <li>결과 내용 : [<?php echo($resultCode) ?>] <?php echo($resultMsg) ?></li>
	      <li>결제 수단 : <?php echo($payMethod) ?></li>
	      <li>상품명 : <?php echo($goodsName) ?></li>
	      <li>금액 : <?php echo($amt) ?> 원</li>
	      <li>TID : <?php echo($tid) ?></li>
	      <li>MID : <?php echo($mid)?></li>
	      <li>가맹점거래번호 : <?php echo ($moid)?></li>
	      <li>카드사명 : <?php echo ($cardName) ?></li>
	      <li>할부개월 : <?php echo ($cardQuota) ?></li>
	      <li>무이자여부 : <?php echo ($cardInterest)?></li>
	      <li>체크카드여부 : <?php echo ($cardCl)?></li>
	      <li>카드BIN번호 : <?php echo ($cardBin)?></li>
	      <li>카드사포인트사용여부 : <?php echo ($cardPoint)?></li>
	      <li>승인일시 : <?php echo($authDate)?></li>
	      <li>승인번호 : <?php echo($authCode)?></li>
	      <li>부인방지토큰 : <textarea rows="5" cols="45" readonly="readonly" style="resize:none;" name="contents"><?php echo ($nonRepToken)?>&nbsp;</textarea></li>
	  </ul>
	</body>
</html>
