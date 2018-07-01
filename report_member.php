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
<title>顧客購買明細書管理</title>
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
function getinfo(){
window.open('member_list.php?form=form1&field=member','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=500,top=100,left=320,scrollbars=yes');
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
      <td><strong>&nbsp;顧客購買明細書</strong>&nbsp;&nbsp;<a href="report_member.php?type=day">日報</a> | <a href="report_member.php?type=month">月報</a> | <a href="report_member.php?type=year">年報</a>&nbsp;&nbsp;<input type="button" onClick="preview(1);" value=" 顧客購買明細書印刷 "></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
     <?php
	  if($type=='')$type='month';
	  switch($type){
	   case 'day':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr height="40">
		<form action="report_member.php?action=save&type=day" name="form1" method="post">
		 <td id="row_style" colspan="10">
		 顧客名：<input type="text" size="15" name="member" value="<?php echo ($action=='save')?$member:''?>">&nbsp;&nbsp;<img src="images/03.gif" border="0" style="cursor:hand;" alt="顧客情報" onclick="getinfo()">
		 日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;<select name="finish"><option value="" <?php echo $finish==""?"selected":"";?>>全表示</option><option value="0" <?php echo $finish=="0"?"selected":"";?>>未払い</option><option value="1" <?php echo $finish=="1"?"selected":"";?>>払い</option></select>&nbsp;
		 <input type="submit" value=" 顧客購買明細書日報レビュー ">
		 </td>
	    </tr>
	   <?php
	   break;
	   case 'month':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr height="40">
		<form action="report_member.php?action=save&type=month" name="form1" method="post">
		 <td id="row_style" colspan="10">
		 顧客名：<input type="text" size="15" name="member" value="<?php echo ($action=='save')?$member:''?>">&nbsp;&nbsp;<img src="images/03.gif" border="0" style="cursor:hand;" alt="More..." onclick="getinfo()">
		 月を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;<select name="finish"><option value="" <?php echo $finish==""?"selected":"";?>>全表示</option><option value="0" <?php echo $finish=="0"?"selected":"";?>>未払い</option><option value="1" <?php echo $finish=="1"?"selected":"";?>>払い</option></select>&nbsp;
		 <input type="submit" value=" 顧客購買明細書月報レビュー ">
		 </td>
	    </tr>
	   <?php
	   break;
	   case 'year':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr height="40">
		<form action="report_member.php?action=save&type=year" name="form1" method="post">
		 <td id="row_style" colspan="10">
		 顧客名：<input type="text" size="15" name="member" value="<?php echo ($action=='save')?$member:''?>">&nbsp;&nbsp;<img src="images/03.gif" border="0" style="cursor:hand;" alt="More..." onclick="getinfo()">
		 年を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;<select name="finish"><option value="" <?php echo $finish==""?"selected":"";?>>全表示</option><option value="0" <?php echo $finish=="0"?"selected":"";?>>未払い</option><option value="1" <?php echo $finish=="1"?"selected":"";?>>払い</option></select>&nbsp;
		 <input type="submit" value=" 顧客購買明細書年報レビュー ">
		 </td>
	    </tr>
		<?php
	   break;
	   }
		if($action=='save'){
        $sql=new dedesql(false);
		$rb=$sql->getone("select id from #@__sale where member='$member'");
		if($rb['id']==''){
		showmsg('該当レコードがありません。','-1');
		exit();
		}
		$plist=new datalist();
		$plist->pageSize = $cfg_record;
		
		$sday=date('Y-m-d',strtotime($sday));
		if($finish=="")
		 $fstr="";
		 else
		 $fstr=" and #@__reportsale.finish='$finish'";
		switch($type){
case "day":
$query="select * 
        from #@__sale,#@__reportsale 
		where year(#@__reportsale.r_date)=year('$sday') and month(#@__reportsale.r_date)=month('$sday') and day(#@__reportsale.r_date)=day('$sday') 
		and #@__sale.rdh=#@__reportsale.r_dh 
		and #@__sale.member='$member' 
		".$fstr;
$report_title="顧客購買明細書日報";
break;
case "month":
$query="select * 
        from #@__sale,#@__reportsale 
		where year(#@__reportsale.r_date)=year('$sday') and month(#@__reportsale.r_date)=month('$sday') 
		and #@__sale.rdh=#@__reportsale.r_dh 
		and #@__sale.member LIKE '%".$member."%' 
		".$fstr;
$report_title="顧客購買明細書月報";
break;
case "year":
$query="select * 
        from #@__sale,#@__reportsale 
		where year(#@__reportsale.r_date)=year('$sday') 
		and #@__sale.rdh=#@__reportsale.r_dh 
		and #@__sale.member LIKE '%".$member."%' 
		".$fstr;
$report_title="顧客購買明細書年報";
break;
}
$p_name=GetCookie('VioomaUserID');
$p_date=GetDateMk(time());
$worker=$staff;
$sql->setquery($query);
$sql->execute();
while($rs=$sql->getArray()){
$allmoney+=$rs['number']*$rs['cp_sale'];
$alln+=$rs['number'];
}
$plist->SetParameter("type",$type);
$plist->SetParameter("action",$action);
$plist->SetParameter("sday",$sday);
$plist->SetSource($query);
$p_rtitle= "<tr class='row_report_head'>
<td>品番</td>
<td>名称</td>
<td>規格</td>
<td>単位</td>
<td>単価</td>
<td>番号</td>
<td>数量</td>
<td>金額</td>
</tr>";
$mylist = $plist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   $row1=$sql->getone("select * from #@__basic where cp_number='$row[productid]'");
	   $n+=$row['number'];
	   $money+=$row['number']*$row['sale'];
	   $p_string=$p_string."<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n
	   <td><center>".$row['productid']."</td>\r\n
	   <td><center>".$row1['cp_name']."</td>\r\n
	   <td><center>".$row1['cp_gg']."</td>
	   <td><center>".get_name($row1['cp_dwname'],'dw')."</td>
	   <td><center>￥".$row['sale']."</td>\r\n
	   <td><center>".$row['rdh']."</td>
	   <td><center>".number_format($row['number'],2,'.',',')."</td>
	   <td><center>￥".number_format($row['number']*$row['sale'],2,'.',',')."</td>
	   </tr>";
	   }

	   $p_string="<table width='100%' id='report_table' border='1' cellspacing='0' cellpadding='0'>". $p_rtitle .$p_string. "<tr>\r\n<td>&nbsp;&nbsp;小  計：</td><td colspan='5'>&nbsp;</td><td><center>".number_format($n,2,'.',',')."</td><td><center>￥".number_format($money,2,',','.')."</td>\r\n</tr>\r\n
	   <tr><td>&nbsp;&nbsp;合  計：</td><td colspan='4'><center>数量：".$alln."</td><td colspan='3'><center>金額：￥".number_format($allmoney,2,'.',',')."</td><td colspan='2'></td></tr>
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
require(dirname(__FILE__)."/templets/t_member.html");
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
