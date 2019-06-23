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
<link href="style/lightbox.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery-1.10.2.min.js" type="text/javascript" ></script>
<script language="javascript" src="js/lightbox-2.6.min.js" type="text/javascript" ></script>
<script language="javascript" src="include/calendar.js"></script>
<script language="javascript" src="include/py.js"></script>
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>商品基本情報記録</title>
<style type="text/css">
.rtext {background:transparent;border:0px;color:blue;font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
.basic_editA{display: none;}
.basic_deleteA{display: none;}
.basic_copyA{display: none;}
</style>
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

function out_excel(shop){
/*cp=document.forms[0].cp_categories.value;
cp_down=document.forms[0].cp_categories_down.value;*/
s=document.forms[0].sort.value;
st=document.forms[0].stext.value;
sdate = document.getElementById("sdate").value;
edate = document.getElementById("edate").value;
if (sdate !== '' && edate !== '') {
    if (Date.parse(sdate) > Date.parse(edate)) {
        alert('正しい日付範囲を選択してください。');
        return false;
    }
}
var url = "service/KcService.class.php?"+$("input").serialize();
window.open(url+'&labid=&compare=&num=&shop='+shop+'&flag=out_excel','','');
}
function submitChk(id) {
        var flag = confirm ( "削除したら復元できないので、本当に削除しますか。");
        if (flag) {
            location.href = "system_basic_del.php?id=" + id;
        }
        return flag;
}
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
function getcode(){
    window.open('code_list.php?form=form1&field=seek_text','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=1080,height=600,top=100,left=120,scrollbars=yes');
}
</script>
<script type="text/vbscript"> 
function vbChr(c) 
vbChr = chr(c) 
end function 

function vbAsc(n) 
vbAsc = asc(n) 
end function 

</script> 
<script type="text/javascript">
$(function(){
	$.ajax({
		type: "post",
		url: "service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"11", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
//         		alert(entry.url+"|"+entry.loc);
        		if(entry.loc > 0) {
    				$("#" + entry.url).show();
        		} else {
    				$("#" + entry.url).remove();
        		}
        		if((entry.url == "basic_editA" ||entry.url == "basic_deleteA" ||entry.url == "basic_copyA" ) && entry.loc > 0) {
        		    $("."+entry.url).each(function(i, item){
            		    $(item).show();
        		    });
        		} else {
        			$("."+entry.url).each(function(i, item){
            		    $(item).remove();
        		    });
        		}
    		});
		}
	});
});
</script>
</head>
<?php
if ($action=='save'){
// if($cp_number==''){ echo "<script language='javascript'>alert('商品コードを入力してください。');history.go(-1)</script>";exit();}
// if($cp_name==''){ echo "<script language='javascript'>alert('商品名を入力してください。');history.go(-1)</script>";exit();}
// if($cp_title==''){ echo "<script language='javascript'>alert('商品タイトルを入力してください。');history.go(-1)</script>";exit();}
// if($cp_detail==''){ echo "<script language='javascript'>alert('商品詳細情報を入力してください。');history.go(-1)</script>";exit();}
// if($cp_gg=='') {echo "<script language='javascript'>alert('商品仕様を入力してください。');history.go(-1)</script>";exit();}
// if($cp_categories==''){ echo "<script language='javascript'>alert('商品大分類を入力選択ください。');history.go(-1)</script>";exit();}
// if($cp_categories_down==''){ echo "<script language='javascript'>alert('商品小分類を選択してください。');history.go(-1)</script>";exit();}
// if($cp_dwname==''){ echo "<script language='javascript'>alert('商品単位を入力選択ください。');history.go(-1)</script>";exit();}
// if($cp_sale1==''){ echo "<script language='javascript'>alert('販売単価を入力してください。');history.go(-1)</script>";exit();}
// if(!(is_numeric($cp_jj) || is_numeric($cp_sale1) )){ echo "<script language='javascript'>alert('単価は数字限り。');history.go(-1)</script>";exit();}
// if($cp_jj!='' and $cp_jj>$cp_sale1){ echo "<script language='javascript'>alert('販売単価＜仕入単価のことが許可されません。');history.go(-1)</script>";exit();}
$bsql=New Dedesql(false);
$cp_number = delSpace($cp_number);
$query="select * from #@__basic where cp_number='$cp_number'";
$bsql->SetQuery($query);
$bsql->Execute();
$rowcount=$bsql->GetTotalRow();
if ($rowcount>=1) {
    ShowMsg('該当商品番号が既に存在しています。もう一度確認してください。','system_basic_cp.php?cp_number='.$cp_number.'&cp_tm='.$cp_tm.
            '&cp_name='.$cp_name.'&cp_title='.$cp_title.'&titgoods='.$titgoods.'&cp_detail_1='.$cp_detail_1.'&titcomputer='.$titcomputer.
            '&cp_detail_2='.$cp_detail_2.'&titgoodcode='.$titgoodcode.'&cp_detail_3='.$cp_detail_3.'&cp_gg='.$cp_gg.'&cp_categories='.$cp_categories.
            '&cp_categories_down='.$cp_categories_down.'&cp_sale1='.$cp_sale1.'&cp_helpword='.$cp_helpword.'&cp_helpword_1='.$cp_helpword_1.
            '&cp_helpword_2='.$cp_helpword_2.'&cp_helpword_3='.$cp_helpword_3.'&cp_helpword_4='.$cp_helpword_4.'&cp_helpword_5='.$cp_helpword_5.'&cp_helpword_6='.$cp_helpword_6.
            '&cp_helpword_7='.$cp_helpword_7.'&cp_helpword_8='.$cp_helpword_8.'&cp_helpword_9='.$cp_helpword_9.'&cp_url='.$cp_url.'&cp_url_1='.$cp_url_1.'&cp_url_2='.$cp_url_2.
            '&cp_url_3='.$cp_url_3.'&cp_url_4='.$cp_url_4.'&cp_browse_node_1='.$cp_browse_node_1.'&cp_browse_node_2='.$cp_browse_node_2.'&cp_bz='.$cp_bz);
 exit();
}
else{
if($cp_saleall=='')$cp_saleall=$cp_sale;
if($cp_sale1=='')$cp_sale1=$cp_sale;
if($cp_sdate=='')$cp_sdate='0000-00-00';
if($cp_edate=='')$cp_edate='0000-00-00';

$query="select * from #@__basic left join #@__barcode on productid = cp_number where cp_number='$cp_number'";
$bsql->SetQuery($query);
$bsql->Execute();
$row=$bsql->GetOne();
//if (is_null($row['barcode'])) {
//    ShowMsg('該当商品のバーコードが存在しません。バーコード管理画面へ確認してください。','-1');
//    exit();
//}
$cp_title = str_replace(array("\r\n","\n","\r"), ' ', $cp_title);
$cp_detail = $titgoods."\n".$cp_detail_1."\n".$titcomputer."\n".$cp_detail_2."\n".$titgoodcode.$cp_detail_3;

$addquery="insert into #@__basic(cp_number,cp_tm,cp_name,cp_title,cp_detail,cp_gg,cp_categories,cp_categories_down,cp_dwname,"
        . "cp_style,cp_jj,cp_sale,cp_saleall,cp_sale1,cp_sdate,cp_edate,cp_gys,cp_helpword,cp_helpword_1,cp_helpword_2,cp_helpword_3,"
        . "cp_helpword_4,cp_helpword_5,cp_helpword_6,cp_helpword_7,cp_helpword_8,cp_helpword_9,"
        . "cp_bz,cp_url,cp_url_1,cp_url_2,cp_url_3,cp_url_4,cp_browse_node_1,cp_browse_node_2,cp_dtime) values("
        . "'$cp_number','".$cp_tm."','$cp_name','$cp_title','$cp_detail','$cp_gg','$cp_categories','$cp_categories_down','$cp_dwname',"
        . "'$cp_style','$cp_jj','$cp_sale','$cp_saleall','$cp_sale1','$cp_sdate','$cp_edate','$cp_gys','$cp_helpword','$cp_helpword_1',"
        . "'$cp_helpword_2','$cp_helpword_3','$cp_helpword_4','$cp_helpword_5','$cp_helpword_6','$cp_helpword_7','$cp_helpword_8','$cp_helpword_9',"
	    . "'$cp_bz','$cp_url','$cp_url_1','$cp_url_2','$cp_url_3','$cp_url_4','$cp_browse_node_1',"
        . "'$cp_browse_node_2','".GetDateTimeMk(time())."')";
$rs=$bsql->ExecuteNoneQuery($addquery);
if($rs)
ShowMsg('商品基本情報を１件登録しました。','system_basic_cp.php');
else
ShowMsg('エラー：'.$bsql->getError(),'-1');
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 WriteNote('商品基本情報'.$cp_number.' を追加しました。',$logindate,$loginip,$username);
$bsql->close();
exit();
    }
}
else if($action=='seek'){
?>
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
	<form action="system_basic_cp.php?action=seek&seek=yes" method="post" name="form1">
     <tr>
         <td><strong>&nbsp;商品基本情報一覧</strong>&nbsp;&nbsp;
             - <input type="button" value="商品新規登録" onClick="location.href='system_simple_rk.php'"> 
         </td>
     </tr>
     <tr height="27">
	  <td align="right" bgcolor="#FFFFFF">
		 分類：
		 <?php
		 if ($action=='seek')
		 getcategories1($cp_categories,$cp_categories_down);
		 else
                 getcategories1(0,'');
	     ?>
		 <select name="sort">
		 <option value="1">商品コード検索</option>
		 <option value="2">ﾊﾞｰｺｰﾄﾞ検索</option>
		 <option value="3" selected>商品詳細検索</option>
		 <option value="4">ｷｰﾜｰﾄﾞ検索</option>
		 </select>
		 <input type="text" name="stext" size="15" VALUE="<?PHP ECHO $stext ?>">&nbsp;<input type="submit" value="検索">
		 &nbsp;&nbsp;
	  </td>
	 </tr></form>
	 <tr>
      <td bgcolor="#FFFFFF">
<?php
$stext = delSpace($stext);
echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
if($seek=='yes'){
    switch($sort){
     case "1":
         if($cp_categories !='' and $cp_categories_down != '') {
             $query="select * from #@__basic where cp_number LIKE '%".$stext."%' and #@__basic.cp_categories='$cp_categories' and #@__basic.cp_categories_down='$cp_categories_down'";
         } else if ($cp_categories !='' and $cp_categories_down == '') {
             $query="select * from #@__basic where cp_number LIKE '%".$stext."%' and #@__basic.cp_categories='$cp_categories'";
         } else {
            $query="select * from #@__basic where cp_number LIKE '%".$stext."%'";
         }
     
     break;
     case "2":
         if($cp_categories !='' and $cp_categories_down != '') {
             $query="select * from #@__basic where cp_tm = '$stext' and #@__basic.cp_categories='$cp_categories' and #@__basic.cp_categories_down='$cp_categories_down'";
         } else if ($cp_categories !='' and $cp_categories_down == '') {
             $query="select * from #@__basic where cp_tm = '$stext' and #@__basic.cp_categories='$cp_categories'";
         } else {
             $query="select * from #@__basic where cp_tm = '$stext'";
         }
     break;
     case "3":
         if($cp_categories !='' and $cp_categories_down != '') {
             $query="select * from #@__basic where (#@__basic.cp_detail LIKE '%".$stext."%' or #@__basic.cp_title LIKE '%".$stext."%') and #@__basic.cp_categories='$cp_categories' and #@__basic.cp_categories_down='$cp_categories_down'";
         } else if ($cp_categories !='' and $cp_categories_down == '') {
             $query="select * from #@__basic where (#@__basic.cp_detail LIKE '%".$stext."%' or #@__basic.cp_title LIKE '%".$stext."%') and #@__basic.cp_categories='$cp_categories'";
         } else {
             $query="select * from #@__basic where #@__basic.cp_detail LIKE '%".$stext."%' or #@__basic.cp_title LIKE '%".$stext."%'";
         }
     break;
     case "4":
         if($cp_categories !='' and $cp_categories_down != '') {
             $query="select * from #@__basic where cp_helpword LIKE '%".$stext."%' and #@__basic.cp_categories='$cp_categories' and #@__basic.cp_categories_down='$cp_categories_down'";
         } else if ($cp_categories !='' and $cp_categories_down == '') {
             $query="select * from #@__basic where cp_helpword LIKE '%".$stext."%' and #@__basic.cp_categories='$cp_categories' order by cp_number cp_dtime desc, desc";
         } else {
             $query="select * from #@__basic where cp_helpword LIKE '%".$stext."%'";
         }
     
     break;
    }
}
else
$query="select * from #@__basic";

$csql=New Dedesql(false);
$dlist = new DataList();
$dlist->pageSize = $cfg_record;
$dlist->SetParameter("action",$action);
$dlist->SetParameter("cp_categories",$cp_categories);
$dlist->SetParameter("cp_categories_down",$cp_categories_down);
$dlist->SetParameter("seek",$seek);
$dlist->SetParameter("sort",$sort);
$dlist->SetParameter("stext",$stext);

if (is_null($sorting)) {
    $query .= " order by cp_number desc ";
} else {
    $query .= " order by cp_number ".$sorting." ";
}

$dlist->SetSource($query);
	   echo "<tr class='row_color_head'>
           <td>操作</td>";
	   $href = "system_basic_cp.php?&sorting=";
           $arrow = "▼";
           if (is_null($sorting)) {
               $href .= "asc";
           } else {
               if ($sorting == "asc") {
                   $href .= "desc";
                   $arrow = "▲";
               } else {
                   $href .= "asc";
               }
           }
           if (!is_null($action)) {
               $href .= "&action=seek";
           }
           if (!is_null($seek)) {
               $href .= "&seek=yes";
           }
           if (!is_null($cp_categories)) {
               $href .= "&cp_categories=".$cp_categories;
           }
           if (!is_null($cp_categories_down)) {
               $href .= "&cp_categories_down=".$cp_categories_down;
           }
           if (!is_null($sort)) {
               $href .= "&sort=".$sort;
           }
           if (!is_null($stext)) {
               $href .= "&stext=".$stext;
           }
           echo "<td><a  href='".$href."'><b>商品コード&nbsp;".$arrow."</b></a></td>
	   <td>メーカ・商品名</td>
	   <td>タイトル</td>
	   <td>販売価格</td>
           <td>商品登録日</td>
	   </tr>";
	   $mylist = $dlist->GetDataList();
       while($row = $mylist->GetArray('dm')){
	   if($row['cp_style']=='1')$pstyle='';
	   else
	   $pstyle="<font color=red>非販売</font>";
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
           <td><input type='checkbox' name='strChk[]' value='".$row['cp_number']."'>"
                   . "<a class=\"basic_editA\" href=system_basic_edit.php?id=".$row['cp_number']."&cp_categories=".$cp_categories.
                "&cp_categories_down=".$cp_categories_down."&sort=".$sort."&stext=".$stext."&sorting=".$sorting."&action=".$action.">修正</a>|
                <a class=\"basic_deleteA\" href='#' onClick=\"submitChk('".$row['cp_number']."')\">削除</a>|
                <a class=\"basic_copyA\" href=system_basic_copy.php?id=".$row['cp_number']."&cp_categories=".$cp_categories.
                "&cp_categories_down=".$cp_categories_down."&sort=".$sort."&stext=".$stext."&sorting=".$sorting."&action=".$action.">COPY</a></td>
	   <td>".$row['cp_number']."</td>
	   <td width=\"10%\">&nbsp;".$row['cp_name']."&nbsp;&nbsp;&nbsp;&nbsp;</td>
	   <td width=\"55%\">&nbsp;".$row['cp_title']."&nbsp;&nbsp;</td>
           <td align=\"right\"><font color=\"blue\">&yen;&nbsp;".number_format($row['cp_sale1'])."</font>&nbsp;&nbsp;</td>
	   <td><center>&nbsp;".ChangeDateTime($row['cp_dtime'])."</td>
	   </tr>";
	   }
	   echo "<tr><td colspan='8'>&nbsp;".$dlist->GetPageList($cfg_record)."</td></tr></table>";
	  
	   $csql->close();
   echo " </td></tr></table>
 </td></tr>
 <tr>
    <td id=\"table_style\" class=\"l_b\">&nbsp;</td>
    <td align=\"right\">商品登録日：
    <input type=\"text\" name=\"sdate\" id=\"sdate\" size=\"15\" VALUE=\"\" class=\"Wdate\" onClick=\"WdatePicker()\"> &ndash; 
    <input type=\"text\" name=\"edate\" id=\"edate\" size=\"15\" VALUE=\"\" class=\"Wdate\" onClick=\"WdatePicker()\">
    <input type=\"button\" onclick=\"out_excel('rakuten')\" value=\"楽天データ出力\" />
    <input type=\"button\" onclick=\"out_excel('amazon')\" value=\"Amazonデータ出力\" />
    </td>
    <td id=\"table_style\" class=\"r_b\">&nbsp;</td>
  </tr>
</table>";
 }
 else{
?>
<script language="javascript">
function check(e){
var e = window.event ? window.event : e;
    if(e.keyCode == 13){
    document.forms[0].cp_name.focus();
	return false;
    }
}
function checkForm(){
    if (checkInput()) {
        document.forms[0].submit();
    }
}

function checkInput() {
    cp_number = document.forms[0].cp_number.value;
    cp_name = document.forms[0].cp_name.value;
    cp_title = document.forms[0].cp_title.value;
    //cp_detail = document.forms[0].cp_detail.value;
    cp_gg = document.forms[0].cp_gg.value;
    cp_categories = document.forms[0].cp_categories.value;
    cp_categories_down = document.forms[0].cp_categories_down.value;
    cp_dwname = document.forms[0].cp_dwname.value;
    cp_sale1 = document.forms[0].cp_sale1.value;
    cp_jj = document.forms[0].cp_jj.value;
    cp_sale = document.forms[0].cp_sale.value;
    cp_saleall = document.forms[0].cp_saleall.value;
    
    if (cp_number === '') {
        alert('商品コードを入力してください。');
        return false;
    }
    if (cp_name === '') {
        alert('商品名を入力してください。');
        return false;
    }
    if (cp_title === '') {
        alert('商品タイトルを入力してください。');
        return false;
    }
//    if (cp_detail === '') {
//        alert('商品詳細情報を入力してください。');
//        return false;
//    }
//    if (cp_gg === '') {
//        alert('商品仕様を入力してください。');
//        return false;
//    }
    if (cp_categories === '') {
        alert('商品大分類を入力選択ください。');
        return false;
    }
    if (cp_categories_down === '') {
        alert('商品小分類を選択してください。');
        return false;
    }
    if (cp_dwname === '') {
        alert('商品単位を選択してください。');
        return false;
    }
    if (cp_sale1 === '') {
        alert('販売単価を入力してください。');
        return false;
    }
    if (cp_jj !== '' && cp_jj.match(/[^0-9]+/)) {
        alert("仕入単価に数字以外の文字が入力されています。もう一度確認して下さい。");
        return false;
    }
    if (cp_sale !== '' && cp_sale.match(/[^0-9]+/)) {
        alert("メーカー希望小売価格に数字以外の文字が入力されています。もう一度確認して下さい。");
        return false;
    }
    if (cp_saleall !== '' && cp_saleall.match(/[^0-9]+/)) {
        alert("メーカー希望卸売価格に数字以外の文字が入力されています。もう一度確認して下さい。");
        return false;
    }
    if(cp_sale1.match(/[^0-9]+/)){
        alert("販売単価に数字以外の文字が入力されています。もう一度確認して下さい。");
        return false;
    }
    if (cp_jj !== '' && cp_jj > cp_sale1) {
        alert('販売単価＜仕入単価のことが許可されません。');
        return false;
    }
    return true;
}
</script>
<body onload="form1.cp_tm.focus()">
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
      <td><strong>&nbsp;商品基本情報管理</strong>(※：オレンジ色が必須項目)&nbsp;&nbsp;
          - <input type="button" value="商品新規登録" onClick="location.href='system_basic_cp.php'"> 
          - <input type="button" value="商品基本情報検索" onClick="location.href='system_basic_cp.php?action=seek'">
      </td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
 <form action="system_basic_cp.php?action=save" method="post" name="form1">
   <tr>
    <input type="hidden" name="conditions">
    <td class="cellcolor">商品コード：</td>
    <td>&nbsp;<input type="text" name="cp_number" id="need" maxlength="15" value="<?php echo $cp_number ?>" readonly style="color:red;ime-mode:disabled">
        &nbsp;<input type="button" value="商品コード選択" onclick="getcode()">
    </td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">バーコード：</td>
    <td>&nbsp;<input type="text" name="cp_tm" maxlength="15" value="<?php echo $cp_tm ?>" readonly style="ime-mode:disabled">&nbsp;</td>
  </tr>  
  <tr>
    <td class="cellcolor" width="30%">メーカ・商品名：</td>
    <td>&nbsp;<input type="text" name="cp_name" id="need" maxlength="255" value="<?php echo $cp_name ?>" size="61"></td>
  </tr>
  <tr>
    <td class="cellcolor">タイトル：</td>
    <td>&nbsp;<textarea rows="3" cols="60" id="need" name="cp_title"><?php echo $cp_title ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">仕様：</td>
    <td>
        &nbsp;<input type="text" class="rtext" readonly name="titgoods" value="<?php echo $titgoods; ?>">
        <br>
        &nbsp;<textarea rows="15" cols="60" name="cp_detail_1"><?php echo $cp_detail_1; ?></textarea>
        <br>
        &nbsp;<input type="text" class="rtext" readonly name="titcomputer" value="<?php echo $titcomputer; ?>">
        <br>
        &nbsp;<textarea rows="15" cols="60" name="cp_detail_2"><?php echo $cp_detail_2; ?></textarea>
        <br>
        &nbsp;<input type="text" class="rtext" readonly name="titgoodcode" value="<?php echo $titgoodcode; ?>">
        <br>
        &nbsp;<input type="text" name="cp_detail_3" maxlength="15" value="<?php echo $cp_detail_3; ?>" readonly style="color:red;ime-mode:disabled">
        <br>&nbsp;
    </td>
  </tr>
  <tr>
    <td class="cellcolor">商品説明：</td>
    <td>&nbsp;<textarea rows="20" cols="60" name="cp_gg"><?php echo $cp_gg; ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">商品分類：</td>
    <td>
    &nbsp;<?php
        if (!is_null($cp_categories) and !is_null($cp_categories_down)) {
            getcategories($cp_categories, $cp_categories_down);
        } else {
            getcategories(0,'');
        }
	?>	</td>
  </tr>
  <tr>
    <td class="cellcolor">単位：</td>
    <td>&nbsp;<?php getdw() ?></td>
  </tr>
  <tr>
   <td class="cellcolor">商品タイプ：</td>
   <td>&nbsp;<select name="cp_style"><option selected value="1">正常販売商品</option><option value="0">非販売商品</option></select>&nbsp;販売一覧表に非販売商品を表示しません。
   </td>
  </tr>
  <tr>
    <td class="cellcolor">仕入単価：<br>(入庫時修正可能)</td>
    <td>&nbsp;<input type="text" name="cp_jj"></td>
  </tr>
  <tr>
    <td class="cellcolor">メーカー希望小売価格：</td>
    <td>&nbsp;<input type="text" name="cp_sale"></td>
  </tr>
  <tr>
    <td class="cellcolor">メーカー希望卸売価格：</td>
    <td>&nbsp;<input type="text" name="cp_saleall"></td>
  </tr>
  <tr>
   <td class="cellcolor">販売価格：</td>
   <td>&nbsp;<input type="text" id="need" name="cp_sale1" value="<?php echo $cp_sale1 ?>">
  </tr>
  <tr>
    <td class="cellcolor">生産日付：</td>
    <td>&nbsp;<input type="text" name="cp_sdate" class="Wdate" onClick="WdatePicker()"></td>
  </tr>
  <tr>
    <td class="cellcolor">廃棄日付：</td>
    <td>&nbsp;<input type="text" name="cp_edate" class="Wdate" onClick="WdatePicker()"></td>
  </tr>
  <tr>
    <td class="cellcolor">仕入先：</td>
    <td>&nbsp;<input type="text" name="cp_gys">&nbsp;<img src="images/up.gif" border="0" align="absmiddle" style="cursor:hand;" onclick="window.open('select_gys.php?form=form1&field=cp_gys','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=250,height=270,top=200,left=520,scrollbars=yes')" />仕入先選択</td>
  </tr>
  <tr>
    <td class="cellcolor">メインURL：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url" value="<?php echo $cp_url; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL1：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_1" value="<?php echo $cp_url_1; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL2：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_2" value="<?php echo $cp_url_2; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL3：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_3" value="<?php echo $cp_url_3; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL4：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_4" value="<?php echo $cp_url_4; ?>">&nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">推奨ブラウズノード（Amazon用）：</td>
    <td>
        &nbsp;<input type="text" maxlength="10" size="20" name="cp_browse_node_1" value="<?php echo $cp_browse_node_1; ?>">
        &nbsp;<input type="text" maxlength="10" size="20" name="cp_browse_node_2" value="<?php echo $cp_browse_node_2; ?>">
    </td>
  </tr>  
  <tr>
    <td class="cellcolor">キーワード（Amazon用）：</td>
    <td>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword" value="<?php echo $cp_helpword; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_1" value="<?php echo $cp_helpword_1; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_2" value="<?php echo $cp_helpword_2; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_3" value="<?php echo $cp_helpword_3; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_4" value="<?php echo $cp_helpword_4; ?>"/><br />
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_5" value="<?php echo $cp_helpword_5; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_6" value="<?php echo $cp_helpword_6; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_7" value="<?php echo $cp_helpword_7; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_8" value="<?php echo $cp_helpword_8; ?>"/>
        &nbsp;<input type="text" maxlength="250" size="20" name="cp_helpword_9" value="<?php echo $cp_helpword_9; ?>"/>
    </td>
  </tr>    
  <tr>
    <td class="cellcolor">備考：</td>
    <td>&nbsp;<textarea rows="3" cols="76" name="cp_bz"><?php echo $cp_bz; ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;<input type="button" value=" 登録 " onclick="checkForm()"></td>
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
}
copyright();
?>
</body>
</html>
