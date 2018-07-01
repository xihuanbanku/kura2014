<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/page.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>商品選択</title>
<script language="javascript">
function selectpro(value,id,gg,dw,sdh){
window.opener.document.<?php echo $form ?>.<?php echo $field ?>.value=value; 
window.opener.document.<?php echo $form ?>.seek_number.value=id; 
window.opener.document.<?php echo $form ?>.showinfo.value="商品名："+value+"  規格："+gg+" 販売表："+sdh; 
window.opener.document.<?php echo $form ?>.showdw.value=dw; 
window.opener.document.<?php echo $form ?>.s_dh.value=sdh; 
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
      <td>
	   <table width="100%" border="0" cellspacing="0">
	    <tr><form action="sale_list.php?action=seek&form=<?php echo $form; ?>&field=<?php echo $field; ?>" name="form1" method="post">
		 <td>
	  <strong>&nbsp;商品を選択してください</strong>
	     </td>
		 <td align="right">販売表番号：
		 <?php
		 if ($action=='seek')
		 echo "<input type='text' name='s_number' value='".$s_number."' size='12'>&nbsp;品番：<input type='text' name='stext' value='".$stext."' size='12'>";
		 else
         echo "<input type='text' name='s_number' size='12'>&nbsp;品番：<input type='text' name='stext' size='12'>";
	     ?>
<input type="submit" value="検索">
		 &nbsp;&nbsp;
		 </td>
		</tr></form>
	   </table>
	  </td>
     </tr>
	 <form method="post" name="sel">
     <tr>
      <td bgcolor="#FFFFFF">
       <?php
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if($action=='seek'){
	   if($s_number=='')echo "<script>alert('返品用販売表番号を入力してください');history.go(-1);</script>";
		$query="select * from #@__sale where rdh='$s_number' and productid LIKE '%".$stext."%'";
	   }
	   else
       $query="select * from #@__sale";
$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;
$dlist->SetParameter("form",$form);
$dlist->SetParameter("field",$field);
if($action=='seek'){
$dlist->SetParameter("action",$action);
$dlist->SetParameter("s_number",$s_number);
$dlist->SetParameter("stext",$stext);
}
$dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>商品コード</td>
	   <td>名称</td>
	   <td>型番・詳細</td>
	   <td>販売単価</td>
	   <td>仕入先</td>
	   <td>販売表番号</td>
	   <td>選択</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n<td><center>".$row['productid']."</td>
	   <td><center>&nbsp;".get_name($row['productid'],'name')."</td>
	   <td><center>".get_name($row['productid'],'gg')."</td>
	   <td><center>￥".getsale($row['productid'])."</td>
	   <td><center>".get_name($row['productid'],'gys')."</td>
	   <td><center>".$row['rdh']."</td><td><input type='checkbox' name='sel_pro".$row['id']."' value='".get_name($row['productid'],'name')."' onclick=\"selectpro(this.value,'".$row['productid']."','".get_name($row['productid'],'gg')."','".get_name(get_name($row['productid'],'dwname'),'dw')."','".$row['rdh']."')\"></td>\r\n</tr>";
	   }
	   echo "<tr><td colspan='8'>&nbsp;".$dlist->GetPageList($cfg_record)."</td></tr>";
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
