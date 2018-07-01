<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
 if($groupname=='') {
 showmsg('顧客グループ名を入力してください。','-1');
 exit();
 }
 if(!is_numeric($sub) || $sub=='' || $sub>10 || $sub<1){
  showmsg('1から10までの数字を入力してください。','-1');
 exit();
 }
 $addsql="insert into #@__group(groupname,sub,groupmem,staffid) values('$groupname','$sub','$groupmem','$staff')";
 $message="顧客グループ".$groupname."を追加しました。";
  
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=Getcookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('顧客グループを追加しました。','guest_group.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>顧客グループ管理</title>
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
      <td><strong>&nbsp;顧客グループ管理</strong>&nbsp;&nbsp;<a href="guest_group.php?action=new">顧客グループ追加</a> | <a href="guest_group.php">顧客グループ一覧へ</a></td>
     </tr><form action="guest_group.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
	  <?php if($action=='new'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style" width="25%">&nbsp;顧客グループ名:<br>(位置や、性質などより)</td>
		 <td>
		 &nbsp;<input type="text" name="groupname" size="20"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;割引グループ：</td>
		 <td>
		 &nbsp;<input type="text" name="sub" size="2" value="10"> 割</td>
	    </tr>
		<tr>
		 <td id="row_style">
		 &nbsp;顧客グループ名：
		 </td>
		 <td>&nbsp;<textarea cols="40" rows="3" name="groupmem"></textarea>
		</tr>
		<tr>
		 <td id="row_style">
		 &nbsp;該当グループの責任者：
		 </td>
		 <td>
		 &nbsp;<?php echo getstaff()?>
		 </td>
		</tr>
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 顧客グループ追加 "></td>
	    </tr>
	   </table></form>
	   <?php
	    } 
		else
		{
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   if(GetCookie('rank')==1)
	   $query="select * from #@__group";	   
	   else{
	   $sql1=New Dedesql(false);
	   $query1="select id from #@__staff where s_name='".GetCookie("VioomaUserID")."'";
	   $rowi=$sql1->getone($query1);
	   $sid=$rowi['id']; 
	   $query="select * from #@__group where staffid='$sid'";
	   }
	   $csql->SetQuery($query);
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;顧客グループがありません、追加してください。<a href=guest_group.php?action=new>グループ追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>グループ名</td>
	   <td>割引</td>
	   <td>責任者</td>
	   <td>操作</td>
	   </tr>";
	   while($row=$csql->GetArray()){
	   echo "<tr>
	   <td><center>ID号:".$row['id']."</td>
	   <td><center><img src=images/cate.gif align=absmiddle>&nbsp;".$row['groupname']."</td>
	   <td><center>".$row['sub']." 割</td>
	   <td><center>".getstaff($row['staffid'],'SHOW')."</td>
	   <td><center><a href=group_edit.php?id=".$row['id'].">修正</a> | <a href=group_del.php?id=".$row['id'].">削除</a></td>
	   </tr>";
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
