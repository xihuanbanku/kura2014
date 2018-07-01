<?php
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/config_rglobals.php");
require_once(dirname(__FILE__)."/../include/checklogin.php");
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>系统参数管理</title>
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
      <td><strong>&nbsp;系统参数</strong><a href="system_static_edit.php?action=new&pType=<?php echo $_REQUEST["pType"]?>">新增</a></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
        <tr>
		 <td id="row_style">名称</td>
		 <td id="row_style">取值</td>
		 <td id="row_style">说明</td>
		 <td id="row_style">操作</td>
	    </tr>
       <?php
        $pType = $_REQUEST["pType"];
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results("select p_name, p_value,id, description from jxc_static where p_type='{$pType}'") or mysql_error();
        if($results) {
            foreach($results as $result) {
                echo "<tr><td id=\"row_style\">{$result->p_name}</td>
            		 <td id=\"row_style\">{$result->p_value}</td>
            		 <td id=\"row_style\">{$result->description}</td>
            		 <td id=\"row_style\"><a href=\"system_static_edit.php?id={$result->id}&pType={$_REQUEST["pType"]}\">修改</a>
                    |<a onclick='return confirm(\"确认?\")' href=\"system_static_edit.php?action=del&id={$result->id}&pType={$_REQUEST["pType"]}\">删除</a>
                    </td></tr>";
                
            }
        }
       ?>
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
