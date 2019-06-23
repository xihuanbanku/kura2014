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
<title><?php echo $cfg_softname;?>部門選択</title>
<script language="javascript">
function selectgys(value){
window.opener.document.<?php echo $form ?>.<?php echo $field ?>.value=value; 
window.close(); 
return false; 
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
      <td><strong>&nbsp;部門を選択してください</strong></td>
     </tr>
	 <form method="post" name="sel">
     <tr>
      <td bgcolor="#FFFFFF">
       <?php
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   $csql->SetQuery("select * from #@__part");
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;部門がありません、追加してください。<a href=system_part.php?action=new>部門追加</a>。</td></tr>";
	   else{
	   echo "<tr class='row_color_head'><td>ID</td><td>名称</td><td>選択</td></tr>";
	   while($row=$csql->GetArray()){
	   echo "<tr><td>ID：".$row['id']."</td><td>&nbsp;".$row['p_name']."</td><td><input type='checkbox' name='sel_gys".$row['p_name']."' value='".$row['p_name']."' onclick='selectgys(this.value)'></td></tr>";
	   }
	   }
	   echo "</table>";
	  
	   $csql->close();
	   ?>
	  </td>
     </tr></form>
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
</body>
</html>
