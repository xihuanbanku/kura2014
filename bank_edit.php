<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')
ShowMsg('引数が正しくない。','-1');
if($action=='save'){
if($b_name==''){
ShowMsg('口座名称を入力してください。','-1');
exit();
}
if($b_default==1){
$sasql=New Dedesql(false);
$sasql->ExecuteNoneQuery("update #@__bank set bank_default=0");
$sasql->close();
}
$addsql="update #@__bank set bank_name='$b_name',bank_account='$b_account',bank_default='$b_default',bank_text='".$b_text."' where id='$id'";
 $message= "銀行資料修正".$b_name."成功";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 WriteNote($message,$logindate,$loginip,$username);
 $isql=new dedesql(false);
 $isql->ExecuteNoneQuery($addsql);
 $isql->close();
 showmsg('銀行資料を修正しました。','bank.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>銀行管理</title>
</head>
<body>
<?php
$esql=New Dedesql(false);
$query="select * from #@__bank where id='$id'";
$esql->SetQuery($query);
$esql->Execute();
if($esql->GetTotalRow()==0){
ShowMsg('引数呼出エラー、もう一度実行してください。','-1');
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
      <td><strong>&nbsp;銀行資料修正</strong></td>
     </tr>
	 <form action="bank_edit.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;銀行名：</td>
		 <td>
		 &nbsp;<input type="text" name="b_name" size="20" value="<?php echo $row['bank_name'] ?>" id="need"><input type="hidden" name="id" value="<?php echo $id; ?>">
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;金額：</td>
		 <td>
		 &nbsp;<input type="text" name="b_money" size="15" value="<?php echo $row['bank_money'] ?>" readonly>&nbsp;元</td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;口座番号：</td>
		 <td>
		 &nbsp;<input type="text" name="b_account" size="20" value="<?php echo $row['bank_account'] ?>"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;ﾃﾞｨﾌｫﾙﾄ銀行をする</td>
		 <td>
		 <?php
		  if ($row['bank_default']==0)
		 echo "&nbsp;<select name=\"b_default\"><option value=\"1\">はい</option><option value=\"0\" selected>いいえ</option></select>&nbsp;ﾃﾞｨﾌｫﾙﾄ銀行が一つ限り";
		 else
		 echo "&nbsp;<select name=\"b_default\"><option value=\"1\" selected>はい</option><option value=\"0\">いいえ</option></select>&nbsp;ﾃﾞｨﾌｫﾙﾄ銀行が一つ限り";
		 ?>
		 </td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;備考：</td>
		 <td>
		 &nbsp;<textarea name="b_text" cols="40" rows="2"><?php echo $row['bank_text'] ?></textarea></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 銀行資料修正 "></td>
	    </tr></form>
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
