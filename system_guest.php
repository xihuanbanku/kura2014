<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
if($g_name==''){
ShowMsg('顧客名称を入力してください。','-1');
exit();
}
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 $pingyin=GetPinyin($g_name,1);
 $addsql="insert into #@__guest(g_name,g_man,g_address,g_phone,g_qq,g_bank,g_card,g_group,g_people,g_dtime,g_helpword) values('$g_name','$g_man','$g_address','$g_phone','$g_qq','$g_bank','$g_card','$g_group','$g_people','$logindate','$pingyin')";
 $message="顧客：".$g_name."を追加しました。";
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('顧客を追加しました。','system_guest.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfs_softname;?>顧客管理</title>
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
     <tr><form action="system_guest.php" method="post">
      <td><strong>&nbsp;顧客管理</strong>&nbsp;&nbsp;<a href="system_guest.php?action=new">顧客追加</a> | <a href="system_guest.php">顧客一覧</a> 
	  </td>
	  <td align="right">
	  顧客検索：<input type="text" value="<?php echo ($helpw=='')?'名称を入力してください':$helpw?>" onclick="this.value='';" name="helpw">&nbsp;<input type="submit" value="検索">&nbsp;&nbsp;
	  </td></form>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF" colspan="2">
	  <?php if($action=='new'){ ?><form action="system_guest.php?action=save" method="post" name="form1">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;顧客名称：</td>
		 <td>
		 &nbsp;<input type="text" name="g_name" size="20" id="need"></td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;連絡担当者：</td>
		 <td>
		 &nbsp;<input type="text" name="g_man" size="15"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;住所：</td>
		 <td>
		 &nbsp;<input type="text" name="g_address" size="25"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;電話番号：</td>
		 <td>
		 &nbsp;<input type="text" name="g_phone" size="15"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;メールアドレス：</td>
		 <td>
		 &nbsp;<input type="text" name="g_qq" size="15"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;銀行名:</td>
		 <td>
		 &nbsp;<input type="text" name="g_bank" size="20"> (例：三井住友銀行池袋支店)</td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;口座番号：</td>
		 <td>
		 &nbsp;<input type="text" name="g_card" size="20"></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;グループ</td>
		 <td>
		 <?php
		 getgroup();
		 ?>
		 </td>
	    </tr>		
		<tr>
		 <td id="row_style">&nbsp;担当者：</td>
		 <td>
		 &nbsp;<input type="text" name="g_people" size="10" value="<?php echo Getcookie('VioomaUserID')?>" readonly>
		 </td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;時間：</td>
		 <td>
		 &nbsp;<?php echo getDatetimeMk(time()); ?></td>
	    </tr>									
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 顧客追加 "></td>
	    </tr>
	   </table></form>
	   <?php
	    } 
		else
		{
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   $query="select * from #@__guest where g_name LIKE '%".$helpw."%' or g_helpword LIKE '%".$helpw."%'";	   
	   $csql->SetQuery($query);
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;顧客が存在しません、追加してください。<a href=system_guest.php?action=new>顧客追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>顧客名称</td>
	   <td>連絡担当者</td>
	   <td>住所</td>
	   <td>電話番号</td>
	   <td>メール</td>
	   <td>銀行名</td>
	   <td>口座番号</td>
	   <td>グループ</td>
	   <td>操作</td>
	   </tr>";
	   while($row=$csql->GetArray()){
	   echo "<tr>
	   <td><center>".$row['id']."</td>
	   <td><center>&nbsp;".$row['g_name']."</td>
	   <td><center>".$row['g_man']."</td>
	   <td><center>&nbsp;".$row['g_address']."</td>
	   <td><center>&nbsp;".$row['g_phone']."</td>
	   <td><center>".$row['g_qq']."</td>
	   <td><center>&nbsp;".$row['g_bank']."</td>
	   <td><center>&nbsp;".$row['g_card']."</td>
	   <td><center>&nbsp;".getgroup($row['g_group'],'group')."</td>
	   <td><center><a href=guest_edit.php?id=".$row['id'].">修正</a> | <a href=guest_del.php?id=".$row['id'].">削除</a></td>
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
