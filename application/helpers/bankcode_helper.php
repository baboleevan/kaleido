<? if (!defined('BASEPATH')) exit('No direct script access allowed');


/** inicis helper **/
function bank_names($find_bank_name){
	$bank_names['01']='한국은행';
	$bank_names['02']='산업은행';
	$bank_names['03']='기업은행';
	$bank_names['04']='국민은행';
	$bank_names['05']='외환은행';
	$bank_names['06']='국민은행';
	$bank_names['06']='국민은행(주택)';
	$bank_names['07']='수협중앙회';
	$bank_names['11']='농협';
	$bank_names['20']='우리은행';
	$bank_names['21']='조흥은행';
	$bank_names['23']='제일은행';
	$bank_names['25']='서울은행';
	$bank_names['26']='신한은행';
	$bank_names['27']='씨티은행';
	$bank_names['31']='대구은행';
	$bank_names['32']='부산은행';
	$bank_names['34']='광주은행';
	$bank_names['35']='제주은행';
	$bank_names['37']='전북은행';
	$bank_names['39']='경남은행';
	$bank_names['45']='새마을금고';
	$bank_names['48']='신협';
	$bank_names['71']='우체국';
	$bank_names['81']='하나은행';
	$bank_names['88']='통합신한은행';
	$bank_names['D1']='동양종합금융증권';
	$bank_names['D2']='현대증권';
	$bank_names['D3']='미래에셋증권';
	$bank_names['D4']='한국투자증권';
	$bank_names['D5']='우리투자증권';
	$bank_names['D6']='하이투자증권';
	$bank_names['D7']='HMC투자증권';
	$bank_names['D8']='SK증권';
	$bank_names['D9']='대신증권';
	$bank_names['DA']='하나대투증권';
	$bank_names['DB']='굿모닝신한증권';
	$bank_names['DC']='동부증권';
	$bank_names['DD']='유진투자증권';
	$bank_names['DE']='메리츠증권';
	$bank_names['DF']='신영증권';
	$bank_names['DH']='삼성증권';
	$bank_names['DM']='한화증권';
	$return = false;
	foreach($bank_names as $key => $val){
		if($find_bank_name == $key) $return = $val;
	}
	return $return;
}


//은행  이름으로 코드 추츨
function code($find_bank_name = null) {
	$bank_names['01']='한국은행';
	$bank_names['02']='산업은행';
	$bank_names['03']='기업은행';
	$bank_names['04']='국민은행';
	$bank_names['05']='외환은행';
	$bank_names['06']='국민은행';
	$bank_names['06']='국민은행(주택)';
	$bank_names['07']='수협중앙회';
	$bank_names['11']='농협';
	$bank_names['20']='우리은행';
	$bank_names['21']='조흥은행';
	$bank_names['23']='제일은행';
	$bank_names['25']='서울은행';
	$bank_names['26']='신한은행';
	$bank_names['27']='씨티은행';
	$bank_names['31']='대구은행';
	$bank_names['32']='부산은행';
	$bank_names['34']='광주은행';
	$bank_names['35']='제주은행';
	$bank_names['37']='전북은행';
	$bank_names['39']='경남은행';
	$bank_names['45']='새마을금고';
	$bank_names['48']='신협';
	$bank_names['71']='우체국';
	$bank_names['81']='하나은행';
	$bank_names['88']='통합신한은행';
	$bank_names['D1']='동양종합금융증권';
	$bank_names['D2']='현대증권';
	$bank_names['D3']='미래에셋증권';
	$bank_names['D4']='한국투자증권';
	$bank_names['D5']='우리투자증권';
	$bank_names['D6']='하이투자증권';
	$bank_names['D7']='HMC투자증권';
	$bank_names['D8']='SK증권';
	$bank_names['D9']='대신증권';
	$bank_names['DA']='하나대투증권';
	$bank_names['DB']='굿모닝신한증권';
	$bank_names['DC']='동부증권';
	$bank_names['DD']='유진투자증권';
	$bank_names['DE']='메리츠증권';
	$bank_names['DF']='신영증권';
	$bank_names['DH']='삼성증권';
	$bank_names['DM']='한화증권';
	$return = false;
	foreach($bank_names as $key => $val){
		if($find_bank_name == $val) $return = $val;
	}
	return $return;
}

//셀렉트 완성
function bank_select($name=false, $id=false, $class=false){
	$bank_names['01']='한국은행';
	$bank_names['02']='산업은행';
	$bank_names['03']='기업은행';
	$bank_names['04']='국민은행';
	$bank_names['05']='외환은행';
	$bank_names['06']='국민은행';
	$bank_names['06']='국민은행(주택)';
	$bank_names['07']='수협중앙회';
	$bank_names['11']='농협';
	$bank_names['20']='우리은행';
	$bank_names['21']='조흥은행';
	$bank_names['23']='제일은행';
	$bank_names['25']='서울은행';
	$bank_names['26']='신한은행';
	$bank_names['27']='씨티은행';
	$bank_names['31']='대구은행';
	$bank_names['32']='부산은행';
	$bank_names['34']='광주은행';
	$bank_names['35']='제주은행';
	$bank_names['37']='전북은행';
	$bank_names['39']='경남은행';
	$bank_names['45']='새마을금고';
	$bank_names['48']='신협';
	$bank_names['71']='우체국';
	$bank_names['81']='하나은행';
	$bank_names['88']='통합신한은행';
	$bank_names['D1']='동양종합금융증권';
	$bank_names['D2']='현대증권';
	$bank_names['D3']='미래에셋증권';
	$bank_names['D4']='한국투자증권';
	$bank_names['D5']='우리투자증권';
	$bank_names['D6']='하이투자증권';
	$bank_names['D7']='HMC투자증권';
	$bank_names['D8']='SK증권';
	$bank_names['D9']='대신증권';
	$bank_names['DA']='하나대투증권';
	$bank_names['DB']='굿모닝신한증권';
	$bank_names['DC']='동부증권';
	$bank_names['DD']='유진투자증권';
	$bank_names['DE']='메리츠증권';
	$bank_names['DF']='신영증권';
	$bank_names['DH']='삼성증권';
	$bank_names['DM']='한화증권';

	$html = "<select name='".$name."' id='".$id."'  class='".$class."'>";
	foreach($bank_names as $key => $val){
		$html = $html."<option value='".$key."'>".$val."</option>";
	}
	$html = $html."</select>";
	return  $html;
}

/** end inicis helper **/