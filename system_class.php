<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
 if($reid==''){
 $addsql="insert into #@__categories(categories,reid) values('$categories','0')";
 $message="大分類".$categories."を追加しました。";
 }
 else{
 $addsql="insert into #@__categories(categories,reid) values('$categories','$reid')";
 $message="小分類".$categories."を追加しました。";
 }
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=Getcookie('VioomaUserID');
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 if($reid==''){
	 $addsql="insert into #@__description(id, name) select id, categories from #@__categories where categories = '$categories'";
	 $asql->ExecuteNoneQuery($addsql);
 }
 $asql->close();
 WriteNote($message,$logindate,$loginip,$username);
 showmsg('商品分類を追加しました。','system_class.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="js/jquery-1.10.2.min.js" type="text/javascript" ></script>
<link href="style/main.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.class_editA{display: none;}
.class_deleteA{display: none;}
</style>
<title><?php echo $cfg_softname;?>分類管理</title>
<script type = "text/javascript">
    function submitChk(id) {
            var flag = confirm ( "削除したら復元できないので、本当に削除しますか。");
            if (flag) {
                location.href = "system_cate_del.php?id=" + id;
            }
            return flag;
    }
</script>
<script type="text/javascript">
$(function(){
	$.ajax({
		type: "post",
		url: "service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"12", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
//         		alert(entry.url+"|"+entry.loc);
        		if(entry.loc > 0) {
    				$("#" + entry.url).show();
        		} else {
    				$("#" + entry.url).remove();
        		}
        		if((entry.url == "class_editA" ||entry.url == "class_deleteA") && entry.loc > 0) {
        		    $("."+entry.url).each(function(i, item){
            		    $(item).show();
        		    });
        		} else {
        			$("."+entry.url).each(function(i, item){
            		    $(item).remove();
        		    });
        		}
    		});
		}
	});
});
</script>
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
      <td><strong>&nbsp;商品分類設定</strong>&nbsp;&nbsp;<a href="system_class.php?action=new">大分類追加</a> | <a href="system_class.php">商品分類一覧</a>&nbsp;<font color=red>※：大分類を削除する時、小分類も削除される</font></td>
     </tr>
	 <form action="system_class.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
	  <?php if($action=='new'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;分類設定：</td>
		 <td>&nbsp;<input type="text" name="categories" size="20"><input type="hidden" name="reid" value="<?php echo $reid; ?>"></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <?php $submitstring=($reid=='')?' 大分類追加 ':' 小分類追加 ';?>
		 <td>&nbsp;<input type="submit" name="submit" value="<?php echo $submitstring;?>"></td>
	    </tr>
		</form>
	   </table>
	   <?php
	    } 
		else
		{
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   $csql->SetQuery("select * from #@__categories where reid=0");
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;分類が存在しません、追加してください。<a href=system_class.php?action=new>大分類追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'><td>ID</td><td>名称</td><td>操作</td></tr>";
	   while($row=$csql->GetArray()){
	   echo "<tr><td>ID：".$row['id']."</td><td><img src=images/cate.gif align=absmiddle>&nbsp;".$row['categories']."</td><td><a class=\"class_editA\" href=system_cate_edit.php?id=".$row['id'].">修正</a> | <a class=\"class_deleteA\" href='#' onClick=\"submitChk(".$row['id'].")\">削除</a> | <a href=system_class.php?action=new&reid=".$row['id'].">小分類追加</a></td></tr>";
	     $csql1=New Dedesql(false);
	     $csql1->SetQuery("select * from #@__categories where reid='".$row['id']."'");
		 $csql1->Execute();
		 while($row1=$csql1->GetArray()){
		 echo "<tr class='row_color_gray'><td>&nbsp;&nbsp;ID：".$row1['id']."</td><td> ├ ".$row1['categories']."</td><td><a class=\"class_editA\" href=system_cate_edit.php?id=".$row1['id'].">修正</a> | <a class=\"class_deleteA\" href='#' onClick=\"submitChk(".$row1['id'].")\">削除</a></td></tr>";
		 } $csql1->close();
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
