<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')
ShowMsg('引数エラー','-1');
if($action=='save'){
 $addsql="update #@__categories set categories='$categories', score='$score', browse_node ='$browse_node', addword='$addword' where id='$id'";
 $message= $lstring.$categories."成功";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
 $asql->close();
 showmsg('商品分類を修正しました。','system_class.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>分類管理</title>
</head>
<body>
<?php
$esql=New Dedesql(false);
$query="select categories,score,browse_node, addword, reid from #@__categories where id='$id'";
$esql->SetQuery($query);
$esql->Execute();
if($esql->GetTotalRow()==0){
ShowMsg('引数エラー、もう一度実行してください。','-1');
exit();
}
$row=$esql->GetOne($query);
$esql->close();
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
      <td><strong>&nbsp;商品分類修正</strong></td>
     </tr>
	 <form action="system_cate_edit.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;分類名：</td>
		 <td>
		 &nbsp;<input type="text" name="categories" size="20" value="<?php echo $row['categories'] ?>"/><input type="hidden" name="id" value="<?php echo $id; ?>"/>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;权重：</td>
		 <td>
		 &nbsp;<input type="text" name="score" size="20" value="<?php echo $row['score'] ?>"/>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;推奖ブラウズノード：</td>
		 <td>
		 &nbsp;<input type="text" name="browse_node" size="20" value="<?php echo $row['browse_node'] ?>"/>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;追加分類：</td>
		 <td>
		 &nbsp;<input type="text" name="addword" size="20" value="<?php echo $row['addword'] ?>"/>
		 </td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <?php $submitstring=($row['reid']=='0')?'大分類修正':'小分類修正';?>
		 <td><input type="hidden" name="lstring" value="<?php echo $submitstring; ?>"/>&nbsp;<input type="submit" name="submit" value=" <?php echo $submitstring;?> "/></td>
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
