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
<title><?php echo $cfg_softname;?>会計管理</title>
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
      <td>
	   <table width="100%" border="0" cellspacing="0">
	   <form action="system_money.php?action=seek" name="form1" method="post">
	    <tr>
		 <td>
	  <strong>&nbsp;会計管理</strong> -<a href="add_money.php">口座手入力</a>
	     </td>
		 <td align="right">日付範囲：
		 <?php 
		 if($action=='seek'){
		 echo "<input type=\"text\" name=\"cp_sdate\" size=\"15\" VALUE=\"".$cp_sdate."\" class=\"Wdate\" onClick=\"WdatePicker()\"> ー 
		 <input type=\"text\" name=\"cp_edate\" size=\"15\" VALUE=\"".$cp_edate."\" class=\"Wdate\" onClick=\"WdatePicker()\">";
		 $hurl="system_money.php?action=seek&cp_sdate='$cp_sdate'&cp_edate='$cp_edate'&atype=";}
		 else{
		 echo "<input type=\"text\" name=\"cp_sdate\" size=\"15\" VALUE=\"\" class=\"Wdate\" onClick=\"WdatePicker()\"> ー 
		 <input type=\"text\" name=\"cp_edate\" size=\"15\" VALUE=\"\" class=\"Wdate\" onClick=\"WdatePicker()\">";
		 $hurl="system_money.php?atype=";}
		 ?>
		 <input type="submit" value="検索">
		 &nbsp;&nbsp;
		 </td>
		</tr></form>
	   </table>
	  </td>
     </tr>
	 <form method="post" name="sel">
     <tr>
      <td bgcolor="#FFFFFF">
       <?php
	   $asql=New dedesql(false);
	   $aquery="select sum(amoney) as imoney from #@__accounts where atype='収入'";
	   $aquery1="select sum(amoney) as omoney from #@__accounts where atype='支出'";
	   $asql->setquery($aquery);
	   $asql->execute();
	   $rs=$asql->getone();
	   $imoney=$rs['imoney'];
	   
	   $asql->setquery($aquery1);
	   $asql->execute();
	   $rs=$asql->getone();
	   $omoney=$rs['omoney'];
	   $asql->close();
	   if($imoney<$omoney)
	   $moneystring="損失：￥".($omoney-$imoney)."円，収入合計：￥".$imoney."，支出合計：￥".$omoney;
	   elseif($imoney-$omoney==0)
	   $moneystring="収支バランス，收入合計：￥".$imoney."，支出合計：￥".$omoney;
	   else
	   $moneystring="利益：￥".($imoney-$omoney)."円，收入合計：￥".$imoney."，支出合計：￥".$omoney;
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if($action=='seek'){
	   if($cp_sdate=='' || $cp_edate=='' || $cp_sdate=='' || $cp_edate=='' || $cp_sdate>$cp_edate)echo "<script>alert('正しい日付範囲を選択してください。');history.go(-1);</script>";
	    if($atype!='')
	   $query="select * from #@__accounts where dtime between '$cp_sdate' and '$cp_edate' and atype='$atype' order by dtime desc";
		else
	   $query="select * from #@__accounts where dtime between '$cp_sdate' and '$cp_edate' order by dtime desc";
	   }
	   else{
	   if($atype!='')
	   $query="select * from #@__accounts where atype='$atype' order by dtime desc";
	   else
       $query="select * from #@__accounts order by dtime desc";
	   }
$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;

if($action=='seek'){
$dlist->SetParameter("action",$action);
$dlist->SetParameter("cp_sdate",$cp_sdate);
$dlist->SetParameter("cp_edate",$cp_edate);
}
$dlist->SetSource($query);
       echo "<tr><td colspan='8' align='right'>".$moneystring."&nbsp;&nbsp;</td></tr>";
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>科目</td>
	   <td>口座</td>
	   <td>担当者</td>
	   <td>日付</td>
	   <td>金額</td>
	   <td>備考</td>
	   <!--<td>操作</td>-->
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   $cmoney+=$row['amoney'];
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n
	   <td><center>".$row['id']."</td>
	   <td><center>&nbsp;<a href='$hurl".$row['atype']."'>".$row['atype']."</td>
	   <td><center>".getbank($row['abank'])."</td>
	   <td><center>".$row['apeople']."</td>
	   <td><center>".$row['dtime']."</td>
	   <td><center>￥".$row['amoney']."</td>
	   <td><center>".$row['atext']."</td>
	   <!--<td><center><input type='checkbox' name='sel_pro".$row['id']."' value='".$row['id']."'></td>-->\r\n
	   </tr>";
	   }
	   echo "<tr><td colspan=\"8\">&nbsp;&nbsp;総計：&nbsp;￥".$cmoney."円</td></tr>";
	   echo "<tr><td colspan='8'>&nbsp;".$dlist->GetPageList($cfg_record)."</td></tr>";
	   echo "</table>";
	   $csql->close();
	   ?>
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
</body>
</html>
