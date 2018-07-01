<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
 $addsql="insert into #@__url_prefix(new_id, category_id, name) values('$new_id', '$category_id', '$categories') on duplicate key update name='$categories'";
 $message= "URL_".$category_id."_".$new_id."修正成功";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
 $asql->close();
 showmsg('URLを修正しました。','system_url_prefix_edit.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>url管理</title>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" >
$(function(){
    $("#category_id").change(function() {
    	loadDesContent();
    });
    $("#new_id").change(function() {
    	loadDesContent();
    });
	$.ajax({
		type: "post",
		url: "service/KcService.class.php",
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = " <option value=''>大分類選択</option>" +msg;
    		$("#category_id").html(msg);
		}
	});
	$.ajax({
		type: "post",
		url: "service/KcService.class.php",
		data: {"flag":"initNew"},
		success: function(msg){
	        msg = " <option value=''>新旧選択</option>" +msg;
    		$("#new_id").html(msg);
		}
	});
});
function loadDesContent() {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: $("select").serialize() + "&flag=loadUrlPrefix",
		success: function(msg){
			if(msg.trim() != "null") {
    			msg = eval("("+msg+")");
				$("input[name=categories]").val(msg[0].name);
    		} else {
				$("input[name=categories]").val("");
			}
		}
	});
}
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
      <td><strong>&nbsp;URL前缀修正</strong></td>
     </tr>
	 <form action="system_url_prefix_edit.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td id="row_style">&nbsp;URL：</td>
		 <td>
		 <select name="new_id" id="new_id">
        </select>
		 <select name="category_id" id="category_id">
        </select>
		 &nbsp;<input type="text" name="categories" size="50" />
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
