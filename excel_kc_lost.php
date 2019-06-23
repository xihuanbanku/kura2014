<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
	   
if (!is_null($stext)) {
    $query="select * from #@__mainkc, #@__basic where #@__mainkc.number <'$stext' and #@__mainkc.p_id = #@__basic.cp_number ";
} else {
    $query="select * from #@__mainkc, #@__basic where #@__mainkc.number <5 and #@__mainkc.p_id = #@__basic.cp_number ";
}

// チェックボックス選択
if ($strChk != '') {
    $query .= " and #@__basic.cp_number in (".$strChk.")";
}

$query .= " order by cp_number";

$row=New Dedesql(false);
$row->setquery($query);
$row->execute();

//仕入用Excelファイル作成
$tab = "\t";
$newline = "\n";
$excel_data = "No".$tab."商品コード".$tab."商品名".$tab."状態".$tab."数量".$tab."仕様（電圧、容量、セル数）".$newline;
$count = 0;
while($rs=$row->getArray()){
    $count++;
    $kbn = mb_substr($rs['cp_number'], 0, 1);
    
    if ($kbn == "L") {
        $kbnNm = "純正";
    } else if ($kbn == "O") {
        $kbnNm = "互換";
    }
    $excel_data .= $count.$tab.$rs['cp_number'].$tab.strRepacreBrToSpace($rs['cp_title']).$tab.$kbnNm.$tab.''.$tab.strRepacreBrToSpace($rs['cp_gg']).$newline;
}

//出力ファイル名の作成
$excel_file = "仕入用データ_". date ( "YmdHis" ) .'.xls';
//MIMEタイプの設定
header("Content-Type: application/vnd.ms-excel");
//名前を付けて保存のダイアログボックスのファイル名の初期値
header("Content-Disposition: attachment;filename={$excel_file}");

// データの出力
echo($excel_data);
    
exit;
?>