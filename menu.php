<?php
require_once (dirname(__FILE__) . "/include/config_base.php");
require_once (dirname(__FILE__) . "/include/config_rglobals.php");
require_once (dirname(__FILE__) . "/include/pub_db_mysql.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
<title>viooma menu</title>
<style type="text/css">
body {
	background-color: #3179bd;
}
</style>
<link href="style/menu.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script language="javascript">
$(function(){
	var url = "service/MenuService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initMenu", "userID":<?php echo $_COOKIE["userID"];?>, "c":<?php echo $_REQUEST["c"] == "" ? 0 : $_REQUEST["c"];?>},
		success: function(data){
			if(data.length > 12) {
    			data = eval("("+data+")");
    			var html="";
    			$.each(data, function(entryIndex, entry){
    				html+="<li><a href='" + entry.url + "' target='main'>" + entry.name + "</a></li>";
    			});
    			$("#menuUL").html(html);
    		}
		}
	});

});

</script>
<?php
if ($c == '')
    $c = 101;
$endmenus = "";
$msql = new Dedesql(false);
$query = "select name from #@__menu where id='$c'";
$menuinfo = $msql->GetOne($query);
?>
<base target="main">
	<body>
		<div class="menu" id="menuDIV">
        
            <dl>
                <dt class="top"><?php echo $menuinfo["name"]?> </dt>
                <dd id="items">
            		<ul id="menuUL" style="display:block;">
            		</ul>
            	</dd>
            </dl>
        </div>
	</body>

</html>