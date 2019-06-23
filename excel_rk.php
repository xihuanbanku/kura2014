<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
$row=new dedesql(false);
switch($type){
case "day":
$query="select * from #@__kc a,#@__basic b where YEAR(a.dtime)=YEAR('$sday') and month(a.dtime)=month('$sday') and a.s_type = $s_type and to_days(a.dtime)=to_days('$sday') and a.productid=b.cp_number";
$report_title=" 入庫レポート_日報 ";
break;
case "week":
$query="select * from #@__kc a,#@__basic b where week(a.dtime)='$sday' and a.productid=b.cp_number";
$report_title=" 入庫レポート_週報 ";
break;
case "month":
$query="select * from #@__kc a,#@__basic b where YEAR(a.dtime)=YEAR('$sday') and month(a.dtime)=month('$sday') and a.productid=b.cp_number";
$report_title=" 入庫レポート_月報 ";
break;
case "year":
$query="select * from #@__kc a,#@__basic b where YEAR(a.dtime)=YEAR('$sday') and a.productid=b.cp_number";
$report_title=" 入庫レポート_年報 ";
break;
case "other":
$query="select * from #@__kc a,#@__basic b where a.rdh='$sday' and a.productid=b.cp_number";
$report_title=" 発注入庫一覧表 ";
break;
}
$row->setquery($query);
$row->execute();
$excel_file = "receipt_". date ( "YmdHis" ) .'.xls';
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename={$excel_file}");
echo "商品コード\t名称\t型番・詳細\t分類\t単位\t仕入単価\t仕入先\t倉庫\t入庫数\t金額\t\n";
while($rs=$row->getArray()){
$allmoney+=$rs['number']*$rs['rk_price'];
$alln+=$rs['number'];
echo " ".$rs['productid']."\t".$rs['cp_name']."\t".$rs['cp_gg']."\t".get_name($rs['cp_categories'],'categories').">".get_name($rs['cp_categories_down'],'categories')."\t".get_name($rs['cp_dwname'],'dw')."\t".$rs['rk_price']."\t".get_name($rs['productid'],'gys')."\t".get_name($rs['labid'],'lab')."\t".$rs['number']."\t￥".$rs['number']*$rs['rk_price']."\t\n";
}
echo "合   計\t\t\t\t\t\t\t\t数量：".$alln."\t金額：￥".$allmoney."\t\n";
?>