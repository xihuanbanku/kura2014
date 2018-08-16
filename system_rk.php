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
<title>商品入庫管理</title>
<style type="text/css">
.rtext {
	background: transparent;
	border: 0px;
	color: red;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
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
window.open('rk_list.php?form=form1&field=seek_text&sort=3','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=980,height=600,top=100,left=120,scrollbars=yes');
}
function putrkinfo(){
    pid=document.forms[0].seek_number.value;
    did=document.forms[0].r_dh.value;
    lid=document.forms[0].labid.value;
    number=document.forms[0].rk_number.value;
    rk_price=document.forms[0].rk_price.value;
    bank=document.forms[0].bank.value;
    
    bz = document.forms[0].rk_bz.value;
    floor = document.forms[0].rk_floor.value;
    shelf = document.forms[0].rk_shelf.value;
    zone = document.forms[0].rk_zone.value;
    horizontal = document.forms[0].rk_horizontal.value;
    vertical = document.forms[0].rk_vertical.value;
    
    hd_floor = document.forms[0].hd_floor.value;
    hd_shelf = document.forms[0].hd_shelf.value;
    hd_zone = document.forms[0].hd_zone.value;
    hd_horizontal = document.forms[0].hd_horizontal.value;
    hd_vertical = document.forms[0].hd_vertical.value;
    hd_lab = document.forms[0].hd_lab.value;
    
    if(pid===''){
        alert('入庫商品を入力してください。');
        return false;
    }
    if(number==='' || (!isInteger(number)) || number<=0){
        alert('正しい入庫数を入力してください。');
        return false;
    }
    
    if (hd_lab === lid) {
        if (hd_floor !== floor || hd_shelf !==shelf || hd_zone !== zone 
            || hd_horizontal !== horizontal || hd_vertical !== vertical) {
            var flag = confirm ( "該当商品は既に以下の位置に存在しています。\n\（"
                                +hd_floor+"-"+hd_shelf+"-"+hd_zone+"-"+hd_horizontal+"-"+hd_vertical+
                                "）\n\これ以外の位置に入庫してもよろしいですか。");
            if (!flag) {
                return false;
            }
        }
    }
    
    
//    if(floor==''){
//    alert('位置-->階を入力してください。');
//    return false;
//    }
//    if(shelf==''){
//    alert('位置-->棚を入力してください。');
//    return false;
//    }
//    if(zone==''){
//    alert('位置-->ゾーンを入力してください。');
//    return false;
//    }
//    if(horizontal==''){
//    alert('位置-->横を入力してください。');
//    return false;
//    }
//    if(vertical==''){
//    alert('位置-->縦を入力してください。');
//    return false;
//    }
    url="current_order.php?pid=" + pid + "&did=" + did + "&lid=" + lid + "&num=" + number + "&rk_price=" + rk_price + "&bank=" + bank
        + "&bz=" + bz + "&floor=" + floor + "&shelf=" + shelf + "&zone=" + zone + "&horizontal=" + horizontal + "&vertical=" + vertical;

    var obj=window.frames["current_order"];
     obj.window.location=url;
}

function showsubinfo(tbnum){
whichEl = eval("rk_subinfo" + tbnum);
if (whichEl.style.display == "none"){eval("rk_subinfo" + tbnum + ".style.display=\"\";");}
else{eval("rk_subinfo" + tbnum + ".style.display=\"none\";");}
}

function iptMode(obj) {
    if (obj.value === "0") {
        document.getElementById("selBtn").disabled = "";
        document.getElementById("tm").disabled = "disabled";
    } else if (obj.value === "1") {
        document.getElementById("selBtn").disabled = "disabled";
        document.getElementById("tm").disabled = "";
    }
    document.getElementById("sel").value = obj.value;
    document.forms[0].tm.focus();
}



</script>
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
        window.location.href='system_rk.php?thistm='+thistm+'&sel='+sel+'&labid='+labid;
//        url='current_order.php?form=form1&action=scan&thistm='+thistm;
//        var obj=window.frames["current_order"];
//        obj.window.location=url;
	return false;
    }
}
function checkForm(){
document.forms[0].submit();
}
function change() {
    document.forms[0].tm.focus();
}
</script>
</head>
<?php

$rs=New Dedesql(falsh);
$query="select * from #@__reportrk where r_people='".GetCookie('VioomaUserID')."' and DATE_FORMAT(r_date,'%Y-%m-%d')='".GetDateMk(date(time()))."'";
$rs->SetQuery($query);
$rs->Execute();
$rowcount=$rs->GetTotalRow();
  $cdh="Vin".str_replace('-','',GetDateMk(date(time())))."-Id".substr(GetCookie('VioomaUserID'), -3)."-".($rowcount+1);
  //$cdh=GetDateMk(date(time()));
 $rs->close();
if ($action == 'save') {
    if ($bank == '') {
        ShowMsg("入庫銀行を選択してください。", "-1");
    }
    $bsql = new Dedesql(false);
    $query = "select * from #@__kc where rdh='$r_dh' and tantousyaid='" . GetCookie('VioomaUserID') . "'";
    $bsql->SetQuery($query);
    $bsql->Execute();
    $rowcount = $bsql->GetTotalRow();
    if ($rowcount == 0) {
        ShowMsg('引数エラー、または商品が追加されてません。');
        exit();
    } else {
        $money = 0;
        while ($row = $bsql->getArray()) {
            $money += $row['number'] * $row['rk_price'];
            $csql = new dedesql(false);
            $csql->setquery("select * from #@__mainkc where p_id='" . $row['productid'] . "' and l_id='" . $row['labid'] . "' and l_floor='" . $row['labfloor'] . "' and l_shelf='" . $row['labshelf'] . "' " . "and l_zone='" . $row['labzone'] . "' and l_horizontal='" . $row['labhorizontal'] . "' and l_vertical='" . $row['labvertical'] . "'");
            $csql->execute();
            $totalrec = $csql->gettotalrow();
            if ($totalrec == 0)
                $rs = $csql->executenonequery("insert into #@__mainkc(p_id,l_id,d_id,number,l_floor,l_shelf,l_zone,l_horizontal,l_vertical,dtime) " . "values('" . $row['productid'] . "','" . $row['labid'] . "','0','" . $row['number'] . "','" . $row['labfloor'] . "'," . "'" . $row['labshelf'] . "','" . $row['labzone'] . "','" . $row['labhorizontal'] . "','" . $row['labvertical'] . "','" . GetDateTimeMk(time()) . "')");
            else
                $rs = $csql->executenonequery("update #@__mainkc set number=number+" . $row['number'] . ",dtime='" . GetDateTimeMk(time()) . "' where p_id='" . $row['productid'] . "' " . "and l_id='" . $row['labid'] . "' and l_floor='" . $row['labfloor'] . "' and l_shelf='" . $row['labshelf'] . "' " . "and l_zone='" . $row['labzone'] . "' and l_horizontal='" . $row['labhorizontal'] . "' and l_vertical='" . $row['labvertical'] . "'");
        }
        if (! $rs) {
            showmsg("エラー" . $csql->getError(), "-1");
            exit();
        }
        $csql->close();
        $loginip = getip();
        $logindate = getdatetimemk(time());
        $username = GetCookie('VioomaUserID');
        WriteNote('入庫情報' . $r_dh . '保存しました。', $logindate, $loginip, $username);
        $newsql = new dedesql(false);
        $newsql->executenonequery("insert into #@__reportrk(r_dh,r_people,r_date,r_status) values('$r_dh','$r_people','$r_date','0')");
        
        $newsql->executenonequery("insert into #@__accounts(atype,amoney,abank,dtime,apeople,atext) values('支出','$money','$bank','$r_date','$r_people','支出金額、入庫番号：" . $r_dh . "')");
        
        $newsql->executenonequery("update #@__bank set bank_money=bank_money-" . $money . " where id='$bank'");
        $newsql->close();
        ShowMsg('入庫しました。', 'system_rk.php?sel=' . $sel . '&labid=' . $labid);
        $bsql->close();
exit();
    }
}
else if($action=='seek'){
?>
<body>
	<table width="100%" border="0" id="table_style_all" cellpadding="0"
		cellspacing="0">
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
						<td><strong>&nbsp;商品入庫表管理</strong>&nbsp;&nbsp;- <input
							type="button" value="入庫表新規作成"
							onClick="location.href='system_rk.php'"></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
<?php
$orderstring=" order by r_date desc";
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
	   if(GetCookie('rank')==1 or GetCookie('rank')==100 or GetCookie('rank')==105) {
            $query="select * from #@__reportrk".$orderstring;
           } else {
            $query="select * from #@__reportrk where r_people='".GetCookie('VioomaUserID')."'".$orderstring;
           }
       $csql=New Dedesql(false);
	   $dlist = new DataList();
       $dlist->pageSize = $cfg_record;
       $dlist->SetParameter("action",$action);
       $dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
	   <td>ID</td>
	   <td>入庫番号</td>
	   <td>担当者</td>
	   <td>入庫時間</td>
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
	   $printstring=" | <a href=report_rk.php?action=save&type=other&sday=".$row['r_dh'].">印刷</a>";
	   $workstring="";
	   }
	   else{
	    if(Getcookie('rank')=='1' or Getcookie('rank')=='100' or Getcookie('rank')=='105')
	   $statusstring="<a href='?action=sure&t=yes&id=".$row['id']."'><img src='images/no.png' alt='審査' border='0'></a>";
	    else
	   $statusstring="<img src='images/no.png' alt='審査' border='0'>";
	   $printstring="";
	   $workstring="<a href=''>編集</a> | <a href=''>削除</a>";
	   }
	   echo "<tr>
	   <td><center>ID号:".$row['id']."</td>
	   <td><center>&nbsp;".$row['r_dh']."</td>
	   <td><center>&nbsp;".$row['r_people']."</td>
	   <td><center>&nbsp;".$row['r_date']."</td>
	   <td><center>&nbsp;".$statusstring."</td>
	   <td><center><span onclick=showsubinfo(".$row['id'].") style='cursor:hand;'>展開</span>".$printstring."</td>
	   </tr>";
	   echo "<tr id='rk_subinfo".$row['id']."' style='display:none;'><td colspan='6'><br><table width=\"98%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\" align=\"center\">";
	   
	   $csql1=New Dedesql(false);
	   $csql1->SetQuery("select * from #@__kc where rdh='".$row['r_dh']."'");
	   $csql1->Execute();
	   $rowcount=$csql1->GetTotalRow();
	   echo "<tr class='row1_color_head'>
	   <td><center>商品コード</td>
	   <td><center>メーカー・商品名</td>
	   <td><center>タイトル</td>
           <td><center>倉庫</td>
           <td><center>入庫位置</td>
	   <td><center>入庫数</td>
	   </tr>";
	   while($row=$csql1->GetArray()){
	   $nsql=New dedesql(false);
	   $query1="select * from #@__basic where cp_number='".$row['productid']."'";
	   $nsql->setquery($query1);
	   $nsql->execute();
	   $row1=$nsql->getone();
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
	   <td><center>".$row['productid']."</td>
	   <td>&nbsp;".$row1['cp_name']."</td>
	   <td width=\"50%\">".$row1['cp_title']."</td>
           <td><center>&nbsp;".get_name($row['labid'],'lab')."</td>
           <td align=\"center\">".$row['labfloor']."-".$row['labshelf']."-".$row['labzone']."-".$row['labhorizontal']."-".$row['labvertical']."</td>
	   <td><center>".$row['number']."</td>
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
 $query="update #@__reportrk set r_status=1 where id='$id'";
 else
 $query="update #@__reportrk set r_status=0 where id='$id'";
 $susql->executenonequery($query);
 $susql->close();
 showmsg('審査状態を変更しました。','system_rk.php?action=seek');
 }
 else{
?>
<body onload="form1.tm.focus()">
								<table width="100%" border="0" id="table_style_all"
									cellpadding="0" cellspacing="0">
									<tr>
										<td id="table_style" class="l_t">&nbsp;</td>
										<td>&nbsp;</td>
										<td id="table_style" class="r_t">&nbsp;</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>
											<table width="100%" border="0" cellpadding="0"
												cellspacing="2">
												<form action="system_rk.php?action=save" method="post"
													name="form1">
													<tr>
														<td><strong><strong>&nbsp;商品入庫管理</strong>(※:オレンジ色が必須項目)&nbsp;&nbsp;
																- <input type="button" value="入庫表新規作成"
																onClick="location.href='system_rk.php'"> - <input
																	type="button" value="入庫表検索"
																	onClick="location.href='system_rk.php?action=seek'"></td>
													</tr>
													<tr>
														<td bgcolor="#FFFFFF">
															<table width="100%" border="0" cellspacing="0"
																cellpadding="0" id="table_border">
																<tr>
																	<td class="cellcolor">入庫銀行：</td>
																	<td>&nbsp;
		 <?php
		 $sql_bank=New dedesql(false);
		 $query_bank="select * from #@__bank";
		 $sql_bank->setquery($query_bank);
		 $sql_bank->execute();
		 $bank_n=$sql_bank->gettotalrow();
		 if($bank_n==0)echo "銀行がありません，追加してください。<a href='bank.php'>追加</a>";
		 else{
		 echo "<select name='bank'>";
		      while($row_bank=$sql_bank->getArray()){
			   if($row_bank['bank_default']=='1')
		         echo "<option value='".$row_bank['id']."' selected>".$row_bank['bank_name']."</option>";
				 else
				 echo "<option value='".$row_bank['id']."'>".$row_bank['bank_name']."</option>";
		      }
		     }
			 $sql_bank->close();
			 echo "</select>";
		 ?>&nbsp;正しい銀行を選択してください。
		 </td>
																</tr>
																<tr height="30">
																	<td class="cellcolor">入庫番号：</td>
																	<td class="cellcolor">&nbsp;<input type="text"
																		name="r_dh" value="<?php echo $cdh; ?>" readonly
																		class="rtext" size="20"> &nbsp;(入庫担当：<input
																			type="text" name="r_people"
																			value="<?php echo Getcookie('VioomaUserID'); ?>"
																			readonly class="rtext" size="10"> 時間：<input
																				type="text" name="r_date"
																				value="<?php echo GetDateTimeMk(time());?>" readonly
																				class="rtext">)</td>
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
         <td>&nbsp;<input type="radio" name="inputMode"
																		<?php echo $selected1; ?> value="0"
																		onclick="iptMode(this);">手入力</input>&nbsp;&nbsp;
																		&nbsp;<input type="radio" name="inputMode"
																		<?php echo $selected2; ?> value="1"
																		onclick="iptMode(this);">スキャン</input> &nbsp;<input
																		type="hidden" name="sel" id="sel"
																		value="<?php echo $sel;?>"></input>
																	</td>
																</tr>
																<tr>
																	<td class="cellcolor" width="30%">商品検索情報：<br></td>
																	<td>&nbsp;<input type="text" name="tm" id="tm" value=""
																		<?php echo $txtDisabled;?> onkeydown="check(event);">&nbsp;
																			<input type="button" id="selBtn" name="selBtn"
																			value="選択" <?php echo $disabled; ?>
																			onclick="getinfo()"> <input type="hidden"
																				name="seek_text" value="" readonly class="rtext"
																				size="15" Tabindex="1">(バーコードリーダー対応) </td>
																</tr>
																<tr>
																	<td class="cellcolor" width="30%">商品コード（バーコード）：<br></td>
																	<td>&nbsp;<input type="text" class="rtext" size="10"
																		readonly name="seek_number" value="" /> &nbsp;<input
																		type="text" class="rtext" size="20" readonly
																		name="tm_number" value="" />
																	</td>
																	<tr>
																		<tr>
																			<td class="cellcolor" width="30%">倉庫：<br></td>
																			<td><input type="hidden" name="hd_lab">
             &nbsp;<?php
                if (!is_null($labid)) {
                    getlab1($labid);
                } else {
                    getlab1();
                }
             ?>
    	 </td>
																			<tr>
																				<td class="cellcolor" width="30%">位置：<br></td>
																				<td>&nbsp;階: <input type="hidden" name="hd_floor"/> 
																				<input name="rk_floor" size="5"></input>
																				棚: <input type="hidden" name="hd_shelf"/>
																				 <input name="rk_shelf" size="5"></input>
																				  ゾーン: <input type="hidden" name="hd_zone"/>
																				   <input name="rk_zone" size="5"></input>
																				  横: <input type="hidden" name="hd_horizontal"/> 
																				  <input name="rk_horizontal" size="5"></input>
																				  縦: <input type="hidden" name="hd_vertical"/>
																				   <input name="rk_vertical" size="5"></input>
																				   </td>
																			</tr>
																		</tr>
																		<tr>
																			<td class="cellcolor" width="30%">入庫数：<br></td>
																			<td>&nbsp;<input type="text" name="rk_number"
																				id="need" style="ime-mode: disabled;" size="5"/>
																					&nbsp;<input type="text" class="rtext"
																					name="showdw" readonly size="5"/></td>
																		</tr>
																		<tr>
																			<td class="cellcolor" width="30%">仕入単価：</td>
																			<td>&nbsp;<input type="text" name="rk_price"
																				style="ime-mode: disabled;" size="5" disabled
																				value="0" />&nbsp;円
																		
																		</tr>
																		<tr>
																			<td class="cellcolor">備考：</td>
																			<td>&nbsp;<textarea rows="5" cols="50" name="rk_bz"></textarea></td>
																		</tr>
																		<tr id="product_date">
																			<td class="cellcolor" width="30%">商品情報：</td>
																			<td><input type="text" class="rtext"
																				style="width: 100%;" name="showinfo" readonly></td>
																		</tr>
																		<tr>
																			<td class="cellcolor">&nbsp;</td>
																			<td>&nbsp;<input type="button" value=" 該当入庫表に登録 "
																				disabled onclick="putrkinfo()">&nbsp;&nbsp;<input
																					type="button" value="該当入庫情報を保存"
																					onclick="checkForm()"></td>
																		</tr>
																		</form>
																		<tr>
																			<td colspan="2"><iframe
																					src="current_order.php?pid=&did=<?php echo $cdh ?>&action=normal"
																					width="100%" height="400" scrolling="auto"
																					frameborder="0" marginheight="0" marginwidth="0"
																					name="current_order" od="current_order"></iframe></td>
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
if($thistm!=''){
	echo $thistm;
	$checksql=new Dedesql(false);
	$checkquery="select basic.*, main.* from #@__basic basic left join #@__mainkc main "
                . "on main.p_id = basic.cp_number and main.l_id = '$labid' where basic.cp_tm='$thistm'";
	$checksql->setquery($checkquery);
	$checksql->execute();
	$recordnumbers=$checksql->getTotalRow();
	if($recordnumbers == 0){
		?>
		<script language="javascript">
		 document.forms[0].tm.focus();
		</script>
		<?php 
	}
	else{
		$row=$checksql->getone();
		?>
		<script lanugage="javascript">
                    function showproduct(){
                    document.forms[0].seek_text.value="<?php echo $row['cp_name']?>";
                    document.forms[0].seek_number.value="<?php echo $row['cp_number']?>";
                    document.forms[0].tm_number.value="（<?php echo $row['cp_tm']?>）";
                    document.forms[0].showinfo.value="商品名：<?php echo $row['cp_name']?>  仕様：<?php echo strRepacreBrToSpace($row['cp_gg'])?>";
                    document.forms[0].showdw.value="<?php echo get_name($row['cp_dwname'],'dw')?>";
                    document.forms[0].rk_price.value="<?php echo $row['cp_jj']?>";

                    document.forms[0].rk_floor.value = "<?php echo $row['l_floor'] ?>";
                    document.forms[0].rk_shelf.value = "<?php echo $row['l_shelf'] ?>";
                    document.forms[0].rk_zone.value = "<?php echo $row['l_zone'] ?>";
                    document.forms[0].rk_horizontal.value = "<?php echo $row['l_horizontal'] ?>";
                    document.forms[0].rk_vertical.value = "<?php echo $row['l_vertical'] ?>";
                        
                    document.forms[0].rk_number.value="1";
                    
                    putrkinfo();
                    
                    document.forms[0].rk_number.focus();
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