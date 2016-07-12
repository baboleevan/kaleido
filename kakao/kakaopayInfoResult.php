<?php
include "incKakaopayCommon.php";
include "lgcns_CNSpay.php";

$connector = new CnsPayWebConnector($LogDir);

// 상점키 암호화
$ediDate = date("YmdHis");
$TID = $_REQUEST["TID"];
$md_src = $ediDate.$MID.$TID;
$salt = hash("sha256",$merchantKey.$md_src,false);
$hash_input = $connector->makeHashInputString($salt);
$hash_calc = hash("sha256", $hash_input, false);
$hash_String = base64_encode($hash_calc);

//결제 처리 경로(DB 또는 Config 파일로 관리한다.)
$connector->CnsActionUrl($CnsPayDealRequestUrl);
$connector->CnsPayVersion($phpVersion);
// 요청 페이지 파라메터 셋팅
$connector->setRequestData($_REQUEST);

$connector->addRequestData("actionType", "CI0");
$connector->addRequestData("PayMethod", "TID_INFO");

$connector->addRequestData("MID", $MID);
$connector->addRequestData("EncodeKey", $merchantKey);
$connector->addRequestData("EdiDate", $ediDate);
$connector->addRequestData("EncryptData", $hash_String);

// CNSPAY Lite 서버 접속하여 처리
$connector->requestAction();
// 결과 처리
$paySuccess = false;												// 결제 상태 조회 성공 여부
$resultCode = $connector->getResultData("ResultCode");	// 결과코드 (정상 :00 , 그 외 에러)
$resultMsg = $connector->getResultData("ResultMsg");  	// 결과메시지
$TID = $connector->getResultData("TID");   					// 거래 ID
$StateCd = $connector->getResultData("StateCd");   			// 거래상태 (0:승인, 1:전체취소, 2:부분취소 - 여러 건 부분취소 후 잔액이 0원일 경우 1:전체취소 로 응답)
$AppAmt = $connector->getResultData("AppAmt");   		// 승인금액
$CcAmt = $connector->getResultData("CcAmt");   			// 취소금액
$RemainAmt = $connector->getResultData("RemainAmt");   // 승인잔액
$CancelYn = $connector->getResultData("CancelYn");   		// 요청 취소건 취소결과 (Y:성공, N:실패)

$resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
if ($resultCode != null) {
	if ($resultCode == "00") {
		$paySuccess = true;		// 결제 상태 조회 성공 여부
	}	
}

$StateNm = "";
if ($paySuccess) {
    // 결제 상태 조회 성공시 DB처리 하세요.
	if ($StateCd == "0") {
		$StateNm = "승인";
	} else if ($StateCd == "1") {
		$StateNm = "전체취소";
	} else if ($StateCd == "2") {
		$StateNm = "부분취소";
	}  
} else {
   $StateNm = "처리실패";
   // 결제 상태 조회 실패시 DB처리 하세요.
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
 <head>
	<title>CNSPay 결제 상태 조회 결과 샘플 페이지</title>
</head>
<body>

    <b>CNSPay 결제 상태 조회 내역입니다</b><br />
    <ul>
        <li>결과 내용 : [<?php echo($resultCode); ?>] <?php echo($resultMsg); ?></li>
        <li>TID : <?php echo($TID); ?></li>
        <li>거래상태 : <?php echo($StateCd); ?> : <?php echo($StateNm) ?></li>
        <li>승인금액 : <?php echo($AppAmt); ?> 원</li>
        <li>취소금액 : <?php echo($CcAmt); ?> 원</li>
        <li>잔여금액 : <?php echo($RemainAmt); ?> 원</li>
        <li>요청취소건결과 : <?php echo($CancelYn); ?></li>
    </ul>
    
</body>
</html>