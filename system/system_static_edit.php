<?php
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/config_rglobals.php");
require_once(dirname(__FILE__)."/../include/checklogin.php");

if($action=='insert'){
    $addsql="insert into #@__static(p_type, p_name, p_value, description) values('$pType', '$p_name', '$p_value', '$description')";
    $message= "系统参数".$p_name."添加成功";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->close();
    showmsg('系统参数を新增しました。','system_static_list.php?pType='.$pType);
    exit();
} else  if($action=='save'){
    if($id=='')
        ShowMsg('引数エラー','-1');
    $addsql="update #@__static set p_name='$p_name', p_value='$p_value', description='$description' where id='$id'";
    $message= "系统参数".$p_name."修改成功";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->close();
    showmsg('系统参数を修正しました。','system_static_list.php?pType='.$pType);
    exit();
} else if ($action=='del') {
    if($id=='')
        ShowMsg('引数エラー','-1');
    $addsql="delete from #@__static where id='$id'";
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->close();
    showmsg('系统参数を删除しました。','system_static_list.php?pType='.$pType);
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>系统参数管理</title>
</head>
<body>
<?php
if($action != "new") {
    $esql=New Dedesql(false);
    $query="select p_name, p_value, description,id from #@__static where id='$id'";
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
      <td><strong>&nbsp;系统参数<?php if($action == "new") {echo "新增";} else {echo "修正";} ?></strong></td>
     </tr>
	 <form action="system_static_edit.php?action=<?php if($action == "new") {echo "insert";} else {echo "save";} ?>&pType=<?php echo $pType;?>" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style"></td>
		 <td>
		 名称和取值两个项目是必须的, 可以认为名称是给人看的,取值是给机器看的. <br/>
		 举例: 是(名称)=1(取值),否(名称)=0(取值)<br/>
		 <font color="red">注意:取值只能是!!!字母和数字!!!</font>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;名称：</td>
		 <td>
		 &nbsp;<input type="text" name="p_name" size="20" value="<?php echo $row['p_name'] ?>"><input type="hidden" name="id" value="<?php echo $id; ?>">
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;取值：</td>
		 <td>
		 &nbsp;<input type="text" name="p_value" size="20" value="<?php echo $row['p_value'] ?>">
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;说明：</td>
		 <td>
		 &nbsp;<input type="text" name="description" size="20" value="<?php echo $row['description'] ?>">
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
