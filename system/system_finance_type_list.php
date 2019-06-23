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
<title><?php echo $cfg_softname;?>财务类型管理</title>
<style type="text/css">
tr{
	
}
</style>
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
      <td><strong>&nbsp;财务分類</strong><a href="system_finance_type_edit.php?action=new">新增</a></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
		 <th id="row_style">名称</th>
		 <th id="row_style">操作</th>
	    </tr>
       <?php 
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results("select id, name from jxc_finance_type a where p_id =1 order by id");
        if($results) {
            foreach($results as $result) {
                echo "<tr><td>┏{$result->name}</td>
            		 <td><a href=\"system_finance_type_edit.php?id={$result->id}\">修改</a>
                |<a onclick='return confirm(\"确认?\")' href=\"system_finance_type_edit.php?action=del&id={$result->id}\">删除</a></td></tr>";
                $child1_results = $newsql->get_results("select id, name from jxc_finance_type a where p_id ={$result->id} order by id");
                foreach($child1_results as $child1_result) {
                    echo "<tr style=\"background-color: #EBF1F6;\"><td>┗━━━┳{$child1_result->name}</td>
                    <td><a href=\"system_finance_type_edit.php?id={$child1_result->id}\">修改</a>
                    |<a onclick='return confirm(\"确认?\")' href=\"system_finance_type_edit.php?action=del&id={$child1_result->id}\">删除</a></td></tr>";
                    $child2_results = $newsql->get_results("select id, name from jxc_finance_type a where p_id ={$child1_result->id} order by id");
                    foreach($child2_results as $child2_result) {
                        echo "<tr style=\"background-color: #c6dbef;\"><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━━━{$child2_result->name}</td>
                        <td><a href=\"system_finance_type_edit.php?id={$child2_result->id}\">修改</a>
                        |<a onclick='return confirm(\"确认?\")' href=\"system_finance_type_edit.php?action=del&id={$child2_result->id}\">删除</a></td></tr>";
                    }
                }
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

