<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
<title>CNSPay 결제 상태 조회 샘플 페이지</title>
<script type="text/javascript">

	function cnspaySubmit(){
		document.payForm.submit();
	}

</script>
</head>
<body>
<form name="payForm" action="kakaopayInfoResult.php"  method="post">

        <b>상태조회 파라미터 입니다</b><br />
        <ul>
            <li>(*)TID : <input type="text" name="TID" value="" maxlength="30" /></li>
            <li>취소번호 : <input type="text" name="CancelNo" value="111" maxlength="30" /></li>
        </ul>
        
        <div class="btns">
            <a href="javascript:cnspaySubmit();">결제 상태 조회</a>
        </div>
    </form>
</body>
</html>
