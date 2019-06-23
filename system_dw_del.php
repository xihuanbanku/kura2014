<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>計量単位削除</title>
</head>
<body>
<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
require_once(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')ShowMsg('不正操作','system_dw.php');

$username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
$dsql=New Dedesql(false);
$query="select * from #@__dw where id='$id'";
$dsql->Setquery($query);
$dsql->Execute();
$rowcount=$dsql->GetTotalRow();
if($rowcount==0)
ShowMsg('不正操作','-1');
else{
 $row=$dsql->GetArray();
 if($row['reid']==0){
 $msql=New Dedesql(false);
 $msql->SetQuery("select * from #@__dw where reid='".$row['id']."'");
 $msql->Execute();
 if($msql->GetTotalRow()>=1)
 echo "<script language='javascript'>alert('該当大単位の中身に小単位があります。小単位を先に削除してください。');history.go(-1);</script>";
 else{
 $msql->ExecuteNoneQuery("delete from #@__dw where id='$id'");
 WriteNote('大単位削除：'.$row['dwname'],getdatetimemk(time()),getip(),$username);
 ShowMsg('計量大単位を削除しました。','system_dw.php');
 }
 $msql->close();
 }
 else{
  $msql=New Dedesql(false);
  $msql->ExecuteNoneQuery("delete from #@__dw where id='$id'");
  WriteNote('小単位削除：'.$row['dwname'],getdatetimemk(time()),getip(),$username);
  ShowMsg('計量小単位を削除しました。','system_dw.php');
  $msql->close();
 }
 $dsql->close();
}

?>
</body>
</html>
