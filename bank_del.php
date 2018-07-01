<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>口座削除</title>
</head>
<body>
<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')ShowMsg('操作エラー','bank.php');
$username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
$dsql=New Dedesql(false);
$query="select * from #@__bank where id='$id'";
$dsql->Setquery($query);
$dsql->Execute();
$rowcount=$dsql->GetTotalRow();
if($rowcount==0)
ShowMsg('操作エラー','-1');
else{
 $row=$dsql->GetOne();
 if($row['bank_default']==1){
 ShowMsg('ﾃﾞｨﾌｫﾙﾄ口座を削除できません!','-1');
 exit();}
 $dsql->ExecuteNoneQuery("delete from #@__bank where id='$id'");
 WriteNote('銀行を削除しました。(ID?'.$id.')',getdatetimemk(time()),getip(),$username);
 ShowMsg('銀行を削除しました','bank.php');
 }
 $dsql->close();
?>
</body>
</html>
