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
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>仕入先へ返品レポート</title>
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
function out_excel(sign){
edate=document.forms[0].sday.value;
window.open('excel_b_gys.php?type='+sign+'&sday='+edate,'','');
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
      <td><strong>&nbsp;仕入先へ返品レポート</strong>&nbsp;&nbsp;<a href="report_b_gys.php?type=day">日報</a> | <a href="report_b_gys.php?type=week">週報</a> | <a href="report_b_gys.php?type=month">月報</a> | <a href="report_b_gys.php?type=year">年報</a>&nbsp;&nbsp;<input type="button" onClick="preview(1);" value=" 印刷 "></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
     <?php
	  if($type=='')$type='day';
	  switch($type){
	   case 'day':
	  ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
       <form action="report_b_gys.php?action=save&type=day" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 日報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('day')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'week':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
       <form action="report_b_gys.php?action=save&type=week" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">週を選択してください&nbsp;&nbsp;
		 <?php
		 echo "<select name='sday'>";
		 for($i=1;$i<=52;$i++){
		 if($action=='save' && $i==$sday)
		 echo "<option value='$i' selected>第{$i}週</option>";
		 else
		 echo "<option value='$i'>第{$i}週</option>";
		 }
		 echo "</select>";
		 ?>
		 &nbsp;&nbsp;<input type="submit" value=" 週を選択してください ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('week')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'month':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
       <form action="report_b_gys.php?action=save&type=month" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 月報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('month')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'year':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
       <form action="report_b_gys.php?action=save&type=year" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 年報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('year')"></td>
	    </tr>
        </form>
		<?php
	   break;
	   case 'other':
	   ?>
	   <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <?php
	   break;
	   }
		if($action=='save'){
        $row=new dedesql(false);
		$plist=new datalist();
		$plist->pageSize = $cfg_record;
		
		switch($type){
		case "day":
$query="select * from #@__kcbackgys,#@__basic where to_days(#@__kcbackgys.dtime)=to_days('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$query1="select * from #@__kcbackgys,#@__basic where to_days(#@__kcbackgys.dtime)=to_days('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート日報";
break;
case "week":
$query="select * from #@__kcbackgys,#@__basic where week(#@__kcbackgys.dtime)='$sday' and #@__kcbackgys.productid=#@__basic.cp_number";
$query1="select * from #@__kcbackgys,#@__basic where week(#@__kcbackgys.dtime)='$sday' and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート週報";
break;
case "month":
$query="select * from #@__kcbackgys,#@__basic where month(#@__kcbackgys.dtime)=month('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$query1="select * from #@__kcbackgys,#@__basic where month(#@__kcbackgys.dtime)=month('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート月報";
break;
case "year":
$query="select * from #@__kcbackgys,#@__basic where YEAR(#@__kcbackgys.dtime)=YEAR('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$query1="select * from #@__kcbackgys,#@__basic where year(#@__kcbackgys.dtime)=year('$sday') and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="仕入先へ返品レポート年報";
break;
case "other":
$query="select * from #@__kcbackgys,#@__basic where #@__kcbackgys.rdh='$sday' and #@__kcbackgys.productid=#@__basic.cp_number";
$query1="select * from #@__kcbackgys,#@__basic where #@__kcbackgys.rdh='$sday' and #@__kcbackgys.productid=#@__basic.cp_number";
$report_title="購買返品レポート";
break;
}
$p_name=GetCookie('VioomaUserID');
$p_date=GetDateMk(time());
$dh=$sday;
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
$p_rtitle= "<tr class='row_report_head'>
<td>品番</td>
<td>名称</td>
<td>規格</td>
<td>分類</td>
<td>単位</td>
<td>仕入単価</td>
<td>仕入先</td>
<td>購買番号</td>
<td>返品数</td>
<td>金額</td>
</tr>";
$mylist = $plist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   $n+=$row['number'];
	   $money+=$row['number']*$row['cp_jj'];
	   $p_string=$p_string."<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n
	   <td>".$row['productid']."</td>\r\n
	   <td>&nbsp;".$row['cp_name']."</td>\r\n
	   <td>".$row['cp_gg']."</td>\r\n
	   <td>".get_name($row['cp_categories'],'categories').">".get_name($row['cp_categories_down'],'categories')."</td>\r\n
	   <td>".get_name($row['cp_dwname'],'dw')."</td>
	   <td>￥".$row['cp_jj']."</td>\r\n
	   <td>".get_name($row['productid'],'gys')."</td>\r\n
	   <td>".$row['idh']."</td>\r\n
	   <td>".$row['number']."</td>\r\n
	   <td>￥".$row['number']*$row['cp_jj']."</td>\r\n
	   </tr>";
	   }
	   $p_string="<table width='100%' id='report_table' border='1' cellspacing='0' cellpadding='0'>". $p_rtitle .$p_string. "<tr>\r\n<td>&nbsp;&nbsp;小　計：</td><td colspan='5'>&nbsp;</td><td colspan='2'>数量：".$n."</td><td colspan='2'>金額：￥".$money."</td>\r\n</tr>\r\n
	   <tr><td>&nbsp;&nbsp;合　計：</td><td colspan='5'>&nbsp;</td><td colspan='2'>数量：".$alln."</td><td colspan='2'>金額：￥".number_format($allmoney,2,'.',',')."</td></tr>
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
if($type=='other') 
require(dirname(__FILE__)."/templets/t_backgys_single.html");
else
require(dirname(__FILE__)."/templets/t_backgys.html");
?>
	   <!--endprint1-->
	     </td>
		</tr>
	   </table>
	   <?php } ?>
	  </td>
     </tr>
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
