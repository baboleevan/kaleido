<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<script>
		function setTxnId() 
		{
			var resultCode = "<?php echo($resultCode) ?>";
			var resultMsg = "<?php echo($resultMsg) ?>";
			var txnId = "<?php echo($txnId) ?>";
			var prDt = "<?php echo($prDt) ?>";
			// 가져온 결과를 payForm에 세팅
			parent.document.getElementById('resultCode').value = resultCode;
			parent.document.getElementById('resultMsg').value = resultMsg;
			parent.document.getElementById('txnId').value = txnId;
			parent.document.getElementById('prDt').value = prDt;
			parent.cnspay();
		}
	</script>
</head>
<body onload="setTxnId()">
</body>
</html>
