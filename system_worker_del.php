<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>社員削除</title>
</head>
<body>
<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/config_rglobals.php");
if($id=='')ShowMsg('不正操作','system_worker.php');

$username=GetCookie('VioomaUserID');
$dsql=New Dedesql(false);
$query="select * from #@__staff where id='$id'";
$dsql->Setquery($query);
$dsql->Execute();
$rowcount=$dsql->GetTotalRow();
if($rowcount==0)
ShowMsg('不正操作','-1');
else{
	//echo $buser;
 $dsql->ExecuteNoneQuery("delete from #@__staff where id='$id'");
 $dsql->ExecuteNoneQuery("delete from #@__boss where boss='$buser'");
 WriteNote('社員情報を削除しました。',getdatetimemk(time()),getip(),$username);
 ShowMsg('社員情報を削除しました。','system_worker.php');
 }
 $dsql->close();
?>
</body>
</html>
