//order form
$(document).ready(function () {
	//취소
	$("#pay_cansle").click(function(){
		window.history.back();
	});

	//주문시작
	$('#pay_go').click(function () {

		var type = $('input:radio[name=methodtype]:checked').val();		
		//alert('pay go!');
		paygo(type);
		//alert();
		//inicisgo();
	});
	
	$('#methodtype5').click(function (){
		//alert('?');
	});
});

function paygo(type){
	if(type == 'Card') inicisgo();
	if(type == 'DirectBank') inicisgo();
	if(type == 'Vbank') inicisgo();
	if(type == 'Hpp') inicisgo();
	if(type == 'Kakao') kakao_load();
}


function inicisgo(){		
	if(check_inicis_values()) {
		var point = $("#point").val();
		var username = $("#buyername").val();		
		var price = $("#price").val();		
		var email = $("#email1").val()+"@"+$("#email2").val();
		var couponid = $("#couponid").val();
		var password = "";

		//var tel = $("mobile1").val()+"-"+$("mobile2").val()+"-"+$("mobile3").val();
		var tel = $("#mobile1").val()+$("#mobile2").val()+$("#mobile3").val();

		$("#buyertel").val(tel);
		$("#buyeremail").val(email);
		
		
		if(!$('input:radio[name=privacy-agree]:checked').val()){
			//alert("정책에 동의해 주셔야 합니다.");
			//return false;
		}
		
		if($("#is_login").val() == "F"){
			
			if(!$("#password").val()){
				alert("패스워드를 입력해 주십시오.");
				$("#password").focus();
				return false;
			}
			if(!$("#password_re").val()){
				alert("패스워드를 확인을 입력해 주십시오.");
				$("#password_re").focus();
				return false;
			}
			if($("#password").val() != $("#password_re").val()){
				alert("패스워드와 패스워드 확인 값이 일치 하지 않습니다.");
				$("#password").focus();
				return false;
			}
			password = $("#password").val();
		}

		//alert('ajax_load');

		var params = $("#pay_post").serialize();

		//$("#pay_post").submit();
		//return false;
		

		$.ajax({
			type: "POST",
			data: params,
			url: "/kaleido/page/ajax_inicis",
			dataType: "html",
			success: function(data){
			
				//한번 더 체크
				if(check_inicis_values()) {
					$("#pay_load").html(data);
					//alert(data);
					INIStdPay.pay('real_pay_go');					
					//alert("pay!");
				}
			}
		});
        
	}
}

function check_inicis_values() {

	var tel= $("#mobile1").val()+$("#mobile2").val()+$("#mobile3").val();
	var email="";
	var buyername="";
	
	if($('#buyername').val()=='') {
		alert('주문자 이름을 입력해주세요.');
		$('#buyername').focus();
		return false;
	}
	
	if($('#email1').val()!='' && $('#email2').val()!='') {
		email=$('#email1').val()+"@"+$('#email2').val();
	} else {
		alert('E-Mail을 입력해주세요');
		$('#email1').focus();
		return false;
	}
	$('#buyertel').val(tel);
	$('#buyeremail').val(email);
	
	if($("#price").val() < 1000){
		alert("1000원 이하는 결재 하 실수 없습니다.");
		return false;	
	}
	return true;
}

/**    Lgcns    **/
function kakao_load(){
	//alert('kakao_load');	
	var params = $("#pay_post").serialize();
		$.ajax({
			type: "POST",
			data: params,
			url: "/kaleido/page/kakao_form",
			dataType: "html",
			success: function(data){				
				$("#pay_load").html(data);
			}
		});
	//return false;
}

//kakaopay 정보조회
function cnspaySubmit(){
	document.payForm.submit();
}

//kakaopay 결제 취소
function goCancel() {
	document.tranMgr.submit();
}