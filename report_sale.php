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
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>?r=<?php echo rand()?>"></script>
<title>販売レポート管理</title>
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
sdate=document.forms[0].sday.value;
edate=document.forms[0].eday.value;
window.open('excel_sale.php?type='+sign+'&sday='+sdate+'&eday='+edate+'&s_type=<?php echo $_REQUEST["s_type"]?>','','');
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
      <td><strong>&nbsp;販売レポート管理</strong>&nbsp;&nbsp;&nbsp;<input type="button" onClick="preview(1);" value="印刷 "></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
     <?php
	  if($type=='')$type='day';
	  switch($type){
	   case 'day':
	  ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
       <form action="report_sale.php?action=save&type=day&s_type=<?php echo $_REQUEST["s_type"]?>" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">
		 渠道:<select name="state_8"><option value="-1">全部</option>
	   <?php
            $row=new dedesql(false);
            $row->setquery("select s_name, s_value from jxc_state where parent_id =8");
            $row->execute();
            while($rs=$row->getArray()){
                if($state_8==$rs['s_value']) {
		          echo "<option value='{$rs['s_value']}' selected>{$rs['s_name']}</option>";
                } else {
		          echo "<option value='{$rs['s_value']}'>{$rs['s_name']}</option>";
                }
            }
		 ?>
		 </select>
		 日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateTimeMk(time());?>">-<input type="text" name="eday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$eday:GetDateTimeMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 日報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('day')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'week':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="report_sale.php?action=save&type=week" name="form1" method="post">
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
		 &nbsp;&nbsp;<input type="submit" value=" 週報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('week')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'month':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
       <form action="report_sale.php?action=save&type=month" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 月報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('month')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'year':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="report_sale.php?action=save&type=year" name="form1" method="post">
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
		    if($state_8 == "-1") {
                $query="select a.productid, '全部' s_name, b.cp_title, a.sale, sum(number) number
                 from #@__sale a,#@__basic b where a.del_flag = 0 and a.s_type = $s_type and to_days(a.dtime)>=to_days('$sday') and to_days(a.dtime)<=to_days('$eday') and a.productid=b.cp_number 
                 GROUP BY a.productid, b.cp_title, a.sale";
		    } else {
                $query="select a.productid, c.s_name, b.cp_title, a.sale, sum(number) number
                 from #@__sale a,#@__basic b, jxc_state c where a.del_flag = 0 and a.s_type = $s_type and a.member = c.s_value and a.member={$state_8} and to_days(a.dtime)>=to_days('$sday') and to_days(a.dtime)<=to_days('$eday') and a.productid=b.cp_number 
                 GROUP BY a.productid, a.member, b.cp_title, a.sale";
		    }
            $report_title="販売レポート日報";
            break;
        case "week":
            $query="select * from #@__sale,#@__basic where week(#@__sale.dtime)='$sday' and #@__sale.productid=#@__basic.cp_number";
            $report_title="販売レポート週報";
            break;
        case "month":
            $query="select * from #@__sale,#@__basic where month(#@__sale.dtime)=month('$sday') and #@__sale.productid=#@__basic.cp_number";
            $report_title="販売レポート月報";
            break;
        case "year":
            $query="select * from #@__sale,#@__basic where YEAR(#@__sale.dtime)=YEAR('$sday') and #@__sale.productid=#@__basic.cp_number";
            $report_title="販売レポート年報";
            break;
        case "other":
            $query="select * from #@__sale,#@__basic where #@__sale.rdh='$sday' and #@__sale.productid=#@__basic.cp_number";
            $report_title="販売レポート";
            break;
        }
        $p_name=GetCookie('VioomaUserID');
        $p_date=GetDateMk(time());
        if($type=='other'){
        $rad=$row->getone("select r_adid from #@__reportsale where r_dh='$sday'");
        $p_adid="担当者：".$rad['r_adid'];}
        else

    $row->setquery($query);
    $row->execute();
// $p_adid='';
// $plist->SetParameter("type",$type);
// $plist->SetParameter("action",$action);
// $plist->SetParameter("sday",$sday);
// $plist->SetParameter("eday",$eday);
// $plist->SetSource($query);
$i=0;
$p_rtitle= "<tr class='row_report_head'><td width='7%'>商品コード</td><td width='10%'>渠道</td><td colspan='3'>タイトル</td><td width='6%'>販売単価</td><td width='4%'>数量</td><td width='8%'>金額</td></tr>";
//$mylist = $plist->GetDataList();
    while($rs=$row->getArray()){
        $i++;
	   $n+=$rs['number'];
	   $money+=$rs['number']*$rs['sale'];
	   $p_string.="<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n"
                   . "<td>&nbsp;".$rs['productid']."</td>\r\n<td>&nbsp;".$rs['s_name']."</td>\r\n<td colspan=\"3\">&nbsp;".$rs['cp_title']."</td>\r\n"
                   . "<td align='right'>￥".number_format($rs['sale'])."&nbsp;</td>\r\n<td align='right'>".$rs['number']."&nbsp;</td>\r\n<td align='right'>￥".number_format($rs['number']*$rs['sale'])."&nbsp;</td>\r\n</tr>";
	}
	   $p_string="<table width='100%' id='report_table' border='1' cellspacing='0' cellpadding='0'>". $p_rtitle .$p_string. "
	   <tr><td>&nbsp;&nbsp;合　計：</td><td colspan='5'>&nbsp;</td><td align='right' colspan='1'>".$n."&nbsp;</td><td align='right' colspan='2'>￥".number_format($money)."&nbsp;</td></tr>
	   </table>";	
// 	   $p_pagestring=$plist->GetPageList($cfg_record);
		}
		?>
	   </table><?php if($action=='save'){?>
	   <table width="100%" cellspacing="0" cellpadding="0">
	    <tr>
		 <td>
   	  <!--startprint1-->
<?php 
require(dirname(__FILE__)."/templets/t_sale.html");
?>
	   <!--endprint1-->
	     </td>
		</tr>
	   </table>
	   <?php } ?>
	  </td>
     </tr>
    </table>
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
