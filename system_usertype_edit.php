<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($rank=='')
ShowMsg('引数エラー','-1');
if($action=='save'){
if($b_name==''){
ShowMsg('ユーザータイプ名を入力してください。','-1');
exit();
}
  if($password=='')
 $addsql="update #@__usertype set typename='$b_name' where rank='$rank'";
 $message= "ユーザータイプ".$b_name."を修正しました。";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 WriteNote($message,$logindate,$loginip,$username);
 $asql->close();
 showmsg('ユーザータイプを修正しました','system_usertype.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>ユーザータイプ管理</title>
</head>
<body>
<?php
$esql=New Dedesql(false);
$queryboss="select * from #@__usertype where rank='$rank'";
$esql->SetQuery($queryboss);
$esql->Execute();
if($esql->GetTotalRow()==0){
ShowMsg('引数エラー、もう一度実行してください。','-1');
exit();
}
$rs=$esql->GetOne($query);
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
      <td><strong>&nbsp;ユーザータイプ修正</strong></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border"> 
	   <form action="system_usertype_edit.php?action=save" method="post">
	    <tr>
		 <td id="row_style">&nbsp;ユーザータイプ名：</td>
		 <td>
		 &nbsp;<input type="text" name="b_name" size="15" value="<?php echo $rs['typename']; ?>">
		 <input type="hidden" name="rank" value="<?php echo $rank; ?>"> *必須
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
