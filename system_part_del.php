<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>部門削除</title>
</head>
<body>
<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
require_once(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')ShowMsg('不正操作','system_part.php');

$username=GetCookie("VioomaUserID");
$dsql=New Dedesql(false);
$query="select * from #@__part where id='$id'";
$dsql->Setquery($query);
$dsql->Execute();
$rowcount=$dsql->GetTotalRow();
if($rowcount==0)
ShowMsg('不正操作','-1');
else{
 $dsql->ExecuteNoneQuery("delete from #@__part where id='$id'");
 WriteNote('部門削除(ID：'.$id.')',getdatetimemk(time()),getip(),$username);
 ShowMsg('部門を削除しました。','system_part.php');
 }
 $dsql->close();
?>
</body>
</html>
