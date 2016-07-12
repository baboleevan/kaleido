<?php
include "incKakaopayCommon.php";
include "lgcns_CNSpay.php";

// 로그 저장 위치 지정
$connector = new CnsPayWebConnector($LogDir);
$connector->CnsActionUrl($CnsPayDealRequestUrl);
$connector->CnsPayVersion($phpVersion);
$connector->setRequestData($_REQUEST);
$connector->addRequestData("actionType", "CL0");
$connector->addRequestData("CancelPwd", $cancelPwd);
$connector->addRequestData("CancelIP", $_SERVER['REMOTE_ADDR']);

//가맹점키 셋팅 (MID 별로 틀림) 
$connector->addRequestData("EncodeKey", $merchantKey);

// 4. CNSPAY Lite 서버 접속하여 처리
$connector->requestAction();

// 5. 결과 처리
$resultCode = $connector->getResultData("ResultCode"); 		// 결과코드 (정상 :2001(취소성공), 그 외 에러)
$resultMsg = $connector->getResultData("ResultMsg");   		// 결과메시지
$cancelAmt = $connector->getResultData("CancelAmt");   		// 취소금액
$cancelDate = $connector->getResultData("CancelDate"); 		// 취소일
$cancelTime = $connector->getResultData("CancelTime");   	// 취소시간
$CancelNum = $connector->getResultData("CancelNum");   		// 취소번호
$payMethod = $connector->getResultData("PayMethod");  		// 취소 결제수단
$mid = 	$connector->getResultData("MID");             		// MID
$tid = $connector->getResultData("TID");               		// TID
$errorCD = $connector->getResultData("ErrorCD");        	// 상세 에러코드
$errorMsg = $connector->getResultData("ErrorMsg");      	// 상세 에러메시지
$authDate = $cancelDate . $cancelTime;										// 취소거래시간
$authDate = $connector->makeDateString($authDate);
$stateCD = $connector->getResultData("StateCD");         	// 거래상태코드 (0: 승인, 1:전취소, 2:후취소)
$ccPartCl = $connector->getResultData("CcPartCl");        // 부분취소 가능여부 (0:부분취소불가, 1:부분취소가능)
$PreCancelCode = $connector->getResultData("PreCancelCode");        // 부분취소 가능여부 (0:부분취소불가, 1:부분취소가능)
$errorMsg = iconv("euc-kr", "utf-8", $errorMsg);
$resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
    <title>CNSPay 결제 취소 결과 샘플 페이지</title>
</head>
<body>

    <b>취소 결과 입니다</b><br />
    <ul>
        <li>거래 ID : <?php echo($tid) ?></li>
        <li>결제 수단 : <?php echo($payMethod) ?></li>
        <li>취소 거래 시간 : <?php echo($authDate); ?></li>
        <li>결과 내용 : [<?php echo($resultCode) ?>] <?php echo($resultMsg); ?></li>
        <li>취소 금액 : <?php echo($cancelAmt) ?></li>
        <li>가맹점 ID : <?php echo($mid)?></li>
        <li>에러코드 : <?php echo($errorCD)?></li>
        <li>에러메시지 : <?php echo($errorMsg)?></li>
        <li>거래상태코드(0: 승인, 1:전취소, 2:후취소) : <?php echo($stateCD)?></li>
        <li>취소 번호 : <?php echo($CancelNum)?></li>
        <li>부분취소 가능여부(0: 불가, 1: 가능) : <?php echo($stateCD)?></li>
        <li>취소종류(0: 전취소, 1: 후취소) : <?php echo($PreCancelCode)?></li>
    </ul>
    
</body>
</html>
