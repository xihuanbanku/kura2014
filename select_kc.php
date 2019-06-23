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
function selectpro(value,id,gg,dw,number,lid,lstring){
window.opener.document.<?php echo $form ?>.<?php echo $field ?>.value=value; 
window.opener.document.<?php echo $form ?>.seek_number.value=id; 
window.opener.document.<?php echo $form ?>.showinfo.value="商品名："+value+"  仕様："+gg+" 在庫："+number; 
window.opener.document.<?php echo $form ?>.showdw.value=dw;
window.opener.document.<?php echo $form ?>.labtext.value=lstring;
window.opener.document.<?php echo $form ?>.labid2.value=lid; 
window.opener.document.<?php echo $form ?>.number.value=number; 
window.close(); 
return false; 
}
</script>
<script language = "JavaScript">
var onecount; 
onecount = 0; 
subcat = new Array();
<?php
$count=0;
$rsql=New Dedesql(false);
$rsql->SetQuery("select * from #@__categories where reid!=0");
$rsql->Execute();
while($rs=$rsql->GetArray()){
?>
subcat[<?php echo $count;?>] = new Array("<?php echo $rs['categories'];?>","<?php echo $rs['reid'];?>","<?php echo $rs['id'];?>");
<?php 
    $count++;
}
$rsql->close();
?>
onecount=<?php echo $count?>; 
function getCity(locationid) 
{ 
    document.form1.cp_categories_down.length = 0; 

    var locationid=locationid; 

    var i; 
    document.form1.cp_categories_down.options[0] = new Option('小分類選択',''); 
    for (i=0;i < onecount; i++) 
    { 
        if (subcat[i][1] == locationid) 
        { 
        document.form1.cp_categories_down.options[document.form1.cp_categories_down.length] = new Option(subcat[i][0], subcat[i][2]);
        } 
    } 

} 
function chgurl(cid){
    cp_cate=document.forms[0].cp_categories.value;
    if(cp_cate=='')
    window.location.href="select_kc.php?form=<?php echo $form;?>&field=<?php echo $field;?>&sort=<?php echo $sort;?>&cp_categories=&cp_categories_down="+cid;
    else
    window.location.href="select_kc.php?form=<?php echo $form;?>&field=<?php echo $field;?>&sort=<?php echo $sort;?>&cp_categories="+cp_cate+"&cp_categories_down="+cid;
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
	    <tr><form action="select_kc.php?action=seek&form=<?php echo $form; ?>&field=<?php echo $field; ?>" name="form1" method="post">
		 <td>
	  <strong>&nbsp;商品を選択してください</strong>
	     </td>
		 <td align="right">分類：
		 <?php
		 getcategories1($cp_categories,$cp_categories_down," onchange=chgurl(this.value)");
	         ?>
		 <select name="sort">
		 <option value="1" <?php if ($sort==1) echo "selected" ?>>商品コード検索</option>
		 <option value="2" <?php if ($sort==2) echo "selected" ?>>ﾊﾞｰｺｰﾄﾞ検索</option>
		 <option value="3" <?php if ($sort==3) echo "selected" ?>>商品詳細検索</option>
		 <option value="4" <?php if ($sort==4) echo "selected" ?>>ｷｰﾜｰﾄﾞ検索</option>
		 </select>
		 <input type="text" name="stext" size="15" value="<?php echo $stext; ?>"><input type="submit" value="検索">
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
           if($cp_categories=='')$catestring1='';
	   else
	   $catestring1=" and #@__basic.cp_categories='$cp_categories'";
	   if($cp_categories_down=='')$catestring2='';
	   else
	   $catestring2=" and #@__basic.cp_categories_down='$cp_categories_down'";
	   $catestring=$catestring1.$catestring2;
           
	   if($action=='seek'){
	   switch($sort){
	    case "1":
		$query="select * from #@__mainkc,#@__basic where #@__mainkc.p_id=#@__basic.cp_number and #@__basic.cp_number LIKE '%".'$stext'."%'".$catestring;
		break;
		case "2":
		$query="select * from #@__mainkc,#@__basic where #@__mainkc.p_id=#@__basic.cp_number and #@__basic.cp_tm LIKE '%".'$stext'."%'".$catestring;
		break;
		case "3":
		$query="select * from #@__mainkc,#@__basic where #@__mainkc.p_id=#@__basic.cp_number and (cp_detail LIKE '%".$stext."%' or cp_title LIKE '%".$stext."%')".$catestring;
		break;
		case "4":
		$query="select * from #@__mainkc,#@__basic where #@__mainkc.p_id=#@__basic.cp_number and cp_helpword LIKE '%".$stext."%'".$catestring;
		break;
		}
	   }
	   else
       $query="select * from #@__mainkc,#@__basic where #@__mainkc.p_id=#@__basic.cp_number";
       $query.=" order by #@__mainkc.dtime desc, #@__mainkc.p_id";
$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;
$dlist->SetParameter("form",$form);
$dlist->SetParameter("field",$field);
if($action=='seek'){
$dlist->SetParameter("action",$action);
$dlist->SetParameter("cp_categories",$cp_categories);
$dlist->SetParameter("cp_categories_down",$cp_categories_down);
$dlist->SetParameter("sort",$sort);
$dlist->SetParameter("stext",$stext);
}
$dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>商品コード</td>
	   <td>メーカー・商品名</td>
	   <td>タイトル</td>
           <td>倉庫</td>
	   <td>在庫位置</td>
           <td>販売価格</td>
           <td>在庫数</td>
	   <td>選択</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
           if ($row['l_id'] == "1") {
               echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n";
           } elseif ($row['l_id'] == "2") {
               echo "<tr bgcolor=\"#FFE4B5\" onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFE4B5';\">\r\n";
           }
	   echo "<td>".$row['cp_number']."</td>
	   <td width=\"15%\">&nbsp;".$row['cp_name']."</td>
	   <td width=\"48%\">&nbsp;".$row['cp_title']."&nbsp;&nbsp;</td>
           <td>&nbsp;".get_name($row['l_id'],'lab')."</td>
	   <td><center>".$row['l_floor']."-".$row['l_shelf']."-".$row['l_zone']."-".$row['l_horizontal']."-".$row['l_vertical']."</td>
           <td align=\"right\"><font color=\"blue\">&yen;".number_format($row['cp_sale1'])."</font>&nbsp;&nbsp;</td>
           <td align=\"right\"><font color=\"red\">".$row['number']."</font>&nbsp;&nbsp;</td>
	   <td><center><input type='checkbox' name='sel_pro".$row['id']."' value='".$row['cp_name']."' "
                   . "onclick=\"selectpro(this.value,'".$row['cp_number']."','".$row['cp_title']."','".get_name($row['cp_dwname'],'dw')."',"
                   . "'".$row['number']."','".$row['l_id']."','".get_name($row['l_id'],'lab')."')\"></td>\r\n
	   </tr>";
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
