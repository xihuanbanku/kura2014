<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')
ShowMsg('不正な引数。','-1');
if($action=='save'){
if($g_name==''){
ShowMsg('顧客名称を入力してください。','-1');
exit();
}
 $addsql="update #@__guest set g_name='$g_name',g_man='$g_man',g_address='$g_address',g_phone='$g_phone',g_qq='$g_qq',g_bank='$g_bank',g_card='$g_card',g_group='$g_group' where id='$id'";
 $message= "会員".$s_name."情報を修正しました。";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
 $asql->close();
 showmsg('会員情報を修正しました','system_guest.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>会員管理</title>
</head>
<body>
<?php
$esql=New Dedesql(false);
$query="select * from #@__guest where id='$id'";
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
      <td><strong>&nbsp;顧客情報修正</strong></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF"><form action="guest_edit.php?action=save" method="post">
	  <input type="hidden" name="id" value="<?php echo $id;?>" />
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <tr>
		 <td id="row_style">&nbsp;顧客名称：</td>
		 <td>
		 &nbsp;<input type="text" name="g_name" size="30" id="need" value="<?php echo $row['g_name'];?>"></td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;担当者：</td>
		 <td>
		 &nbsp;<input type="text" name="g_man" size="15" value="<?php echo $row['g_man'];?>"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;住所：</td>
		 <td>
		 &nbsp;<input type="text" name="g_address" size="25" value="<?php echo $row['g_address'];?>"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;電話番号：</td>
		 <td>
		 &nbsp;<input type="text" name="g_phone" size="15" value="<?php echo $row['g_phone'];?>"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;メールアドレス:</td>
		 <td>
		 &nbsp;<input type="text" name="g_qq" size="15" value="<?php echo $row['g_qq'];?>"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;口座名：</td>
		 <td>
		 &nbsp;<input type="text" name="g_bank" size="20" value="<?php echo $row['g_bank'];?>"> (格式:招行XX省XX支行)</td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;口座番号：</td>
		 <td>
		 &nbsp;<input type="text" name="g_card" size="20" value="<?php echo $row['g_card'];?>"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;グループ</td>
		 <td>
		 <?php
		 getgroup($row['g_group']);
		 ?>
		 </td>
	    </tr>		
		<tr>
		 <td id="row_style">&nbsp;修正担当者：</td>
		 <td>
		 &nbsp;<?php echo $row['g_people'] ?>
		 </td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;修正時間：</td>
		 <td>
		 &nbsp;<?php echo $row['g_dtime']; ?></td>
	    </tr>							
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 会員情報修正 "></td>
        </tr>
	   </table></form>
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
