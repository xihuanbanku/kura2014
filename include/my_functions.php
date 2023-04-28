<?php
require_once 'JSON.php';
function my_json_encode($phparr){
	if(function_exists("json_encode")){
		return json_encode($phparr, JSON_FORCE_OBJECT);
	}else{
		$json = new Services_JSON();
		return $json->encode($phparr);
	}
}
function my_json_decode($phparr){
	if(function_exists("json_decode")){
		return json_decode($phparr, true);
	}else{
		$json = new Services_JSON();
		return $json->decode($phparr, true);
	}
}

function delSpace($str) {
	return preg_replace('/(\s|　)/','',$str);
}

function strRepacre($str) {
	$str = SBC_DBC($str, 1);
	$str = preg_replace("/\r/", "<br>", $str);
	$str = preg_replace("/\n/", "<br>", $str);
	$str = preg_replace("/\r\n/", "<br>", $str);
	return $str;
}

function strRepacreBrToSpace($str) {
	$str = SBC_DBC($str, 1);
	$str = preg_replace("/\r/", " ", $str);
	$str = preg_replace("/\n/", " ", $str);
	$str = preg_replace("/\r\n/", " ", $str);
	return $str;
}

// 第一个参数：传入要转换的字符串
// 第二个参数：取0，半角转全角；取1，全角到半角
function SBC_DBC($str, $args2) {
	$DBC = Array(
		'０' , '１' , '２' , '３' , '４' ,
		'５' , '６' , '７' , '８' , '９' ,
		'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
		'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
		'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
		'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
		'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
		'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
		'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
		'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
		'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
		'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
		'ｙ' , 'ｚ' , '－' , '　' , '：' ,
		'．' , '，' , '／' , '％' , '＃' ,
		'！' , '＠' , '＆' , '（' , '）' ,
		'＜' , '＞' , '＂' , '＇' , '？' ,
		'［' , '］' , '｛' , '｝' , '＼' ,
		'｜' , '＋' , '＝' , '＿' , '＾' ,
		'￥' , '￣' , '｀' , '～'
	);
	$SBC = Array( // 半角
		'0', '1', '2', '3', '4',
		'5', '6', '7', '8', '9',
		'A', 'B', 'C', 'D', 'E',
		'F', 'G', 'H', 'I', 'J',
		'K', 'L', 'M', 'N', 'O',
		'P', 'Q', 'R', 'S', 'T',
		'U', 'V', 'W', 'X', 'Y',
		'Z', 'a', 'b', 'c', 'd',
		'e', 'f', 'g', 'h', 'i',
		'j', 'k', 'l', 'm', 'n',
		'o', 'p', 'q', 'r', 's',
		't', 'u', 'v', 'w', 'x',
		'y', 'z', '-', ' ', ':',
		'.', ',', '/', '%', '#',
		'!', '@', '&', '(', ')',
		'<', '>', '"', '\'','?',
		'[', ']', '{', '}', '\\',
		'|', '+', '=', '_', '^',
		'$', '~', '`', '~'
	);
	if ($args2 == 0) {
		return str_replace($SBC, $DBC, $str);  // 半角到全角
	} else if ($args2 == 1) {
		return str_replace($DBC, $SBC, $str);  // 全角到半角
	} else {
		return false;
	}
}

?>
