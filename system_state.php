<?php
require(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='insert'){
if($s_name==''){
ShowMsg('狀態を入力してください。','-1');
exit();
}
 $addsql="insert into #@__state(s_name, s_value, parent_id) values('$s_name', '$s_value', '$parent_id')";
 $message="狀態".$s_name."[".$s_value."]を追加しました。";

 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=Getcookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('狀態を追加しました。','system_state.php');
 exit();
} else if ($action=='save'){
    $dsql=New Dedesql(false);
    $query="update #@__state set s_name = '$s_name', s_value = '$s_value', parent_id = '$parent_id' where id='$id'";
    $dsql->ExecuteNoneQuery($query);
    ShowMsg('狀態を修正しました。','system_state.php');
    $dsql->close();
} else if ($action=='del'){
    if($id=='')ShowMsg('不正操作','system_state.php');
    
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $dsql=New Dedesql(false);
    $query="select * from #@__state where id='$id'";
    $dsql->Setquery($query);
    $dsql->Execute();
    $rowcount=$dsql->GetTotalRow();
    if($rowcount==0) //非法ID
        ShowMsg('不正操作','-1');
    else{
        $row=$dsql->GetOne();
        if($row['l_default']==1){
            ShowMsg('ﾃﾞｨﾌｫﾙﾄ狀態を削除できません。','-1');
            exit();}
            $dsql->ExecuteNoneQuery("delete from #@__state where id='$id'");
            WriteNote('狀態削除(ID：'.$id.')',getdatetimemk(time()),getip(),$username);
            ShowMsg('狀態を削除しました。','system_state.php');
    }
    $dsql->close();
} else if ($action=='mod'){
    if($id=='')ShowMsg('不正操作','system_state.php');
    
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $dsql=New Dedesql(false);
    $query="select * from #@__state where id='$id'";
    $row=$dsql->GetOne($query);
    $dsql->close();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfs_softname;?>狀態管理</title>
</head>
<body>
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
      <td><strong>&nbsp;狀態管理</strong>&nbsp;&nbsp;<a href="system_state.php?action=new">狀態追加</a> | <a href="system_state.php">狀態一覧</a></td>
     </tr>
	 <form action="system_state.php?action=<?php if($action == "new") {echo "insert";} else {echo "save";} ?>" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
	  <?php if($action=='new' || $action=='mod'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">狀態分类：</td>
		 <td>
		      <select name="parent_id">
		          <option <?php if($row['parent_id'] == 1) {echo "selected = \"selected\"";}?> value="1">狀態1</option>
		          <option <?php if($row['parent_id'] == 2) {echo "selected = \"selected\"";}?> value="2">狀態2</option>
		          <option <?php if($row['parent_id'] == 3) {echo "selected = \"selected\"";}?> value="3">狀態3</option>
		          <option <?php if($row['parent_id'] == 4) {echo "selected = \"selected\"";}?> value="4">狀態4</option>
		          <option <?php if($row['parent_id'] == 5) {echo "selected = \"selected\"";}?> value="5">狀態5</option>
		          <option <?php if($row['parent_id'] == 6) {echo "selected = \"selected\"";}?> value="6">狀態6</option>
		          <option <?php if($row['parent_id'] == 7) {echo "selected = \"selected\"";}?> value="7">狀態7</option>
		          <option <?php if($row['parent_id'] == 8) {echo "selected = \"selected\"";}?> value="8">狀態8</option>
		          <option <?php if($row['parent_id'] == 9) {echo "selected = \"selected\"";}?> value="9">狀態9</option>
		          <option <?php if($row['parent_id'] == 10) {echo "selected = \"selected\""; }?> value="10">狀態10</option>
		          <option <?php if($row['parent_id'] == 11) {echo "selected = \"selected\""; }?> value="11">狀態11</option>
		          <option <?php if($row['parent_id'] == 12) {echo "selected = \"selected\""; }?> value="12">狀態12</option>
		      </select>
	      </td>
	    </tr>
	    <tr>
		 <td id="row_style">狀態名：</td>
		 <td>
		 &nbsp;<input type="text" name="s_name" size="20" value="<?php echo $row['s_name'] ?>"/><input type="hidden" name="id" value="<?php echo $id; ?>"></td>
	    </tr>
	    <tr>
		 <td id="row_style">狀態值：</td>
		 <td>
		 &nbsp;<input type="text" name="s_value" size="20" value="<?php echo $row['s_value'] ?>"/></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value="提交 "/></td>
	    </tr>
		</form>
	   </table>
	   <?php
	    } 
		else
		{
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   $csql->SetQuery("select * from #@__state where id>0 order by parent_id, id");
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;狀態が存在しません、追加してください。<a href=system_state.php?action=new>狀態追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'><td>ID</td><td>狀態分类</td><td>狀態名</td><td>狀態值(更新狀態时使用这一列)</td><td>操作</td></tr>";
	   while($row=$csql->GetArray()){
	   if ($row['l_default']==1)
	    $default_yes="<img src=images/yes.png>";
		else
		$default_yes="&nbsp;";
	   echo "<tr><td>ID号:".$row['id']."</td>
        <td>狀態".$row['parent_id']."</td><td>".$row['s_name']."</td>
        <td>".$row['s_value']."</td>
        <td><a href=system_state.php?id=".$row['id']."&action=mod>编辑</a>|<a href=system_state.php?id=".$row['id']."&action=del>削除</a></td></tr>";
	   }
	   }
	   echo "</table>";
	  
	   $csql->close();
		}
	   ?>
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
