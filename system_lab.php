<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
if($l_name==''){
ShowMsg('倉庫名を入力してください。','-1');
exit();
}
 $addsql="insert into #@__lab(l_name,l_city,l_mang,l_default) values('$l_name','$l_city','$l_mang','$l_default')";
 $message="倉庫".$s_name."を追加しました。";

 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=Getcookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('倉庫を追加しました。','system_lab.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfs_softname;?>倉庫管理</title>
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
      <td><strong>&nbsp;倉庫管理</strong>&nbsp;&nbsp;<a href="system_lab.php?action=new">倉庫追加</a> | <a href="system_lab.php">倉庫一覧</a></td>
     </tr>
	 <form action="system_lab.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
	  <?php if($action=='new'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;倉庫名：</td>
		 <td>
		 &nbsp;<input type="text" name="l_name" size="20" id="need"></td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;住所：</td>
		 <td>
		 &nbsp;<input type="text" name="l_city" size="20"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;責任者：</td>
		 <td>
		 &nbsp;<input type="text" name="l_mang" size="15"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;ﾃﾞｨﾌｫﾙﾄ倉庫：</td>
		 <td>
		 &nbsp;<select name="l_default"><option value="1">はい</option><option value="0" selected>いいえ</option></select>&nbsp;ﾃﾞｨﾌｫﾙﾄ倉庫が一つ限り</td>
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
	   $csql->SetQuery("select * from #@__lab");
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;倉庫が存在しません、追加してください。<a href=system_lab.php?action=new>倉庫追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'><td>ID</td><td>倉庫名</td><td>住所</td><td>責任者</td><td>ﾃﾞｨﾌｫﾙﾄ</td><td>修正</td></tr>";
	   while($row=$csql->GetArray()){
	   if ($row['l_default']==1)
	    $default_yes="<img src=images/yes.png>";
		else
		$default_yes="&nbsp;";
	   echo "<tr><td>ID号:".$row['id']."</td><td>&nbsp;".$row['l_name']."</td><td>&nbsp;".$row['l_city']."</td><td>&nbsp;".$row['l_mang']."</td><td>&nbsp;".$default_yes."</td><td><a href=system_lab_edit.php?id=".$row['id'].">修正</a> | <a href=system_lab_del.php?id=".$row['id'].">削除</a></td></tr>";
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
