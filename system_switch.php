<?php
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/page.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title>倉庫調達管理</title>
<style type="text/css">
.rtext {background:transparent;border:0px;color:red;font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
</style>
<script language="javascript">
function isInteger(sNum) { 
var num 
num=new RegExp('[^0-9_]','') 
if (isNaN(sNum)) { 
return false 
} 
else { 
if (sNum.search(num)>=0) { 
return false 
} 
else { 
return true 
} 
} 
} 

function getinfo(){
window.open('select_kc.php?form=form1&field=seek_text&sort=3','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=980,height=600,top=100,left=120,scrollbars=yes');
}
function putrkinfo(){
pid=document.forms[0].seek_number.value;
did=document.forms[0].r_dh.value;
lid=document.forms[0].labid2.value;
lid2=document.forms[0].labid.value;
number=document.forms[0].rk_number.value;
kcnumber= document.forms[0].number.value;

if(pid==''){
    alert('入庫商品を選択してください。');
    return false;
}
if (lid == lid2) {
    alert('同じ倉庫の調達ができません。');
    return false;
}
if(number=='' || (!isInteger(number)) || number<=0){
    alert('正しい入庫数を入力してください。');
    return false;
}
if (parseInt(number) > parseInt(kcnumber)) {
    alert('在庫数を超えています。確認してください。');
    return false;
}
url="sw_order.php?pid="+pid+"&did="+did+"&lid="+lid+"&num="+number+"&tolid="+lid2;
var obj=window.frames["current_order"];
 obj.window.location=url;
}

function showsubinfo(tbnum){
whichEl = eval("rk_subinfo" + tbnum);
if (whichEl.style.display == "none"){eval("rk_subinfo" + tbnum + ".style.display=\"\";");}
else{eval("rk_subinfo" + tbnum + ".style.display=\"none\";");}
}
</script>
</head>
<?php

$rs=New Dedesql(falsh);
$query="select * from #@__reportswitch";
$rs->SetQuery($query);
$rs->Execute();
$rowcount=$rs->GetTotalRow();
$cdh="SW".str_replace('-','',GetDateMk(date(time())))."-".($rowcount+1);
 $rs->close();
 
if ($action=='save'){

$bsql=New Dedesql(false);
$query="select * from #@__switch where rdh='$r_dh'";
$bsql->SetQuery($query);
$bsql->Execute();
$rowcount=$bsql->GetTotalRow();
if ($rowcount==0){
 ShowMsg('引数エラー、または商品がありません!','-1');
 exit();
}
else{
 while($row=$bsql->getArray()){
 $csql=New dedesql(false);
 
 $csql->ExecuteNoneQuery("update #@__mainkc set number=number-".$row['number']." where p_id='".$row['productid']."' and l_id='".$row['fromlab']."'");
 
 $csql->setquery("select * from #@__mainkc where p_id='".$row['productid']."' and l_id='".$row['tolab']."'");
 $csql->execute();
 $totalrec=$csql->gettotalrow();
 if($totalrec==0)
  $csql->executenonequery("insert into #@__mainkc(p_id,l_id,number) values('".$row['productid']."','".$row['tolab']."','".$row['number']."')");
  else
  $csql->executenonequery("update #@__mainkc set number=number+".$row['number']." where p_id='".$row['productid']."' and l_id='".$row['tolab']."'");
 }
 $csql->close(); 
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 WriteNote(' 調達表 '.$r_dh.' を保存しました。',$logindate,$loginip,$username);
 $newsql=New dedesql(false);
 $newsql->executenonequery("insert into #@__reportswitch(r_dh,r_people,r_date,r_status) values('".$r_dh."','".$r_people."','".$r_date."','0')");
 $newsql->close();
 ShowMsg('倉庫調達完了しました。','system_switch.php');
$bsql->close();
exit();
    }
}
else if($action=='seek'){
?>
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
      <td><strong>&nbsp;倉庫調達管理</strong>&nbsp;&nbsp;- <a href="system_switch.php">調達表新規作成</a></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
<?php
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   $query="select * from #@__reportswitch";
       $csql=New Dedesql(false);
	   $dlist = new DataList();
       $dlist->pageSize = $cfg_record;
       $dlist->SetParameter("action",$action);
       $dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>調達表番号</td>
	   <td>担当者</td>
	   <td>時間</td>
	   <td>審査状態</td>
	   <td>操作</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   if($row['r_status']==1){
	   $statusstring="<a href='?action=sure&t=no&id=".$row['id']."'><img src='images/yes.png' alt='審査取消' border='0'></a>";
	   $printstring=" | <a href=report_rk.php?action=save&type=other&sday=".$row['r_dh'].">印刷</a>";
	   }
	   else{
	   $statusstring="<a href='?action=sure&t=yes&id=".$row['id']."'><img src='images/no.png' alt='審査' border='0'></a>";
	   $printstring="";}
	   echo "<tr><td>ID：".$row['id']."</td><td>&nbsp;".$row['r_dh']."</td><td>&nbsp;".$row['r_people']."</td><td>&nbsp;".$row['r_date']."</td><td>&nbsp;".$statusstring."</td><td><span onclick=showsubinfo(".$row['id'].") style='cursor:hand;'>詳細</span>".$printstring."</td></tr>";
	   echo "<tr id='rk_subinfo".$row['id']."' style='display:none;'><td colspan='6'><br><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\" align=\"center\">";
	   
	   $csql1=New Dedesql(false);
	   $csql1->SetQuery("select * from #@__switch where rdh='".$row['r_dh']."'");
	   $csql1->Execute();
	   $rowcount=$csql1->GetTotalRow();
	   echo "<tr class='row1_color_head'>
	   <td><center>商品コード</td>
	   <td><center>メーカー・商品名</td>
	   <td width=\"60%\"><center>タイトル</td>
	   <td><center>調達数</td>
	   <td><center>調達状況</td>
	   </tr>";
	   while($row=$csql1->GetArray()){
	   $nsql=New dedesql(false);
	   $query1="select * from #@__basic where cp_number='".$row['productid']."'";
	   $nsql->setquery($query1);
	   $nsql->execute();
	   $row1=$nsql->getone();
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"><td>"
           .$row['productid']."</td><td>&nbsp;".$row1['cp_name']."</td><td>".$row1['cp_title']."</td>"
           ."<td><center>".$row['number']."</td><td>".get_name($row['fromlab'],'lab')." ⇒ ".get_name($row['tolab'],'lab')." </td></tr>";
	   $nsql->close();
	   }
	   $csql1->close();
	   echo "</table><br></td></tr>\r\n";
	   }
	   $csql->close();
   echo "<tr><td colspan='6'>&nbsp;".$dlist->GetPageList($cfg_record)."</td></tr></table>\r\n </td></tr></table>
 </td></tr>  <tr>
    <td id=\"table_style\" class=\"l_b\">&nbsp;</td>
    <td>&nbsp;</td>
    <td id=\"table_style\" class=\"r_b\">&nbsp;</td>
  </tr>
</table>";
 }
 else if($action=='sure'){
 $susql=new dedesql(false);
 if($t=='yes')
 $query="update #@__reportswitch set r_status=1 where id='$id'";
 else
 $query="update #@__reportswitch set r_status=0 where id='$id'";
 $susql->executenonequery($query);
 $susql->close();
 showmsg('審査状況変更しました。','system_switch.php?action=seek');
 }
 else{
?>
<body onload="form1.seek_text.focus()">
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
      <td><strong><strong>&nbsp;在庫調達管理</strong>(※：オレンジ色が必須項目)&nbsp;&nbsp;- <a href="system_switch.php">調達表新規作成</a> - <a href="system_switch.php?action=seek">調達表検索</a></td>
     </tr><form action="system_switch.php?action=save" method="post" name="form1">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
    <tr height="30">
    <td class="cellcolor">調達表番号：</td>
    <td class="cellcolor">&nbsp;<input type="text" name="r_dh" value="<?php echo $cdh; ?>" readonly class="rtext" size="15">&nbsp;(担当者：<input type="text" name="r_people" value="<?php echo GetCookie('VioomaUserID'); ?>" readonly class="rtext" size="8">      時間：
      <input type="text" name="r_date" value="<?php echo GetDateTimeMk(time());?>"  readonly class="rtext" />
      )</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">商品情報：<br></td>
    <td>&nbsp;
      <input type="text" name="seek_text" id="need" value="" /> &nbsp;
      <input type="button" onclick="getinfo()" value="選択">
	<input type="hidden" name="seek_number" value=""/>	<input type="hidden" name="labid2" value=""/></td>
  </tr> 
  <tr>
    <td class="cellcolor" width="30%">倉庫：<br></td>
    <td>&nbsp;<input type="text" name="labtext" value="" readonly class="rtext"/>⇒&nbsp;&nbsp;移動先:&nbsp;<?php getlab();?></td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">該当在庫数：<br></td>
    <td>
	&nbsp;<input type="text" class="rtext" name="number" size="5">
    </td>
  </tr> 
  <tr>
    <td class="cellcolor" width="30%">調達数：<br></td>
    <td>&nbsp;<input type="text" name="rk_number" id="need" size="5"><input type="text" class="rtext" name="showdw" readonly size="5">	</td>
  </tr>   
  <tr id="product_date" style="display:block;">
   <td colspan="2">
   &nbsp;<input type="text" class="rtext" style="width:80%;" name="showinfo" readonly>   </td>
  </tr> 
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;<input type="button" value=" 該当調達表に登録 " onclick="putrkinfo()">&nbsp;&nbsp;<input type="submit" value="該当調達表を保存"></td>
  </tr></form>
  <tr>
   <td colspan="2">
   <iframe src="sw_order.php?pid=&did=<?php echo $cdh ?>&action=normal" width="100%" height="400" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0" name="current_order" od="current_order"></iframe>   </td>
  </tr>
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

}
copyright();
?>
</body>
</html>
