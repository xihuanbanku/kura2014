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
<title>販売管理</title>
<style type="text/css">
.rtext {background:transparent;border:0px;color:red;font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
.rtext1 {background:transparent;border:0px;font-family:Verdana, Arial, Helvetica, sans-serif;}
</style>
<script language="javascript">
function isInteger(sNum) { 
    var num 
    num=new RegExp('[^0-9_]','') 
    if (isNaN(sNum)) { 
    	return false 
    } else { 
        if (sNum.search(num)>=0) { 
        return false 
        } else { 
        	return true 
        }
    }
} 

function getinfo(){
	window.open('system_basic_list.php?form=form1&field=seek_text&sort=3','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=980,height=600,top=100,left=120,scrollbars=yes');
}
function getinfo1(){
	window.open('member_list.php?form=form1&field=member','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=500,top=100,left=320,scrollbars=yes');
}

function putrkinfo(){
    pid=document.forms[0].seek_number.value;
    did=document.forms[0].r_dh.value;
    transport=document.forms[0].transport.value;
    whopay=document.forms[0].whopay.value;
    transportpay=document.forms[0].transportpay.value;
    member=document.forms[0].member.value;
    number=document.forms[0].rk_number.value;
    salen=document.forms[0].sale.value;
    lid=document.forms[0].labid.value;
    kc_number=parseInt(document.forms[0].number.value);
    positions = document.forms[0].positions.value;
    is_report = document.forms[0].is_report.value;

    if(pid==''){
        alert('商品を選択してください。');
        return false;
    }
    if(number=='' || (!isInteger(number)) || number<=0){
        alert('正しい出庫数を入力してください。');
        return false;
    }
    if(salen=='' || (!isInteger(salen)) || salen<=0){
        alert('正しい販売単価を入力してください。');
        return false;
    }
    
    if(number > kc_number){
        alert('在庫数を超えています。確認してください。');
        return false;
    }
    if(transportpay=='' || (!isInteger(transportpay)) || transportpay<=0){
        alert('正しい送料を入力してください。');
        return false;
    }
    positionlist = positions.split("-");
    url="current_order_sale.php?pid="+pid+"&did="+did+"&is_report="+is_report+"&num="+number+"&sale="+salen+"&labid="+lid+"&member="+member
            +"&floor="+ positionlist[0] +"&shelf="+ positionlist[1] +"&zone="+ positionlist[2] +"&horizontal="+ positionlist[3] +"&vertical="+positionlist[4];
    
    var obj=window.frames["current_order"];
     obj.window.location=url;
}

function showsubinfo(tbnum){
    whichEl = eval("rk_subinfo" + tbnum);
    if (whichEl.style.display == "none") {
        eval("rk_subinfo" + tbnum + ".style.display=\"\";");
    } else{
        eval("rk_subinfo" + tbnum + ".style.display=\"none\";");
    }
}
function setsale(number){
    document.forms[0].sale.value=number;
}
function ConfirmDel()
{
   if(confirm("削除してもよろしいですか。"))
     return true;
   else
     return false;

}
function setcod(obj) {
    var cod = document.forms[0].cod;
    if (obj.checked) {
        cod.value = "315";
        cod.id = "need";
    } else {
        cod.value = "0";
        cod.id = "";
    }
}

function iptMode(obj) {
    if (obj.value === "0") {
        document.getElementById("selBtn").disabled = "";
        document.getElementById("tm").disabled = "disabled";
    } else if (obj.value === "1") {
        document.getElementById("selBtn").disabled = "disabled";
        document.getElementById("tm").disabled = "";
    }
    document.forms[0].tm.focus();
}

function change() {
    document.forms[0].tm.focus();
}
</script>
</head>
<?php

$rs=New Dedesql(falsh);
$query="select * from #@__reportsale where r_people='".GetCookie('VioomaUserID')."' and DATE_FORMAT(r_date,'%Y-%m-%d')='".GetDateMk(date(time()))."'";
$rs->SetQuery($query);
$rs->Execute();
$rowcount=$rs->GetTotalRow();
$cdh="Vs".str_replace('-','',GetDateMk(date(time())))."-Id".substr(GetCookie('VioomaUserID'), -3)."-".($rowcount+1);
 $rs->close();
 
if ($action=='save'){

$bsql=New Dedesql(false);
$query="select * from #@__sale where rdh='$r_dh' and tantousyaid='".GetCookie('VioomaUserID')."'";
$bsql->SetQuery($query);
$bsql->Execute();
$rowcount=$bsql->GetTotalRow();
if ($rowcount==0){
 ShowMsg('不正引数、または該当商品がありません。','-1');
 exit();
}
else{
 //checkbank();
 $money=0;
 while($row=$bsql->getArray()){
 $money+=$row['number']*$row['sale'];
 $csql=New dedesql(false);
 $csql->setquery("select * from #@__mainkc where p_id='".$row['productid']."'");
 $csql->execute();
 $totalrec=$csql->gettotalrow();
 if($totalrec!=0){
  $csql->executenonequery("update #@__mainkc set number=number-".$row['number']." where p_id='".$row['productid']."' and l_id='".$row['salelab']."'"
          . "and l_floor='".$row['labfloor']."' and l_shelf='".$row['labshelf']."' "
          . "and l_zone='".$row['labzone']."' and l_horizontal='".$row['labhorizontal']."' and l_vertical='".$row['labvertical']."'");
  }
 }
 $csql->close(); 
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 WriteNote('販売表 '.$r_dh.' を保存しました。',$logindate,$loginip,$username);
 $newsql=New dedesql(false);
 if($transportpay=="") $transportpay=0;
 if($is_report=="1") {
    $newsql->executenonequery("insert into #@__reportsale(r_dh,r_people,r_date,r_transport,r_whopay,r_transportpay,r_all,r_bank,r_status,r_adid,r_cod,r_chkcod) values('".$r_dh."','".$r_people."','".$r_date."'".",".$transport.",".$whopay.",".$transportpay.",".$money.",".$BANKID.",'0','".$r_people."',".$cod.",'".$chkcod."')");
 }
 if ($whopay==1){
 	$money=$money-$transportpay;
 }
 $newsql->executenonequery("insert into #@__accounts(atype,amoney,abank,dtime,apeople,atext) values('収入','".$money."','".$BANKID."','".$r_date."','".$r_people."','販売番号".$r_dh."')");

 $newsql->executenonequery("update #@__bank set bank_money=bank_money+".$money." where id='".$BANKID."'");
 $newsql->close();
 ShowMsg('該当商品が出庫し、販売されました。','report_sale.php?action=save&type=other&sday='.$r_dh);
$bsql->close();
exit();
    }
}else if($action=='del'){
 $bsql=New Dedesql(false);
 $query="select * from #@__sale where rdh='$rdh' and tantousyaid='".GetCookie('VioomaUserID')."'";
 $bsql->SetQuery($query);
 $bsql->Execute();
 $rowcount=$bsql->GetTotalRow();
 while($row=$bsql->getArray()){
 $csql=New dedesql(false);
 $csql->setquery("select * from #@__sale where rdh='".$rdh."' and tantousyaid='".GetCookie('VioomaUserID')."'");
 $csql->execute();
 $totalrec=$csql->gettotalrow();
 if($totalrec!=0){
  $csql->executenonequery("update #@__mainkc set number=number+".$row['number']." where p_id='".$row['productid']."' and l_id='".$row['salelab']."'");
  }
 }
 $csql->close();
 $newsql=New dedesql(false);
 $newsql->executenonequery("delete from #@__reportsale where id=".$id);
 $newsql->executenonequery("delete from #@__accounts where atype='収入' and atext='販売番号：".$rdh."'");
 if ($whopay==1){
 	$money=$allmoney-$transportpay;
 }else{
 	$money=$allmoney;
 }
 $newsql->executenonequery("update #@__bank set bank_money=bank_money-".$money." where id='".$bank."'");
 $newsql->close();
 ShowMsg('該当販売表が削除されました。一覧画面へ遷移します。','sale.php?action=seek');
 
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
      <td><strong>&nbsp;販売表管理</strong>&nbsp;&nbsp;
          - <input type="button" value="販売表新規" onClick="location.href='sale.php'"> 
      </td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
<?php
$orderstring=" order by r_date desc";
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if(GetCookie('rank')==1 or GetCookie('rank')==100 or GetCookie('rank')==105) {
                $query="select * from #@__reportsale".$orderstring;
           } else {
                $query="select * from #@__reportsale where r_people='".GetCookie('VioomaUserID')."'".$orderstring;
           }
       $csql=New Dedesql(false);
	   $dlist = new DataList();
       $dlist->pageSize = $cfg_record;
       $dlist->SetParameter("action",$action);
       $dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>販売番号</td>
	   <td>担当者</td>
	   <td>販売時間</td>
	   <td>輸送費用</td>
	   <td>合計金額</td>
	   <td>審査状態</td>
	   <td>その他</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   if($row['r_status']==1){
	   if(Getcookie('rank')=='1' or Getcookie('rank')=='100' or Getcookie('rank')=='105')
	   $statusstring="<a href='?action=sure&t=no&id=".$row['id']."'><img src='images/yes.png' alt='審査取消' border='0'></a>";
	    else
	   $statusstring="<img src='images/yes.png' alt='審査取消' border='0'>";
	   $printstring=" | <a href=report_sale.php?action=save&type=other&sday=".$row['r_dh'].">印刷</a>";
	   }
	   else{
	   if(Getcookie('rank')=='1' or Getcookie('rank')=='100' or Getcookie('rank')=='105')
	   $statusstring="<a href='?action=sure&t=yes&id=".$row['id']."'><img src='images/no.png' alt='審査' border='0'></a>";
	    else
	   $statusstring="<img src='images/no.png' alt='審査' border='0'>";
	   $printstring="";
	   $delstring=" | <a href='sale.php?action=del&id=".$row['id']."&rdh=".$row['r_dh']."&whopay=".$row['r_whopay']."&transportpay=".$row['r_transportpay']."&bank=".$row['r_bank']."&allmoney=".$row['r_all']."' onclick='return ConfirmDel();'>削除</a>";
	   }
	   if($row['r_whopay']==0){
	   	$chengdan="お客様負担";
	   }else{
	   	$chengdan="自社負担";
	   }
	   if ($row['r_transport']==0){
	   	$yunfei="郵便局";
	   }else if($row['r_transport']==1){
	   	$yunfei="佐川";
	   }
	   if($row['r_whopay']==3)
	   {
	   	$yunshufei="送料なし";
	   }else{
		$yunshufei=$chengdan.$yunfei."費用"."&nbsp;&nbsp;￥".$row['r_transportpay'];
		}
	 	if($row['r_whopay']==1){
			$totail=$row['r_all']-$row['r_transportpay'];
		}else{
			$totail=$row['r_all'];
		}
                if($row['r_chkcod']=="on"){
                    $yunshufei=$yunshufei."<br>&nbsp;&nbsp;代引き手数料"."&nbsp;&nbsp;￥".$row['r_cod'];
                }
	   echo "<tr>
	   <td><center>ID号:".$row['id']."</td>
	   <td><center>&nbsp;".$row['r_dh']."</td>
	   <td><center>&nbsp;".$row['r_people']."</td>
	   <td><center>&nbsp;".$row['r_date']."</td>
	   <td><center>&nbsp;".$yunshufei."</td>
	   <td><center>&nbsp;￥".($totail)."</td>
	   <td><center>&nbsp;".$statusstring."</td>
	   <td><center><span onclick=showsubinfo(".$row['id'].") style='cursor:hand;'>詳細</span> ".$printstring.$editstring.$delstring."</td>
	   </tr>";
	   echo "<tr id='rk_subinfo".$row['id']."' style='display:none;'>
	   <td colspan='8'><br>
	   <table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\" align=\"center\">";
	   
	   $csql1=New Dedesql(false);
	   $csql1->SetQuery("select * from #@__sale where rdh='".$row['r_dh']."'");
	   $csql1->Execute();
	   $rowcount=$csql1->GetTotalRow();
           $rowsale=$csql1->getone();
	   echo "<tr class='row1_color_head'>
	   <td><center>商品コード</td>
	   <td><center>メーカー・名称</td>
	   <td><center>タイトル</td>
           <td><center>倉庫</td>
	   <td><center>販売単価</td>
	   <td><center>販売数</td>
	   <td><center>金額</td>
	   </tr>";
	   while($row=$csql1->GetArray()){
	   $nsql=New dedesql(false);
	   $query1="select * from #@__basic where cp_number='".$row['productid']."'";
	   $nsql->setquery($query1);
	   $nsql->execute();
	   $row1=$nsql->getone();
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
	   <td><center>".$row['productid']."</td>
	   <td><center>&nbsp;".$row1['cp_name']."</td>
	   <td width=\"50%\"><center>".$row1['cp_title']."</td>
           <td><center>&nbsp;".get_name($rowsale['salelab'],'lab')."</td>
	   <td><center>￥".$row['sale']."</td>
	   <td><center>".$row['number']."</td>
	   <td><center>￥".number_format($row['number']*$row['sale'])."</td>
	   </tr>";
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
 $query="update #@__reportsale set r_status=1 where id='$id'";
 else
 $query="update #@__reportsale set r_status=0 where id='$id'";
 $susql->executenonequery($query);
 $susql->close();
 showmsg('審査状態が変更しました。','sale.php?action=seek');
 }
 else{
?>
<script language="javascript">
function check(e){
var e = window.event ? window.event : e;
    if(e.keyCode == 13){
        thistm=document.forms[0].tm.value;
        
        inputMode=document.forms[0].inputMode;
        labid=document.forms[0].labid.value;
        
        if (inputMode[0].checked) {
            sel = "0";
        } else {
            sel = "1";
        }
        
    //window.parent.main.location.href='sale.php?thistm='+thistm;
        window.location.href='sale.php?thistm='+thistm+'&sel='+sel+'&labid='+labid;
    //document.forms[0].rk_number.focus();
	return false;
    }
}
function checkForm(){
document.forms[0].submit();
}
</script>
<body onload="form1.tm.focus()">
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
      <td><strong><strong>&nbsp;販売管理</strong>&nbsp;&nbsp;
              - <input type="button" value="販売表新規作成" onClick="location.href='sale.php'">
              - <input type="button" value="販売表検索" onClick="location.href='sale.php?action=seek'">
                  </td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
	   <form action="sale.php?action=save" method="post" name="form1">
    	<tr>
	     <td class="cellcolor">販売出庫銀行：</td>
		 <td>&nbsp;
		 <?php
		 $sql_bank=New dedesql(false);
		 $query_bank="select * from #@__bank";
		 $sql_bank->setquery($query_bank);
		 $sql_bank->execute();
		 $bank_n=$sql_bank->gettotalrow();
		 if($bank_n==0)echo "銀行がありません、追加してください。<a href='bank.php'>銀行追加</a>";
		 else{
		 echo "<select name='BANKID' id='BANKID'>";
		      while($row_bank=$sql_bank->getArray()){
			   if($row_bank['bank_default']=='1')
		         echo "<option value='".$row_bank['id']."' selected>".$row_bank['bank_name']."</option>";
				 else
				 echo "<option value='".$row_bank['id']."'>".$row_bank['bank_name']."</option>";
		      }
		     }
			 $sql_bank->close();
			 echo "</select>";
		 ?>&nbsp;銀行を選択してください		 </td>
	    </tr>
    <tr height="30">
    <td class="cellcolor">販売番号：</td>
    <td class="cellcolor">&nbsp;<input type="text" name="r_dh" value="<?php echo $cdh; ?>" readonly class="rtext" size="20">&nbsp;(販売担当：<input type="text" name="r_people" value="<?php echo GetCookie('VioomaUserID'); ?>" readonly class="rtext" size="10">時間：<input type="text" name="r_date" value="<?php echo GetDateTimeMk(time());?>"  readonly class="rtext">)</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">入庫モード：<br></td>
    <?php 
       if ($sel == "1") {
           $selected1 = "";
           $selected2 = "checked";
           $disabled = "disabled";
           $txtDisabled = "";
       } else {
           $selected1 = "checked";
           $selected2 = "";
           $disabled = "";
           $txtDisabled = "disabled";
       }
    ?>
    <td>&nbsp;<input type="radio" name="inputMode" <?php echo $selected1; ?> value="0" onclick="iptMode(this);">手入力</input>&nbsp;&nbsp;
        &nbsp;<input type="radio" name="inputMode" <?php echo $selected2; ?> value="1" onclick="iptMode(this);">スキャン</input>
    </td>
   </tr>
  <tr>
    <td class="cellcolor" width="30%">商品検索情報：<br></td>
    <td>&nbsp;<input type="text" name="tm" id="tm" value="" <?php echo $txtDisabled;?> onkeydown="check(event);">&nbsp;
            <input type="button" id="selBtn" name="selBtn" value="商品選択" <?php echo $disabled; ?> onclick="getinfo()">(バーコードリーダ対応)
	<input type="hidden" name="seek_number" value=""/>	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">出荷倉庫：<br></td>
    <td>
        &nbsp;<?php
                if (!is_null($labid)) {
                    getlab1($labid);
                } else {
                    getlab1();
                }
             ?>
    </td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">在庫位置：<br></td>
    <td>
        &nbsp;<input type="hidden" class="rtext1" readonly name="labnm" size="10">
            <input type="text" class="rtext1" name="positions" readonly size="40">
    </td>
  </tr> 
  <tr>
    <td class="cellcolor" width="30%">該当在庫数：<br></td>
    <td>
	&nbsp;<input type="text" class="rtext" name="number" size="5">
    </td>
  </tr> 
  <tr>
    <td class="cellcolor" width="30%">販売数：<br></td>
    <td>
	&nbsp;<input type="text" name="rk_number" style="ime-mode: disabled;" id="need" size="5">
            <input type="text" class="rtext1" name="showdw" readonly size="15">
	&nbsp;<!--<input type="hidden" name="labid" value="">-->
     </td>
  </tr> 
  <tr>
   <td class="cellcolor" width="30%">販売単価：</td>
   <td>&nbsp;<input type="text" name="sale" size="12" style="ime-mode: disabled;" id="need">&nbsp;円&nbsp;(販売単価を入力してください)
   <div style="height:27px;float:left;" id="sale_string"></div>   </td>
  </tr>
  <tr>
   <td class="cellcolor" width="30%">是否提交报告：</td>
   <td><input type="radio" checked="checked" name="is_report" value="0"/>否<input type="radio" name="is_report" value="1"/>是 </td>
  </tr>
  <tr>
  <td class="cellcolor" width="30%">輸送方法；</td>
   <td>&nbsp;
     <select name="transport" id="transport" style="width:80px;">
       <option value="0">ゆうパック</option>
       <option value="1">佐川急便</option>
       <option value="2">ヤマト運輸</option>
       <option value="3">西濃運輸</option>
     </select>     
     &nbsp;&nbsp;(輸送方法選択)   
        <input type="radio" name="whopay" id="whopay" value="0" checked="checked"/>お客様負担
        <input type="radio" name="whopay" id="whopay" value="1" />自社負担
        <input type="radio" name="whopay" id="whopay" value="3" />送料なし
        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkcod" id="chkcod" onclick="setcod(this)" />代金引換
     </td>
  </tr>
  <tr>
  <td class="cellcolor" width="30%"></td>
    <td>
        &nbsp;送料：<input type="text" name="transportpay" size="6" maxlength="6" value="500" style="ime-mode: disabled;" id="need" >&nbsp;円&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            代金引換手数料：<input type="text" name="cod" size="6" value="0" style="ime-mode: disabled;" readonly/>&nbsp;円
  　</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">顧客：<br></td>
    <td>&nbsp;<input type="text" name="member">&nbsp;<input type="button" value="顧客選択" onclick="getinfo1()">	</td>
  </tr>      
  <tr id="product_date" style="display:block;">
   <td colspan="2">
   &nbsp;商品情報：<input type="text" class="rtext" style="width:80%;" name="showinfo" readonly>   </td>
  </tr> 
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;<input type="button" value=" 該当販売情報に登録 " disabled onclick="putrkinfo()">&nbsp;&nbsp;<input type="button" value="該当販売情報を保存" onclick="checkForm()"></td>
  </tr>
  <tr>
   <td colspan="2">
   <iframe src="current_order_sale.php?pid=&did=<?php echo $cdh ?>&action=normal" width="100%" height="400" scrolling="auto" frameborder="0" marginheight="0" marginwidth="0" name="current_order" od="current_order"></iframe>   </td>
  </tr></form>
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
if($thistm!=''){
	echo $thistm;
	$checksql=new Dedesql(false);
	$checkquery="select #@__basic.*, #@__mainkc.* from #@__basic,#@__mainkc where #@__basic.cp_tm='$thistm' and #@__mainkc.p_id=#@__basic.cp_number and #@__mainkc.l_id='$labid'";
	$checksql->setquery($checkquery);
	$checksql->execute();
	$recordnumbers=$checksql->getTotalRow();
	if($recordnumbers==0){
		?>
		<script language="javascript">
		 document.forms[0].tm.focus();
		</script>
		<?php 
	} else{
		$row=$checksql->getone();
		?>
		<script lanugage="javascript">
		function showproduct(){
            document.forms[0].seek_number.value="<?php echo $row['cp_number']?>";
            document.forms[0].showinfo.value="商品名：<?php echo $row['cp_number']?>  仕様：<?php echo strRepacreBrToSpace($row['cp_gg'])?>";
            document.forms[0].showdw.value="<?php echo get_name($row['cp_dwname'],'dw')?>";
            document.forms[0].sale.value="<?php echo $row['cp_sale1']?>";
            document.forms[0].labid.value="<?php echo $row['l_id']?>";
            document.forms[0].number.value="<?php echo $row['number']?>";
            document.forms[0].labnm.value="<?php echo get_name($row['l_id'],'lab')?>";
            document.forms[0].positions.value="<?php echo $row['l_floor']?>"+"-"+"<?php echo $row['l_shelf']?>"+"-"
                +"<?php echo $row['l_zone']?>"+"-"+"<?php echo $row['l_horizontal']?>"+"-"+"<?php echo $row['l_vertical']?>";
            document.forms[0].rk_number.value= + 1;
            document.forms[0].rk_number.focus();
            putrkinfo();
		}
		showproduct();
		</script>
		<?php 
	}
}
}
copyright();
?>
</body>
</html>