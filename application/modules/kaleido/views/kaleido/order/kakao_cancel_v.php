<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
			<form name="tranMgr" method="post" action="<?=KAKAO_URL?>/kakaopayCancelResult">
		
    <b>취소 요청 파라미터 입니다</b><br />
    <ul>
        <li>(*)MID : <input type="text" name="MID" value="<?php echo($MID); ?>" maxlength="30" /></li>
        <li>(*)TID : <input type="text" name="TID" value="" maxlength="30" /></li>
        <li>(*)취소 금액 : <input type="text" name="CancelAmt" value="" /></li>
        <li>공급가액 : <input type="text" name="SupplyAmt" value="0" /></li>
        <li>부가세 : <input type="text" name="GoodsVat" value="0" /></li>
        <li>봉사료 : <input type="text" name="ServiceAmt" value="0" /></li>
        <li>(*)취소사유 : <input type="text" name="CancelMsg" value="고객 요청" /></li>
        <li>(*)부분취소 여부 :
            <select name="PartialCancelCode">
                <option value="0">전체 취소</option>
                <option value="1">부분 취소</option>
            </select>
        </li>
        <li>취소요청자IP : <input type="text" name="CancelIP" value="" /></li>
        <li>취소번호 : <input type="text" name="CancelNo" value="" /></li>
        <li>부분취소 가능 잔액 : <input type="text" name="CheckRemainAmt" value="" /></li>
        <li>취소종류 : <input type="text" name="PreCancelCode" value="" /> (0: 전취소, 1: 후취소)</li>
    </ul>
    
    
    <div class="btns">
        <a href="javascript:goCancel();">취소하기</a>
    </div>
    
	</form>
		</div>
		<!-- End .content-inner -->
	</div>
	<!-- End .content-wrapper -->
	<div class="clearfix"></div>
</div>
<!-- End #content -->
<script></script>
