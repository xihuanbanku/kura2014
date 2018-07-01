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
<title><?php echo $cfg_softname;?>在庫切れ検索</title>
<script language = "JavaScript">
    function setChk(objChk) {
        strChk = document.getElementById("strChk");
        if (objChk.checked) {
            if (strChk.value !== '' && strChk.value !== '0') {
                strChk.value = strChk.value + ",'" + objChk.value + "'";
            } else {
                strChk.value = "'" + objChk.value + "'";
            }
        } else {
            strChkVal = strChk.value;
            strChkSize = strChkVal.split(",");
            if (strChkSize.length > 1) {
                strChkVal = strChkVal.replace(",'" + objChk.value + "'", "");
                strChkVal = strChkVal.replace("'" + objChk.value + "',", "");
            } else {
                strChkVal = "";
            }
            strChk.value = strChkVal;
        }
    }

    function out_excel(){
        st = document.forms[0].stext.value;
        strChk = document.getElementById("strChk").value;
        window.open('excel_kc_lost.php?&strChk=' + strChk + '&stext=' + st);
    }
    
    function chkAll() {
        var frm=document.sel;
        var chkall = frm.hogeAll;
        var strChk = document.getElementById("strChk");
        if (chkall.checked) {
            for(var i=0; i<frm.length; i++) {
                frm.elements[i].checked = true;
                if (strChk.value !== '' && strChk.value !== '0') {
                    strChk.value = strChk.value + ",'" + frm.elements[i].value + "'";
                } else {
                    strChk.value = "'" + frm.elements[i].value + "'";
                }
            }
        } else {
            for(var i=0; i<frm.length; i++) {
                frm.elements[i].checked = false;
            }
            strChk.value = "";
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
	    <tr><form action="system_kc_lost.php?action=seek" name="form1" method="post">
		 <td>
	  <strong>&nbsp;在庫切れ管理</strong>
	     </td>
		 <td align="right">
		 <?php if($action=='seek')
		 echo "在庫数＜<input type=\"text\" name=\"stext\" size=\"5\" VALUE=\"".$stext."\">の商品<input type=\"submit\" value=\"検索\">";
		 else
		 echo "在庫数＜<input type=\"text\" name=\"stext\" size=\"5\" VALUE=\"5\">の商品<input type=\"submit\" value=\"検索\">";
		 ?>
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
   if($stext=='' || !is_numeric($stext) || $stext<0)echo "<script>alert('正しい数字を入力してください');history.go(-1);</script>";
	$query="select * from #@__mainkc,#@__basic where  #@__mainkc.number<'$stext' and #@__mainkc.p_id=#@__basic.cp_number order by cp_number";
	   }
   else
    $query="select * from #@__mainkc,#@__basic where #@__mainkc.number<5  and #@__mainkc.p_id=#@__basic.cp_number order by cp_number";
$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;

if($action=='seek'){
$dlist->SetParameter("action",$action);
$dlist->SetParameter("stext",$stext);
}
$dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
           <td><input type='checkbox' name='hogeAll' onClick=\"chkAll()\">選択&nbsp;&nbsp;</td>
	   <td>商品コード</td>
	   <td>状态</td>
	   <td>タイトル</td>
	   <td>倉庫</td>
           <td>在庫位置<br>階 - 棚 - ゾーン - 横 - 縦</td>
	   <td>仕入れ数</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
           if ($row['l_id'] == "1") {
               echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">\r\n";
           } elseif ($row['l_id'] == "2") {
               echo "<tr bgcolor=\"#FFE4B5\" onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFE4B5';\">\r\n";
           }
           echo "<td width=\"5%\"><center><input type='checkbox' name='hoge' onClick=\"setChk(this)\" value='".$row['p_id']."'></td>
	   <td><center>&nbsp;".$row['p_id']."</td>
           <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row['cp_name']."</td>
	   <td width=\"50%\">&nbsp;".$row['cp_title']."</td>
	   <td><center>&nbsp;&nbsp;".get_name($row['l_id'],'lab')."</td>
           <td><center>".$row['l_floor']."-".$row['l_shelf']."-".$row['l_zone']."-".$row['l_horizontal']."-".$row['l_vertical']."</td>
	   <td><center><font color=red>".$row['number']."</font></td>
	   \r\n</tr>";
	   }
	   echo "<tr><td colspan='8'>&nbsp;".$dlist->GetPageList($cfg_record)."</td></tr>";
	   echo "</table>";
	   $csql->close();
	   ?>
	  </td>
     </tr>
         <tr>
            <td align="right">
            <input type="hidden" name="strChk" id="strChk">
            <input type="button" onclick="out_excel()" value="仕入用データ出力" />
            </td>
         </form>
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
