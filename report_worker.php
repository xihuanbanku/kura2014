<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/page.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="include/calendar.js"></script>
<title>社員給料レポート管理</title>
<script language=javascript>    
function preview(oper)
{
if (oper < 10){
bdhtml=window.document.body.innerHTML;
sprnstr="<!--startprint"+oper+"-->";
eprnstr="<!--endprint"+oper+"-->";
prnhtml=bdhtml.substring(bdhtml.indexOf(sprnstr)+18);

prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));
window.document.body.innerHTML=prnhtml;
window.print();
window.document.body.innerHTML=bdhtml;
} 
else {
window.print();
}
}
</script> 
</head>
<body>
<table width="100%" border="0" id="table_style_all" cellpadding="0" cellspacing="0">
  <tr>
    <td id="table_style" class="l_t">&nbsp;</td>
    <td>&nbsp;</td>
    <td id="table_style" class="r_t">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<table width="100%" border="0" cellpadding="0" cellspacing="2">
     <tr>
      <td><strong>&nbsp;社員給料レポート管理</strong>&nbsp;&nbsp; <a href="report_worker.php?type=month">月報</a> | <a href="report_worker.php?type=year">年報</a>&nbsp;&nbsp;<input type="button" onClick="preview(1);" value=" 社員給料レポート印刷 "></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
     <?php
	  if($type=='')$type='month';
	  switch($type){
	   case 'month':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr height="40"><form action="report_worker.php?action=save&type=month" name="form1" method="post">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" onclick="setday(this)" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">(日付選択)&nbsp;&nbsp;<input type="submit" value=" 月報レビュー "></td>
	    </tr>
	   <?php
	   break;
	   case 'year':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr height="40"><form action="report_worker.php?action=save&type=year" name="form1" method="post">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" onclick="setday(this)" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">(日付選択)&nbsp;&nbsp;<input type="submit" value=" 年報レビュー "></td>
	    </tr>
		<?php
	   break;
	   }
		if($action=='save'){
        $row=new dedesql(false);
		$plist=new datalist();
		$plist->pageSize = $cfg_record;
		
		switch($type){
case "month":
$query="select * from #@__wages,#@__basic where month(#@__wages.dtime)=month('$sday') and #@__wages.productid=#@__basic.cp_number";
$query1="select * from #@__wages,#@__basic where month(#@__wages.dtime)=month('$sday') and #@__wages.productid=#@__basic.cp_number";
$report_title="社員給料レポート_月報";
break;
case "year":
$query="select * from #@__wages,#@__basic where YEAR(#@__wages.dtime)=YEAR('$sday') and #@__wages.productid=#@__basic.cp_number";
$query1="select * from #@__wages,#@__basic where year(#@__wages.dtime)=year('$sday') and #@__wages.productid=#@__basic.cp_number";
$report_title="社員給料レポート_年報";
break;
case "other":
$query="select * from #@__wages,#@__basic where to_days(#@__wages.dtime)=to_days('$sday') and #@__wages.productid=#@__basic.cp_number";
$report_title="社員給料一覧表";
break;
}
$p_name=Getcookie('VioomaUserID');
$p_date=GetDateMk(time());
$row->setquery($query1);
$row->execute();
while($rs=$row->getArray()){
$allmoney+=$rs['number']*$rs['cp_jj'];
$alln+=$rs['number'];
}
$row->close();
$plist->SetParameter("type",$type);
$plist->SetParameter("action",$action);
$plist->SetParameter("sday",$sday);
$plist->SetSource($query);
$p_rtitle= "<tr class='row_report_head'><td>品番</td><td>名称</td><td>規格</td><td>分類</td><td>単位</td><td>販売単価</td><td>仕入先</td><td>廃棄番号</td><td>廃棄数</td><td>金額</td></tr>";
$mylist = $plist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   $n+=$row['number'];
	   $money+=$row['number']*$row['cp_jj'];
	   $p_string=$p_string."<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n<td>".$row['productid']."</td>\r\n<td>&nbsp;".$row['cp_name']."</td>\r\n<td>".$row['cp_gg']."</td>\r\n<td>".get_name($row['cp_categories'],'categories').">".get_name($row['cp_categories_down'],'categories')."</td>\r\n<td>".get_name($row['cp_dwname'],'dw')."</td><td>￥".$row['cp_jj']."</td>\r\n<td>".get_name($row['productid'],'gys')."</td>\r\n<td>".$row['rdh']."</td>\r\n<td>".$row['number']."</td>\r\n<td>￥".$row['number']*$row['cp_jj']."</td>\r\n</tr>";
	   }
	   $p_string="<table width='100%' id='report_table' border='1' cellspacing='0' cellpadding='0'>". $p_rtitle .$p_string. "<tr>\r\n<td>&nbsp;&nbsp;小  計：</td><td colspan='5'>&nbsp;</td><td colspan='2'>数量：".$n."</td><td colspan='2'>金額：￥".$money."</td>\r\n</tr>\r\n
	   <tr><td>&nbsp;&nbsp;合  計：</td><td colspan='5'>&nbsp;</td><td colspan='2'>数量：".$alln."</td><td colspan='2'>金額：￥".number_format($allmoney,2,'.',',')."</td></tr>
	   </table>";	
	   $p_pagestring=$plist->GetPageList($cfg_record);
		}
		?>
	   </table><?php if($action=='save'){?>
	   <table width="100%" cellspacing="0" cellpadding="0">
	    <tr>
		 <td>
   	  <!--startprint1-->
<?php 
require(dirname(__FILE__)."/templets/t_worker.html");
?>
	   <!--endprint1-->
	     </td>
		</tr>
	   </table>
	   <?php } ?>
	  </td>
     </tr></form>
    </table>
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td id="table_style" class="l_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td id="table_style" class="r_b">&nbsp;</td>
  </tr>
</table>
<?php
copyright();
?>
</body>
</html>
