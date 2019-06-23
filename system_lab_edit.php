<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')
ShowMsg('引数不正','-1');
if($action=='save'){
if($l_name==''){
ShowMsg('新倉庫を入力してください','-1');
exit();
}
if($l_default==1){
$sasql=New Dedesql(false);
$sasql->ExecuteNoneQuery("update #@__lab set l_default=0");
$sasql->close();
}
 
 $addsql="update #@__lab set l_name='$l_name',l_city='$l_city',l_mang='$l_mang',l_default='$l_default' where id='$id'";
 $message= "倉庫".$l_name."を修正しました。";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
 $asql->close();
 showmsg('倉庫を修正しました。','system_lab.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>倉庫管理</title>
</head>
<body>
<?php
$esql=New Dedesql(false);
$query="select * from #@__lab where id='$id'";
$esql->SetQuery($query);
$esql->Execute();
if($esql->GetTotalRow()==0){
ShowMsg('引数エラー、もう一度実行してください。','-1');
exit();
}
$row=$esql->GetOne($query);
$esql->close();
?>
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
      <td><strong>&nbsp;倉庫情報修正</strong></td>
     </tr>
	 <form action="system_lab_edit.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;倉庫名：</td>
		 <td>
		 &nbsp;<input type="text" name="l_name" size="10" value="<?php echo $row['l_name'] ?>" id="need"><input type="hidden" name="id" value="<?php echo $id; ?>">
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;住所：</td>
		 <td>
		 &nbsp;<input type="text" name="l_city" size="30" value="<?php echo $row['l_city'] ?>"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;責任者：</td>
		 <td>
		 &nbsp;<input type="text" name="l_mang" size="15" value="<?php echo $row['l_mang'] ?>"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;ﾃﾞｨﾌｫﾙﾄ倉庫</td>
		 <td>
		 <?php
		  if ($row['l_default']==0)
		 echo "&nbsp;<select name=\"l_default\"><option value=\"1\">はい</option><option value=\"0\" selected>いいえ</option></select>&nbsp;ﾃﾞｨﾌｫﾙﾄ倉庫が一つ限り";
		 else
		 echo "&nbsp;<select name=\"l_default\"><option value=\"1\" selected>はい</option><option value=\"0\">いいえ</option></select>&nbsp;ﾃﾞｨﾌｫﾙﾄ倉庫が一つ限り";
		 ?>
		 </td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 修正 "></td>
	    </tr>
		</form>
	   </table>
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
