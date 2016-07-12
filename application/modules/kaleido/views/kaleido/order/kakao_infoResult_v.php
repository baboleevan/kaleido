<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
			 <ul>
				<li>결과 내용 : [<?php echo($resultCode); ?>] <?php echo($resultMsg); ?></li>
				<li>TID : <?php echo($TID); ?></li>
				<li>거래상태 : <?php echo($StateCd); ?> : <?php echo($StateNm) ?></li>
				<li>승인금액 : <?php echo($AppAmt); ?> 원</li>
				<li>취소금액 : <?php echo($CcAmt); ?> 원</li>
				<li>잔여금액 : <?php echo($RemainAmt); ?> 원</li>
				<li>요청취소건결과 : <?php echo($CancelYn); ?></li>
			</ul>
		</div>
		<!-- End .content-inner -->
	</div>
	<!-- End .content-wrapper -->
	<div class="clearfix"></div>
</div>
<!-- End #content -->
<script></script>
