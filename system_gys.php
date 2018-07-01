<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
if($g_name==''){
ShowMsg('仕入先名を入力してください。','-1');
exit();
}
 $addsql="insert into #@__gys(g_name,g_people,g_address,g_phone,g_qq) values('$g_name','$g_people','$g_address','$g_phone','$g_qq')";
 $message="仕入先：".$g_name."を追加しました。";

 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=Getcookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('仕入先を追加しました','system_gys.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>仕入先管理</title>
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
      <td><strong>&nbsp;仕入先管理</strong>&nbsp;&nbsp;<a href="system_gys.php?action=new">仕入先追加</a> | <a href="system_gys.php">仕入先一覧</a></td>
     </tr>
	 <form action="system_gys.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
	  <?php if($action=='new'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;仕入先名：</td>
		 <td>
		 &nbsp;<input type="text" name="g_name" size="30" id="need"></td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;仕入先住所：</td>
		 <td>
		 &nbsp;<input type="text" name="g_address" size="30"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;連絡担当者：</td>
		 <td>
		 &nbsp;<input type="text" name="g_people" size="10"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;電話番号：</td>
		 <td>
		 &nbsp;<input type="text" name="g_phone" size="15"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;メール：</td>
		 <td>
		 &nbsp;<input type="text" name="g_qq" size="20"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 追加 "></td>
	    </tr>
		</form>
	   </table>
	   <?php
	    } 
		else
		{
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   $csql->SetQuery("select * from #@__gys");
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;仕入先が存在しません、追加してください。<a href=system_gys.php?action=new>仕入先追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'><td>ID</td><td>名称</td><td>連絡担当者</td><td>住所</td><td>電話番号</td><td>メール</td><td>操作</td></tr>";
	   while($row=$csql->GetArray()){
	   echo "<tr><td>ID号:".$row['id']."</td><td>&nbsp;".$row['g_name']."</td><td>&nbsp;".$row['g_people']."</td><td>&nbsp;".$row['g_address']."</td><td>&nbsp;".$row['g_phone']."</td><td>&nbsp;".$row['g_qq']."</td><td><a href=system_gys_edit.php?id=".$row['id'].">修正</a> | <a href=system_gys_del.php?id=".$row['id'].">削除</a></td></tr>";
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
