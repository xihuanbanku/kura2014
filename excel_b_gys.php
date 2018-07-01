<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
$row=new dedesql(false);
switch($type){
case "day":
$query="select * from #@__kcbackgys,#@__basic where to_days(#@__kcbackgys.dtime)=to_days('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート_日報";
break;
case "week":
$query="select * from #@__kcbackgys,#@__basic where week(#@__kcbackgys.dtime)='$sday' and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート_週報";
break;
case "month":
$query="select * from #@__kcbackgys,#@__basic where month(#@__kcbackgys.dtime)=month('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート_月報";
break;
case "year":
$query="select * from #@__kcbackgys,#@__basic where YEAR(#@__kcbackgys.dtime)=YEAR('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート_年報";
break;
case "other":
$query="select * from #@__kcbackgys,#@__basic where #@__kcbackgys.rdh='$sday' and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="発注返品一覧表";
break;
}
$row->setquery($query);
$row->execute();
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:filename=excel_rk.xls");
echo "品番\t名称\t規格\t分類\t単位\t仕入単価\t仕入先\t発注番号\t数量\t金額\t\n";
while($rs=$row->getArray()){
$allmoney+=$rs['number']*$rs['cp_jj'];
$alln+=$rs['number'];
echo $rs['productid']."\t".$rs['cp_name']."\t".$rs['cp_gg']."\t".get_name($rs['cp_categories'],'categories').">".get_name($rs['cp_categories_down'],'categories')."\t".get_name($rs['cp_dwname'],'dw')."\t".$rs['cp_jj']."\t".get_name($rs['productid'],'gys')."\t".$rs['idh']."\t".$rs['number']."\t￥".$rs['number']*$rs['cp_jj']."\t\n";
}
echo "合   計\t\t\t\t\t\t\t\t数量：".$alln."\t金額：￥".$allmoney."\t\n";
?>