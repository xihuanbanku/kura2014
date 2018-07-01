<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
if($b_name==''){
ShowMsg('担当者のユーザー名を入力してください。','-1');
exit();
}
 if(checkbossexist($b_name)){
 $password=md5($password);
 $addsql="insert into #@__boss(boss,password,logindate,loginip,errnumber,rank) values('$b_name','$password','0000-00-00 00:00:00','','0','$s_utype')";
 $message="担当者".$b_name."を追加しました。";

 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('担当者を追加しました。','system_boss.php');
 exit();
 }
 else
 showmsg('該当ユーザー名が既に存在しています。','system_boss.php?action=new');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>担当者管理</title>
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
      <td><strong>&nbsp;担当者管理</strong>&nbsp;&nbsp;<a href="system_worker.php?action=new">担当者追加</a> | <a href="system_boss.php">担当者一覧</a>&nbsp;|&nbsp;<a href="system_grant.php">権限設定</a></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
	  <?php if($action=='new'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="system_boss.php?action=save" method="post">
	    <tr>
		 <td id="row_style">&nbsp;ユーザー名：</td>
		 <td>
		 &nbsp;<input type="text" name="b_name" size="15" id="need"> *登録用ユーザー名</td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;パスワード：</td>
		 <td>
		 &nbsp;<input type="password" name="password"> *登録用パスワード</td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;担当者グループ：</td>
		 <td>&nbsp;<?php getusertype('',0);?></td>
	    </tr>
		<tr>
		 <td></td>
		 <td><input type="submit" value=" 追加 "></td>
		</tr>
		</form>
	   </table>
	   <?php
	    } 
		else
		{
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   if(GetCookie('rank')==1)
	   $csql->SetQuery("select * from #@__boss");
	   else
	   $csql->SetQuery("select * from #@__boss where boss='".GetCookie('VioomaUserID')."'");
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;担当者が存在しません、追加してください。<a href=system_boss.php?action=new>担当者追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>名称</td>
	   <td>登録時間</td>
	   <td>IPアドレス</td>
	   <td>グループ</td>
	   <td>操作</td>
	   </tr>";
	   while($row=$csql->GetArray()){
	   if($row['boss']!='admin')
	   echo "<tr>
	   <td><center>ID：".$row['id']."</td>
	   <td><center>".$row['boss']."</td>
	   <td><center>".$row['logindate']."</td>
	   <td><center>".$row['loginip']."</td>
	   <td><center>".getusertype($row['rank'],0)."</td>
	   <td><center><a href=system_boss_edit.php?id=".$row['id'].">修正</a> | <a href=system_boss_del.php?id=".$row['id'].">削除</a></td></tr>";
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