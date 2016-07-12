<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
			<form name="payForm" action="<?=KAKAO_URL?>/kakaopayInfoResult"  method="post">

				<b>상태조회 파라미터 입니다</b><br />
				<ul>
					<li>(*)TID : <input type="text" name="TID" value="" maxlength="30" /></li>
					<li>취소번호 : <input type="text" name="CancelNo" value="111" maxlength="30" /></li>
				</ul>
				
				<div class="btns">
					<a href="javascript:cnspaySubmit();">결제 상태 조회</a>
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
