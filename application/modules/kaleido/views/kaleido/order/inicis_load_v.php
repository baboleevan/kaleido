<?

$time = time();
$offerPeriod = "";
$offerPeriod = date("Ymd") . "-" . date("Ymd", strtotime("+30 Day", $time));

//판매 후 30일 제품 보장
$vbank_date = date("Ymd", strtotime("+1 Week", $time));
//무통장 입금 결제일은 오늘 부터 1주일

$payViewType = "overlay";
//overlay popup  : 모바일은 new
if ($this -> agent -> is_mobile())	$payViewType = "new";

?>


<form name="real_pay_go" class="real_pay_go" method="post">
<input type="text" name="version" value="1.0">
<input type="text" name="mid" id="mid" value="<?=$mid ?>">
<input type="text" name="oid" id="oid" value="<?=$oid ?>">
<input type="text" name="timestamp" id="timestamp" value="<?=$timestamp ?>">
<input type="text" name="currency" value="WON">

<input type="text" name="returnUrl" value="<?=$inicis_returnUrl ?>">
<input type="text" name="popupUrl" id="popupUrl" value="<?=$inicis_popupUrl?>">
<input type="text" name="closeUrl" id="closeUrl" value="<?=$inicis_closeUrl?>">

<input type="text" name="price" id="price" value="<?=$price?>">
<input type="text" name="goodsname" id="goodsname" value="상품">

<input type="text" name="payViewType" id="payViewType" value="<?=$payViewType ?>">
<input type="text" name="mKey" id="mKey" value="<?=$mkey?>">
<input type="text" name="signature" id="signature" value="<?=$signature?>">
<input type="text" name="languageView" id="languageView" value="ko">

<!-- 필수값 -->
<input type="text" name="charset" id="charset">
<input type="text" name="gopaymethod" id="gopaymethod" value="<?=$input["methodtype"]?>">
<!-- 결재 선택 -->
<input type="text" name="acceptmethod" id="acceptmethod" value="HPP(1):vbank(<?=$vbank_date ?>):below1000">
<!-- 추가 옵션 선택 -->
<!-- 필수값으로 수정되었음. 콘텐트(1), 실물(2) -->
<input type="text" name="nointerest" id="nointerest">
<input type="text" name="quotabase" id="quotabase">
<input type="text" name="vbankRegNo" id="vbankRegNo">
<input type="text" name="offerPeriod" id="offerPeriod" value="">
<input type="text" name="merchantData" id="merchantData">

<!-- input -->
<input type="text" name="methodtype" id="methodtype" value="<?=$input["methodtype"]?>">
<input type="text" name="buyername" id="buyername" value="<?=$input["buyername"]?>">
<input type="text" name="buyeremail" id="buyeremail" value="<?=$input["buyeremail"]?>">
<input type="text" name="buyertel" id="buyertel" value="<?=$input["buyertel"]?>">
</form>