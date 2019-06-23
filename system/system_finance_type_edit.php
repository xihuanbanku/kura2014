<?php
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/config_rglobals.php");
require_once(dirname(__FILE__)."/../include/checklogin.php");
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';

if($action=='insert'){
    $addsql="insert into #@__finance_type(name, p_id) values('$categories', '$p_id')";
    $message= "财务类型".$categories."添加成功";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
    $asql->close();
    showmsg('财务类型を新增しました。','system_finance_type_list.php');
    exit();
} else  if($action=='save'){
    if($id=='')
        ShowMsg('引数エラー','-1');
    $addsql="update #@__finance_type set name='$categories' where id='$id'";
    $message= "财务类型".$categories."修改成功";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
    $asql->close();
    showmsg('财务类型を修正しました。','system_finance_type_list.php');
    exit();
} else if ($action=='del') {
    if($id=='')
        ShowMsg('引数エラー','-1');
    $addsql="delete from #@__finance_type where id='$id'";
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    $asql->close();
    showmsg('财务类型を删除しました。','system_finance_type_list.php');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.searchableSelect.js"></script>
<link href="../style/jquery.searchableSelect.css" rel="stylesheet" type="text/css"/>
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>财务类型管理</title>
</head>
<body>
<?php
if($action != "new") {
    $esql=New Dedesql(false);
    $query="select name,id from #@__finance_type where id='$id'";
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
      <td><strong>财务类型<?php if($action == "new") {echo "新增";} else {echo "修正";} ?></strong></td>
     </tr>
	 <form action="system_finance_type_edit.php?action=<?php if($action == "new") {echo "insert";} else {echo "save";} ?>" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">父类型：</td>
		 <td>
		 <select name="p_id">
           <?php 
            $newsql = new ezSQL_mysql();
            $results = $newsql->get_results("select a.name, a.id from jxc_finance_type a") or mysql_error();
            if($results) {
                foreach($results as $result) {
                    echo "<option value=\"{$result->id}\">{$result->name}</option>";
                }
            }
           ?>
           </select>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">名称：</td>
		 <td>
		 &nbsp;<input type="text" name="categories" size="20" value="<?php echo $row['name'] ?>"><input type="hidden" name="id" value="<?php echo $id; ?>">
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
<script>
	$(function(){
		$('select').searchableSelect();
	});
</script>
</body>
</html>
