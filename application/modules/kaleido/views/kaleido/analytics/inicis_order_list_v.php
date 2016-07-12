<style type="text/css">
</style>

<script>
	$(document).ready(function (){
		$(".pagination a").click(function (event){
		  	event.stopPropagation ();
			$("#page").val($(this).data("num")); //검색 번호 유지
			$("#page_form").submit();
		});
		
		$("#stx").keydown(function (key) {
	        if(key.keyCode == 13) search();
		});

		$("#form_post").click(function (event){
			search();			
		});

		$("#reset").click(function (){
			$("#page").val("1");
			$("#sfl").val("");
			$("#stx").val("");
			$("#sdate").val("");
			$("#edate").val("");
			$("#page_form").submit();			
		});
		
		
		$.datepicker.regional['ko'] = {
			 closeText: '닫기',
			 prevText: '이전달',
			 nextText: '다음달',
			 currentText: '오늘',
			 monthNames: ['1월','2월','3월','4월','5월','6월',
			 '7월','8월','9월','10월','11월','12월'],
			 monthNamesShort: ['1월','2월','3월','4월','5월','6월',
			 '7월','8월','9월','10월','11월','12월'],
			 dayNames: ['일','월','화','수','목','금','토'],
			 dayNamesShort: ['일','월','화','수','목','금','토'],
			 dayNamesMin: ['일','월','화','수','목','금','토'],
			 //weekHeader: 'Wk',
			 dateFormat: 'yy-mm-dd',
			 firstDay: 0,
			 isRTL: false,
			 duration:200,
			 showMonthAfterYear: true,
			 autoSize: false, //오토리사이즈(body등 상위태그의 설정에 따른다)
			 changeMonth: true, //월변경가능
			 changeYear: true, //년변경가능
			 yearRange: '1990:2020',
			 yearSuffix: '년'
		};
		$.datepicker.setDefaults($.datepicker.regional['ko']);
		$("#sdate").datepicker();
		$("#edate").datepicker();               
	});

	//검색 반응
	function search(){
		$("#page").val("1"); //검색시 페이지를 1로 초기화
		$("#page_form").submit();
	}
</script>
<!-- Start #right-sidebar -->
<!-- Start #content -->
<div id="content">
	<!-- Start .content-wrapper -->
	<div class="content-wrapper">
		<!-- Start .content-inner -->
		<div class="content-inner">
			<div class="form-group">
			<form action="" id="page_form" method="get" >
				<input type="hidden" name="page"	id="page"	value="<?=(isset($input['page']) && $input['page']>=1)? $input['page'] : 1 ?>" />
				<input type="hidden" name="type" 	id="type" 	value="<?=(isset($input['type']) )? $input['type'] : "" ?>" />

				<div class="col-md-3">
				<select name="sfl" id="sfl" class="form-control">
					<option value="payMethod" <? if(isset($input['sfl']) && $input['sfl']=="payMethod") echo "selected";?>>결제수단</option>					
				</select>
				</div>
				<div class="col-md-9">
				<input type="text" name="stx" 		id="stx"  class="form-control"	value="<?=(isset($input['stx']) )? $input['stx'] : "" ?>" placeholder="결제수단" />
				</div>

				<div class="col-md-6">
				<input type="text" name="sdate" 	id="sdate"  class="input-small"	value="<?=(isset($input['sdate']) )? $input['sdate'] : "" ?>" placeholder="시작일" />
				<input type="text" name="edate" 	id="edate"  class="input-small"	value="<?=(isset($input['edate']) )? $input['edate'] : "" ?>" placeholder="종료일" />
				<a href="javascript:;" id="reset" class="btn">리셋</a>
				<a href="javascript:;" id="form_post" class="btn">검색</a>
				</div>
			</form>
			</div>

			<div class="table-responsive" style="overflow: hidden; width: 100%; height: auto;">
				<table class="table">
				<thead>
					<th>id</th>
					<th>TID</th>
					<th>결제수단</th>
					<th>결제코드</th>
					<th>결제메세지</th>
					<th>총결제금액</th>
					<th>승인시간</th>
					<th>결제 상태</th>
				</thead>
				<tbody>
						<?php			
						//page set
						$page = $input["page"];
						$pagelist = $input["pagelist"];
						if(is_numeric($page) == false) $page = 1;
						if($page < 0) $page = 1;

						$total_count = $total_count - (($page -1) * $pagelist);
						foreach ($lists as $key => $val): ?>
							<tr>
								<td><? echo $total_count--;?></td>								
								<td><?=$val['tid']?></td>
								<td><?=$val['payMethod']?></td>								
								<td><?=$val['resultCode']?></td>
								<td><?=$val['resultMsg']?></td>
								<td><?=$val['TotPrice']?></td>
								<td><?=$val['applDate']?> &nbsp;<?=$val['applTime']?></td>
								<td><?=$val['payment']?></td>
							</tr>
						<? endforeach;  ?>
					</tbody>
				</table>
			</div>

			<!-- Start .page-nation -->
			<div class="page-nation">
				<ul class="pagination">
					<?=$page_nation?>
				</ul>
			</div>
			<!-- End .page-nation-->
		</div>
	</div>
	<!-- End .content-wrapper -->
	<div class="clearfix"></div>
</div>
<!-- End #content -->
