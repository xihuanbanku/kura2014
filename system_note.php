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
<title><?php echo $cfg_softname;?>システムログ</title>
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
	    <tr><form action="system_note.php?action=seek" name="form1" method="post">
		 <td>
	  <strong>&nbsp;システムログ</strong>
	     </td>
		 <td align="right">
		 <?php 
		 if($action=='seek')
		 echo "关键字<input type=\"text\" name=\"msg\" size=\"15\" VALUE=\"".$msg."\">
		日付範囲：<input type=\"text\" name=\"cp_sdate\" size=\"15\" VALUE=\"".$cp_sdate."\" class=\"Wdate\" onClick=\"WdatePicker()\">ー 
		<input type=\"text\" name=\"cp_edate\" size=\"15\" VALUE=\"".$cp_edate."\" class=\"Wdate\" onClick=\"WdatePicker()\">";
		 else
		 echo "关键字<input type=\"text\" name=\"msg\" size=\"15\">
		日付範囲：<input type=\"text\" name=\"cp_sdate\" size=\"15\" class=\"Wdate\" onClick=\"WdatePicker()\"> ー 
		<input type=\"text\" name=\"cp_edate\" size=\"15\" class=\"Wdate\" onClick=\"WdatePicker()\">";
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
		echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
		if($action=='seek'){
			if($cp_sdate=='' || $cp_edate=='' || $cp_sdate=='' || $cp_edate=='' || $cp_sdate>$cp_edate) {
				echo "<script>alert('正しい日付範囲を選択してください。');history.go(-1);</script>";
			}
			$query="select * from #@__recordline where date between '$cp_sdate' and '$cp_edate' and message like '%$msg%' order by date desc";
		} else {
			$query="select * from #@__recordline order by id desc";
		}
		$csql=New Dedesql(false);
		$dlist = new DataList();
		$dlist->pageSize = $cfg_record;

		if($action=='seek'){
			$dlist->SetParameter("action",$action);
			$dlist->SetParameter("cp_sdate",$cp_sdate);
			$dlist->SetParameter("cp_edate",$cp_edate);
			$dlist->SetParameter("msg",$msg);
		}
		$dlist->SetSource($query);
		echo "<tr class='row_color_head'><td>ID</td><td>操作情報</td><td>IPアドレス</td><td>ユーザー</td><td>日付</td><!--<td>選択</td>--></tr>";
		$mylist = $dlist->GetDataList();
		while($row = $mylist->GetArray('dm')){
			echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n<td>".$row['id']."</td><td>&nbsp;".$row['message']."</td><td>".$row['ip']."</td><td>".$row['userid']."</td><td>".$row['date']."</td><td><!--<input type='checkbox' name='sel_pro".$row['id']."' value='".$row['id']."'></td>-->\r\n</tr>";
		}
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
