<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>嵿柋暘椶娗棟</title>
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
      <td><strong>&nbsp;嵿柋暘椶娗棟</strong>&nbsp;&nbsp;<a href="cate.php?action=new">嵿柋暘椶捛壛</a> | <a href="cate.php">暘椶堦棗</a></td>
     </tr>
	 <form action="cate.php?action=save" method="post">
     
     <tr>
      <td bgcolor="#FFFFFF">
              <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
<tr class='row_color_head'>
<td>ID</td><td>柤徧</td>
</tr>
<tr><td>ID:1</td><td><img src=images/cate.gif align=absmiddle>&nbsp;廂擖</td></tr>
<tr><td>ID:2</td><td><img src=images/cate.gif align=absmiddle>&nbsp;巟弌</td></tr>
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
