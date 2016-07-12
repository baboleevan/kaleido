<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
			<div class="ord-ordpaper-section">
				<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
				<form id="pay_post" method="post" action="">		
					
					<input name="buyertel" id="buyertel" type="hidden" value="">
					<input name="buyeremail" id="buyeremail" type="hidden" value="">
					

					<table class="table" style="border-top : 0px;" summary="주문자 이름, 연락처(휴대폰), 이메일, 비밀번호 입력필드 제공" style="width:100%">
						<colgroup>
							<col style="width: 110px;">
							<col style="width: 338px;">
							<col style="width: 110px;">
							<col style="width: 339px;">
						</colgroup>
						<tbody>
							<tr class="">
								<th scope="row">결재방식</th>
								<td colspan="3">
								<div class="rounding-outline ord-ordpaper-section">

									<input type="radio" id="methodtype1" value="Card" name="methodtype" >
									<label for="methodtype1">신용카드</label>

									<input type="radio" id="methodtype2" value="DirectBank" name="methodtype">
									<label for="methodtype2">실시간계좌이체</label>

									<input type="radio" id="methodtype3" value="Vbank" name="methodtype" checked="checked">
									<label for="methodtype3">무통장입금(가상계좌)</label>

									<input type="radio" id="methodtype4" value="Hpp" name="methodtype">
									<label for="methodtype4">휴대폰</label>
								</div>					
							</tr>

							<tr class="">
								<th scope="row">주문자 이름</th>
								<td colspan="3">
								<input name="buyername" id="buyername" class="form-control" type="text" value="이한복">
								<span class="guide">(성과 이름을 붙여 주세요)</span></td>
							</tr>
							<tr class="">
								<th scope="row">주문가격</th>
								<td colspan="3">
								<input type="text" name="price" id="price" class="form-control" value="2000">
								<span class="guide">(주문가격)</span></td>
							</tr>

							<tr>
								<th scope="">휴대폰번호</th>
								<td class="row">									
									<div class="col-lg-4 col-md-4">
									<select class="form-control " id="mobile1">
										<option selected>010</option>
										<option>011</option>
										<option>016</option>
										<option>018</option>
										<option>019</option>
									</select>
									</div>
									<div class="col-lg-4 col-md-4">										
										<input type="text" id="mobile2" class="form-control"  value="1111" maxlength="4">
									</div>
									<div class="col-lg-4 col-md-4">
										<input type="text" id="mobile3" class="form-control" value="2222" maxlength="4">
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row">이메일</th>
								<td class="row">									
										<div class="col-lg-4 col-md-4">
											<input type="text" id="email1" class="form-control" value="<?=$mail_id ?>" <?=$readonly?>>
										</div>
										
										<div class="col-lg-4 col-md-4">
											<input type="text" id="email2" class="form-control" value="<?=$mail_domain ?>" <?=$readonly?>>
										</div>
										<div class="col-lg-4 col-md-4">								
											<select class="form-control " id="emails" onchange="email_select()" <?=$disabled?>>
												<option>선택하세요</option>
												<option>chol.com</option>
												<option>dreamwiz.com</option>
												<option>empal.com</option>
												<option>freechal.com</option>
												<option>gmail.com</option>
												<option>hanafos.com</option>
												<option>hanmail.net</option>
												<option>hanmir.com</option>
												<option>hotmail.com</option>
												<option>korea.com</option>
												<option>nate.com</option>
												<option>naver.com</option>
												<option>netian.com</option>
												<option>paran.com</option>
												<option>sayclub.com</option>
												<option>yahoo.co.kr</option>
												<option>yahoo.com</option>
												<option>직접입력</option>
											</select>
									</div>
								</td>									
							</tr>
						</tbody>
					</table>
				</form>
			</div>
			
			<div id="pay_load"></div><!-- load pay parameter -->
			
			<div class="row mb40 text-center">
			<button type="button" class="btn btn-lg bg_red2 w280 mb10"id="pay_go">결제</button>
			<button type="button" class="btn btn-lg  w280 mb10 ml5 ml0-xs" id="pay_cansle">이전/취소</button>
		</div>

<script>
$(document).ready(function () {
	$('input:radio[name=methodtype]').change(function() {
		$("#gopaymethod").val($(this).val());
	});
});
	 
</script>
	
		
		</div>
		<!-- End .content-inner -->
	</div>
	<!-- End .content-wrapper -->
	<div class="clearfix"></div>
</div>
<!-- End #content -->