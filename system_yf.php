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
<title><?php echo $cfg_softname;?>買掛金管理</title>
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
	   <form action="system_yf.php?action=seek" name="form1" method="post">
	    <tr>
		 <td>
	     <strong>&nbsp;買掛金</strong>
	     </td>
		 <td align="right">
		 仕入先：<select name="member"><option value="">仕入先一覧表示</option>
		 <?php
		 if($action=='sure'){
		 $sql=new dedesql(false);
		 $row=$sql->getone("select * from #@__bank where bank_default='1'");
		 $loginip=getip();
          $logindate=getdatetimemk(time());
          $username=GetCookie('VioomaUserID');
		 $query="update #@__reportrk set finish='1' where r_dh='$dh'";
		 $query1="insert into #@__accounts(atype,amoney,abank,dtime,apeople,atext) values('支出','$money','".$row['id']."','$logindate','$username','既払金、番号：$dh')";
		 $sql->executenonequery($query);
		 $sql->executenonequery($query1);
          WriteNote('番号：'.$dh.'の金を払いました。',$logindate,$loginip,$username);
		  $sql->close();
		  showMsg('払いました!','system_yf.php');
		 }
		 $sql1=new dedesql(false);
		 $q1="SELECT id,g_name FROM #@__gys ORDER BY id DESC";
		 $sql1->setquery($q1);
		 $sql1->execute();
		 while($r=$sql1->getArray()){
		 if($action=='seek')
		  if($r['g_name']==$member)
		 echo "<option value='".$r['g_name']."' selected>".$r['g_name']."</option>";
		  else
		 echo "<option value='".$r['g_name']."'>".$r['g_name']."</option>";  
		 else
		 echo "<option value='".$r['g_name']."'>".$r['g_name']."</option>";
		 }
		 $sql1->close();
		 ?>
		   </select>
		 日付範囲：
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
		 <input type="submit" value="買掛金確認">
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
	   $aquery="select * from #@__reportrk where finish='0'";
	   $asql->setquery($aquery);
	   $asql->execute();
	   $inumber=$asql->gettotalrow();
	   while($r1=$asql->getArray()){
	   $asql1=new dedesql(false);
	   $asql1->setquery("select * from #@__kc,#@__basic where #@__kc.rdh='".$r1['r_dh']."' and #@__kc.productid=#@__basic.cp_number");
	   $asql1->execute();
	   while($r2=$asql1->getArray()){
	   $imoney+=$r2['cp_jj']*$r2['number'];
	   }
	   $asql1->close();
	   }
	   $asql->close();
	   
	   $moneystring="<b>買掛金：".$inumber." 件、合計金額：￥".number_format($imoney,2,'.',',')."円</b>";
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if($action=='seek'){
	   if($cp_sdate=='' || $cp_edate=='' || $cp_sdate=='' || $cp_edate=='' || $cp_sdate>$cp_edate)echo "<script>alert('正しい日付範囲を選択してください。');history.go(-1);</script>";
	   $query="select * from #@__reportrk where r_date between '$cp_sdate' and '$cp_edate' and finish='0' order by r_date desc";
	   }
	   else
	   $query="select * from #@__reportrk where finish='0' order by r_date desc";
$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;

if($action=='seek'){
$dlist->SetParameter("action",$action);
$dlist->SetParameter("member",$member);
$dlist->SetParameter("cp_sdate",$cp_sdate);
$dlist->SetParameter("cp_edate",$cp_edate);
}
$dlist->SetSource($query);
       echo "<tr><td colspan='8' align='right'>".$moneystring."&nbsp;&nbsp;</td></tr>";
	   echo "<tr class='row_color_head'>
	   <td>No</td>
	   <td>番号</td>
	   <td>担当者</td>
	   <td>日付</td>
	   <td>金額</td>
	   <td>審査</td>
	   <td>支払</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   if($row['r_status']=='1') $statustring="<font color=red>審査済</font>";
	   else $statustring="未審査";
	   if($member=='')
	   $cmoney=Out_money('rk',$row['r_dh']);
	   else
	   $cmoney=Out_money('rk',$row['r_dh'],$member);
	   $amoney+=$cmoney;
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n
	   <td><center>".$row['id']."</td>
	   <td><center>".$row['r_dh']."</td>
	   <td><center>".$row['r_people']."</td>
	   <td><center>".$row['r_date']."</td>
	   <td><center>￥".$cmoney."</td>
	   <td><center>".$statustring."</td>
	   <td><center><a href='system_yf.php?action=sure&dh=".$row['r_dh']."&money=$cmoney' title='支払確認'>支払</a></td>\r\n
	   </tr>";
	   }
	   echo "<tr><td colspan=\"8\">&nbsp;&nbsp;総計：&nbsp;￥".$amoney."円</td></tr>";
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
