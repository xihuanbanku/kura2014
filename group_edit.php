<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')
ShowMsg('不正操作','-1');
if($action=='save'){
 if($groupname=='') {
 showmsg('顧客グループ名','-1');
 exit();
 }
 if(!is_numeric($sub) || $sub=='' || $sub>10 || $sub<1){
  showmsg('1から10までの数字を入力してください。','-1');
 exit();
 }
 $addsql="update #@__group set groupname='$groupname',sub='$sub',groupmem='$groupname',staffid='$staff' where id='$id'";
 $message= "顧客グループ".$groupname."を修正しました。";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 WriteNote($message,$logindate,$loginip,$username);
 $asql->close();
 showmsg('顧客グループを修正しました。','guest_group.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>計量単位管理</title>
</head>
<body>
<?php
$esql=New Dedesql(false);
$query="select * from #@__group where id='$id'";
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
      <td><strong>&nbsp;顧客グループ修正</strong></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF"><form action="group_edit.php?action=save" method="post">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style" width="25%">&nbsp;顧客グループ名：<br>(位置や、性質などより)</td>
		 <td>
		 &nbsp;<input type="text" name="groupname" size="20" value="<?php echo $row['groupname'] ?>">
		 <input type="hidden" name="id" value="<?php echo $id; ?>">
		 </td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;割引グループ：</td>
		 <td>
		 &nbsp;<input type="text" name="sub" size="2" value="<?php echo $row['sub'] ?>"> 割</td>
	    </tr>
		<tr>
		 <td id="row_style">
		 &nbsp;顧客グループ説明：
		 </td>
		 <td>&nbsp;<textarea cols="40" rows="3" name="groupmem"><?php echo $row['groupmem'];?></textarea>
		</tr>
		<tr>
		 <td id="row_style">
		 &nbsp;該当グループの責任者：
		 </td>
		 <td>
		 &nbsp;<?php echo getstaff($row['staffid'],'')?>
		 </td>
		</tr>
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 顧客グループ修正 "></td>
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
