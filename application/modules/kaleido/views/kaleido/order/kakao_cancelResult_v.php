<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
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
		</div>
		<!-- End .content-inner -->
	</div>
	<!-- End .content-wrapper -->
	<div class="clearfix"></div>
</div>
<!-- End #content -->
<script></script>
