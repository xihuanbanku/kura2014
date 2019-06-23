<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/page.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>商品コード選択</title>
<script language="javascript">
function selectpro(productid,barcode,cp_categories,cp_categories_down){
    window.opener.document.<?php echo $form ?>.cp_number.value = productid;
    window.opener.document.<?php echo $form ?>.cp_tm.value = barcode;
    window.opener.document.<?php echo $form ?>.cp_categories.value = cp_categories;
    window.opener.document.<?php echo $form ?>.cp_categories.onchange();
    window.opener.document.<?php echo $form ?>.cp_categories_down.value = cp_categories_down;

    // バッテリー
    if (cp_categories === "13") {
        window.opener.document.<?php echo $form ?>.titgoods.value = "【対応バッテリーの品番】";
        window.opener.document.<?php echo $form ?>.titcomputer.value = "【対応パソコンの型番】";
    // キーバード
    } else if (cp_categories === "22") {
         window.opener.document.<?php echo $form ?>.titgoods.value = "【対応キーボートの品番】";
         window.opener.document.<?php echo $form ?>.titcomputer.value = "【対応パソコンの型番】";
    // ファン
    } else if (cp_categories === "20") {
         window.opener.document.<?php echo $form ?>.titgoods.value = "";
         window.opener.document.<?php echo $form ?>.titcomputer.value = "対応機種:";
    // 液晶パネル
    } else if (cp_categories === "90") {
         window.opener.document.<?php echo $form ?>.titgoods.value = "【対応液晶の品番】";
         window.opener.document.<?php echo $form ?>.titcomputer.value = "【対応パソコンの型番】";
    // ACアダプター
    } else if (cp_categories === "15") {
        window.opener.document.<?php echo $form ?>.titgoods.value = "";
        window.opener.document.<?php echo $form ?>.titcomputer.value = "【適合機種】";
        // 雑貨
    } else if (cp_categories === "141") {
        window.opener.document.<?php echo $form ?>.titgoods.value = "";
        window.opener.document.<?php echo $form ?>.titcomputer.value = "商品詳細";
    }
    window.opener.document.<?php echo $form ?>.titgoodcode.value = "商品コード：";
    window.opener.document.<?php echo $form ?>.cp_detail_3.value = productid;
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
  window.location.href="code_list.php?form=<?php echo $form;?>&field=<?php echo $field;?>&cp_categories="+locationid;
} 

function chgurl(cid){
    cp_cate=document.forms[0].cp_categories.value;
    if(cp_cate=='') {
        window.location.href="code_list.php?form=<?php echo $form;?>&field=<?php echo $field;?>&cp_categories=&cp_categories_down="+cid;
    } else {
        window.location.href="code_list.php?form=<?php echo $form;?>&field=<?php echo $field;?>&cp_categories="+cp_cate+"&cp_categories_down="+cid;
    }

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
	    <tr><form action="code_list.php?action=seek&form=<?php echo $form; ?>&field=<?php echo $field; ?>" name="form1" method="post">
		 <td>
	  <strong>&nbsp;商品コード選択</strong>
	     </td>
		 <td align="right">分類：
		 <?php
                    getcategories1($cp_categories,$cp_categories_down,"onchange=chgurl(this.value)");
	         ?>
		 &nbsp;&nbsp;商品コード：<input type="text" name="stext" size="15" value="<?php echo $stext; ?>">
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
       // 空白除く
       $stext = delSpace($stext);
       // 大文字へ変換
       $stext = strtoupper($stext);
       
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if($cp_categories !='' and $cp_categories_down != '') {
               $query = "select barcode.id, barcode.productid, barcode.barcode, barcode.cp_categories, barcode.cp_categories_down, basic.cp_number "
                   . " from #@__barcode barcode left join #@__basic basic on basic.cp_number = barcode.productid "
                   . " where barcode.productid like '%".$stext."%' and basic.cp_number is null and barcode.cp_categories = '$cp_categories' "
                   . " and barcode.cp_categories_down = '$cp_categories_down' ";
           } else if ($cp_categories !='' and $cp_categories_down == '') {
               $query = "select barcode.id, barcode.productid, barcode.barcode, barcode.cp_categories, barcode.cp_categories_down, basic.cp_number "
                   . " from #@__barcode barcode left join #@__basic basic on basic.cp_number = barcode.productid "
                   . " where barcode.productid like '%".$stext."%' and basic.cp_number is null and barcode.cp_categories = '$cp_categories'";
           } else {
               $query = "select barcode.id, barcode.productid, barcode.barcode, barcode.cp_categories, barcode.cp_categories_down, basic.cp_number "
                   . " from #@__barcode barcode left join #@__basic basic on basic.cp_number = barcode.productid "
                   . " where barcode.productid like '%".$stext."%' and basic.cp_number is null ";
           }
           $query .= " order by productid ";
           
$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;
$dlist->SetParameter("form",$form);
$dlist->SetParameter("field",$field);
$dlist->SetParameter("cp_categories",$cp_categories);
$dlist->SetParameter("cp_categories_down",$cp_categories_down);
if($action=='seek'){
$dlist->SetParameter("action",$action);
$dlist->SetParameter("stext",$stext);
}
$dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>商品コード</td>
	   <td>バーコード</td>
           <td>分類</td>
	   <td>選択</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
           echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n";
	   echo "<td><center>".$row['productid']."</td>
	   <td><center>".$row['barcode']."</td>
           <td><center>".get_name($row['cp_categories'],'categories')."&nbsp;⇒&nbsp;".get_name($row['cp_categories_down'],'categories')."</td>
	   <td><center><input type='checkbox' name='sel_pro".$row['id']."' value='".$row['cp_name']."' "
                   . "onclick=\"selectpro('".$row['productid']."','".$row['barcode']."','".$row['cp_categories']."','".$row['cp_categories_down']."')\"></td>\r\n
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
