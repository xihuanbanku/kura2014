<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='insert'){
    $addsql="insert into #@__new(name, score) values('$categories', '$score')";
    $message= "新旧类型".$categories."添加成功";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
    $asql->close();
    showmsg('新旧类型を新增しました。','system_new_list.php');
    exit();
} else if($action=='save'){
    if($id=='')
        ShowMsg('引数エラー','-1');
    $addsql="update #@__new set name='$categories', score='$score' where id='$id'";
    $message= "新旧类型".$categories."修正成功";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
    $asql->close();
    showmsg('新旧类型を修正しました。','system_new_list.php');
    exit();
} else if ($action=='del') {
    if($id=='')
        ShowMsg('引数エラー','-1');
    $addsql="delete from #@__new where id='$id'";
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->close();
    showmsg('新旧类型を删除しました。','system_new_list.php');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>新旧类型管理</title>
</head>
<body>
<?php
if($action != "new") {
    $esql=New Dedesql(false);
    $query="select name,score,id from #@__new where id='$id'";
    $esql->SetQuery($query);
    $esql->Execute();
    if($esql->GetTotalRow()==0){
    ShowMsg('引数エラー、もう一度実行してください。','-1');
    exit();
    }
    $row=$esql->GetOne($query);
    $esql->close();
}
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
      <td><strong>&nbsp;新旧类型<?php if($action == "new") {echo "新增";} else {echo "修正";} ?></strong></td>
     </tr>
	 <form action="system_new_edit.php?action=<?php if($action == "new") {echo "insert";} else {echo "save";} ?>" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;名称：</td>
		 <td>
		 &nbsp;<input type="text" name="categories" size="20" value="<?php echo $row['name'] ?>"><input type="hidden" name="id" value="<?php echo $id; ?>">
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;权重：</td>
		 <td>
		 &nbsp;<input type="text" name="score" size="20" value="<?php echo $row['score'] ?>">
		 </td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value="提交"></td>
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
