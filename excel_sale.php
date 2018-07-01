<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
$row=new dedesql(false);
switch($type){
case "day":
$query="select a.productid, b.cp_name, b.cp_title, a.sale, sum(number) number
 from #@__sale a,#@__basic b where and a.del_flag = 0 and a.s_type = $s_type and to_days(a.dtime)>=to_days('$sday') and to_days(a.dtime)<=to_days('$eday') and a.productid=b.cp_number 
 GROUP BY a.productid, b.cp_name, b.cp_title, a.sale";
$report_title=iconv("utf-8", "Shift_jis"," 販売レポート_日報 ");
break;
case "week":
$query="select * from #@__sale,#@__basic where week(#@__sale.dtime)='$sday' and #@__sale.productid=#@__basic.cp_number";
$report_title=" 販売レポート_週報 ";
break;
case "month":
$query="select * from #@__sale,#@__basic where YEAR(#@__sale.dtime)=YEAR('$sday') and month(#@__sale.dtime)=month('$sday') and #@__sale.productid=#@__basic.cp_number";
$report_title=" 販売レポート_月報 ";
break;
case "year":
$query="select * from #@__sale,#@__basic where YEAR(#@__sale.dtime)=YEAR('$sday') and #@__sale.productid=#@__basic.cp_number";
$report_title=" 販売レポート_年報 ";
break;
case "other":
$query="select * from #@__sale,#@__basic where #@__sale.rdh='$sday' and #@__sale.productid=#@__basic.cp_number";
$report_title=" 顧客販売一覧表 ";
break;
}
$row->setquery($query);
$row->execute();
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=excel_rk.xls");
echo iconv("utf-8", "Shift_jis","商品コード\tメーカー・商品名\tタイトル\t販売単価\t販売表番号\t数量\t金額\t\n");
while($rs=$row->getArray()){
$allmoney+=$rs['number']*$rs['sale'];
$alln+=$rs['number'];
echo $rs['productid']."\t".iconv("utf-8", "Shift_jis",$rs['cp_name'])."\t".iconv("utf-8", "Shift_jis",$rs['cp_title'])."\t".number_format($rs['sale'])."\t".$rs['rdh']."\t".$rs['number']."\t￥".number_format($rs['number']*$rs['sale'])."\t\n";
// echo iconv("utf-8", "Shift_jis",$rs['productid']."\t".$rs['cp_name']."\t￥".$rs['cp_title']."\t".number_format($rs['sale'])."\t".$rs['rdh']."\t".$rs['number']."\t￥".number_format($rs['number']*$rs['sale'])."\t\n");
}
echo iconv("utf-8", "Shift_jis","合   計\t\t\t\t\t数量：".$alln."\t金額：￥".number_format($allmoney)."\t\n");
?>