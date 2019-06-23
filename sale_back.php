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
<title>顧客返品</title>
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
window.open('sale_list.php?form=form1&field=seek_text','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=750,height=500,top=100,left=120,scrollbars=yes');
}
function getinfo1(){
window.open('member_list.php?form=form1&field=member','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=500,top=100,left=320,scrollbars=yes');
}

function putrkinfo(){
pid=document.forms[0].seek_number.value;
did=document.forms[0].r_dh.value;
number=document.forms[0].rk_number.value;
r_text=document.forms[0].r_text.value;
member=document.forms[0].member.value;
sdh=document.forms[0].s_dh.value;

if(pid==''){
alert('返品商品を選択してください。');
return false;
}
if(number=='' || (!isInteger(number)) || number<=0){
alert('正しい返品数を入力してください。');
return false;
}
if(r_text==''){
alert('返品の原因を入力してください。');
return false;
}
url="order_sale_back.php?pid="+pid+"&did="+did+"&num="+number+"&r_text="+r_text+"&member="+member+"&sdh="+sdh;
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
$query="select * from #@__reportsback";
$rs->SetQuery($query);
$rs->Execute();
$rowcount=$rs->GetTotalRow();
$cdh="Vl".str_replace('-','',GetDateMk(date(time())))."-".($rowcount+1);
 $rs->close();
 
if ($action=='save'){

$bsql=New Dedesql(false);
$query="select * from #@__saleback where rdh='$r_dh'";
$bsql->SetQuery($query);
$bsql->Execute();
$rowcount=$bsql->GetTotalRow();
if ($rowcount==0){
 ShowMsg('不正引数、または返品商品がありません。','-1');
 exit();
}
else{
 checkbank();
 $money=0;
 while($row=$bsql->getArray()){
 $money+=$row['number']*getsale($row['productid']);
 $csql=New dedesql(false);
 $csql->setquery("select * from #@__mainkc where p_id='".$row['productid']."'");
 $csql->execute();
 $totalrec=$csql->gettotalrow();
 if($totalrec!=0){
  $csql->executenonequery("update #@__mainkc set number=number+".$row['number']." where p_id='".$row['productid']."'");
  }
 }
 $csql->close(); 
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 WriteNote(' 返品表 '.$r_dh.'保存しました。',$logindate,$loginip,$username);
 $newsql=New dedesql(false);
 $newsql->executenonequery("insert into #@__reportsback(r_dh,r_people,r_date,r_status) values('$r_dh','$r_people','$r_date','0')");
 
 $newsql->executenonequery("insert into #@__accounts(atype,amoney,abank,dtime,apeople,atext) values('支出','".$money."','".BANKID."','".$r_date."','".$r_people."','返品表番号：".$r_dh."')");

 $newsql->executenonequery("update #@__bank set bank_money=bank_money-".$money." where id='".BANKID."'");
 $newsql->close();
 ShowMsg('返品処理を行いました。','sale_back.php');
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
      <td><strong>&nbsp;顧客返品管理</strong>&nbsp;&nbsp;- <a href="sale_back.php">新返品表</a></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
<?php
$orderstring=" order by r_date desc";
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if(GetCookie('rank')==1)
	   $query="select * from #@__reportsback".$orderstring;
	   else
	   $query="select * from #@__reportsback where r_people='".GetCookie('VioomaUserID')."'".$orderstring;
       $csql=New Dedesql(false);
	   $dlist = new DataList();
       $dlist->pageSize = $cfg_record;
       $dlist->SetParameter("action",$action);
       $dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>返品表番号</td>
	   <td>担当者</td>
	   <td>返品時間</td>
	   <td>審査状態</td>
	   <td>その他</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   if($row['r_status']==1){
	   if(Getcookie('rank')=='1')
	   $statusstring="<a href='?action=sure&t=no&id=".$row['id']."'><img src='images/yes.png' alt='審査取消' border='0'></a>";
	    else
	   $statusstring="<img src='images/yes.png' alt='審査取消' border='0'>";
	   $printstring=" | <a href=report_s_back.php?action=save&type=other&sday=".$row['r_dh'].">印刷</a>";
	   }
	   else{
	   if(Getcookie('rank')=='1')
	   $statusstring="<a href='?action=sure&t=yes&id=".$row['id']."'><img src='images/no.png' alt='審査' border='0'></a>";
	    else
	   $statusstring="<img src='images/no.png' alt='審査' border='0'>";
	   $printstring="";
	   }
	   echo "<tr><td>ID号:".$row['id']."</td><td>&nbsp;".$row['r_dh']."</td><td>&nbsp;".$row['r_people']."</td><td>&nbsp;".$row['r_date']."</td><td>&nbsp;".$statusstring."</td><td><span onclick=showsubinfo(".$row['id'].") style='cursor:hand;'>展開</span> ".$printstring."</td></tr>";
	   echo "<tr id='rk_subinfo".$row['id']."' style='display:none;'><td colspan='6'><br><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\" align=\"center\">";
	   
	   $csql1=New Dedesql(false);
	   $csql1->SetQuery("select * from #@__saleback where rdh='".$row['r_dh']."'");
	   $csql1->Execute();
	   $rowcount=$csql1->GetTotalRow();
	   echo "<tr class='row1_color_head'><td>品番</td><td>名称</td><td>規格</td><td>分類</td><td>単位</td><td>販売単価</td><td>返品原因<td>返品数</td><td>その他</tr>";
	   while($row=$csql1->GetArray()){
	   $nsql=New dedesql(false);
	   $query1="select * from #@__basic where cp_number='".$row['productid']."'";
	   $nsql->setquery($query1);
	   $nsql->execute();
	   $row1=$nsql->getone();
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"><td>".$row['productid']."</td><td>&nbsp;".$row1['cp_name']."</td><td>".$row1['cp_gg']."</td><td>".get_name($row1['cp_categories'],'categories').">".get_name($row1['cp_categories_down'],'categories')."</td><td>".get_name($row1['cp_dwname'],'dw')."</td><td>￥".$row1['cp_sale']."</td><td>".$row['r_text']."</td><td>".$row['number']."</td><td><a href=''></a></td></tr>";
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
 $query="update #@__reportsback set r_status=1 where id='$id'";
 else
 $query="update #@__reportsback set r_status=0 where id='$id'";
 $susql->executenonequery($query);
 $susql->close();
 showmsg('審査状態が変更しました。','sale_back.php?action=seek');
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
      <td><strong><strong>&nbsp;顧客返品管理</strong>&nbsp;&nbsp;- <a href="sale_back.php">新返品表</a> - <a href="sale_back.php?action=seek">返品表検索</a></td>
     </tr><form action="sale_back.php?action=save" method="post" name="form1">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
    <tr height="30">
    <td class="cellcolor">返品表番号：</td>
    <td class="cellcolor">&nbsp;<input type="text" name="r_dh" value="<?php echo $cdh; ?>" readonly class="rtext" size="15">&nbsp;(担当者：<input type="text" name="r_people" value="<?php echo getcookie('VioomaUserID'); ?>" readonly class="rtext" size="8">返品時間：<input type="text" name="r_date" value="<?php echo GetDateTimeMk(time());?>"  readonly class="rtext">)</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">商品検索情報：<br></td>
    <td>&nbsp;<input type="text" name="seek_text" value="商品情報選択" onclick="getinfo()">&nbsp;(クイック検索)
	<input type="hidden" name="seek_number" value=""/><input type="hidden" name="s_dh" value=""/>
	</td>
  </tr> 
  <tr>
    <td class="cellcolor" width="30%">返品数：<br></td>
    <td>&nbsp;<input type="text" name="rk_number" size="5"><input type="text" class="rtext" name="showdw" readonly size="5">(返品数＞販売数の場合、全部返品とする)
	</td>
  </tr>   
  <tr>
    <td class="cellcolor" width="30%">顧客：<br></td>
    <td>&nbsp;<input type="text" name="member">&nbsp;<input type="button" value="顧客選択" onclick="getinfo1()">
	</td>
  </tr> 
    <td class="cellcolor" width="30%">返品原因：<br></td>
    <td>&nbsp;<textarea name="r_text" rows="2" cols="40"></textarea>
	</td>
  </tr>    
  <tr id="product_date" style="display:block;">
   <td colspan="2">
   &nbsp;商品情報：<input type="text" class="rtext" style="width:80%;" name="showinfo" readonly>
   </td>
  </tr> 
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;<input type="button" value=" 該当返品情報に登録 " onclick="putrkinfo()">&nbsp;&nbsp;<input type="submit" value="返品情報を保存"></td>
  </tr></form>
  <tr>
   <td colspan="2">
   <iframe src="order_sale_back.php?pid=&did=<?php echo $cdh ?>&action=normal" width="100%" height="400" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0" name="current_order" od="current_order"></iframe>
   </td>
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
