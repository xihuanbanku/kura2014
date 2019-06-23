<?php
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/config_base.php");
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
<title>商品情報記録</title>
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
    document.form1.cp_categories_down.options[0] = new Option('==小分類==',''); 
    for (i=0;i < onecount; i++) 
    { 
        if (subcat[i][1] == locationid) 
        { 
        document.form1.cp_categories_down.options[document.form1.cp_categories_down.length] = new Option(subcat[i][0], subcat[i][2]);
        } 
    } 

} 
function backlist() {
    action = document.forms[0].p_actions.value;
    cp_categories = document.forms[0].p_cp_categories.value;
    cp_categories_down = document.forms[0].p_cp_categories_down.value;
    sort = document.forms[0].p_sort.value;
    stext = document.forms[0].p_stext.value;
    if (sort === '') {
        sort = "3";
    }
    sorting = document.forms[0].p_sorting.value;
    if (sorting == "") {
        sorting = "desc";
    }
    window.location.href='system_basic_cp.php?action='+action+'&seek=yes&cp_categories='+cp_categories
            +'&cp_categories_down='+cp_categories_down+'&sort='+sort+'&stext='+stext+'&sorting='+sorting;
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
</head>
<?php
if ($action=='save'){
// if($cp_name=='') echo "<script language='javascript'>alert('商品名を入力してください。');history.go(-1)</script>";
// if($cp_title==''){ echo "<script language='javascript'>alert('商品タイトルを入力してください。');history.go(-1)</script>";exit();}
// if($cp_detail==''){ echo "<script language='javascript'>alert('商品詳細情報を入力してください。');history.go(-1)</script>";exit();}
// if($cp_gg=='') {echo "<script language='javascript'>alert('商品仕様を入力してください。');history.go(-1)</script>";exit();}
// if($cp_categories=='') echo "<script language='javascript'>alert('商品大分類を選択してください。');history.go(-1)</script>";
// if($cp_categories_down=='') echo "<script language='javascript'>alert('商品小分類を選択してください。');history.go(-1)</script>";
// if($cp_dwname=='') echo "<script language='javascript'>alert('商品単位を選択してください。');history.go(-1)</script>";
// if($cp_sale=='') echo "<script language='javascript'>alert('販売単価を入力してください。');history.go(-1)</script>";
// if(!(is_numeric($cp_jj) && is_numeric($cp_sale) )) echo "<script language='javascript'>alert('単価は数字限り。');history.go(-1)</script>";
// if($cp_jj!='' and $cp_jj>$cp_sale) echo "<script language='javascript'>alert('販売単価＜仕入単価のことが許可されません。');history.go(-1)</script>";
$bsql=New Dedesql(false);
$cp_title = str_replace(array("\r\n","\n","\r"), ' ', $cp_title);
$query="update #@__basic set cp_number='".$cp_number."', cp_parent='".$cp_parent."', cp_tm='".$cp_tm."',cp_name='".$cp_name."',cp_title='".$cp_title."',"
        . "cp_detail='".$cp_detail."',cp_gg='".$cp_gg."',cp_categories='".$cp_categories."',cp_categories_down='".$cp_categories_down."',"
        . "cp_dwname='".$cp_dwname."',cp_style='".$cp_style."',cp_jj='".$cp_jj."',cp_sale='".$cp_sale."',cp_saleall='".$cp_saleall."',"
        . "cp_sale1='".$cp_sale1."',cp_sdate='".$cp_sdate."',cp_edate='".$cp_edate."',cp_gys='".$cp_gys."',cp_helpword='".$cp_helpword."',"
        . "cp_helpword_1='".$cp_helpword_1."',cp_helpword_2='".$cp_helpword_2."',cp_helpword_3='".$cp_helpword_3."',"
        . "cp_helpword_4='".$cp_helpword_4."',cp_helpword_5='".$cp_helpword_5."',cp_helpword_6='".$cp_helpword_6."',"
        . "cp_helpword_7='".$cp_helpword_7."',cp_helpword_8='".$cp_helpword_8."',cp_helpword_9='".$cp_helpword_9."',"
        . "cp_bz='".$cp_bz."',cp_url='".$cp_url."',cp_url_1='".$cp_url_1."',cp_url_2='".$cp_url_2."',cp_url_3='".$cp_url_3."',"
        . "cp_bullet_1='".$cp_bullet_1."',cp_bullet_2='".$cp_bullet_2."',cp_bullet_3='".$cp_bullet_3."',cp_bullet_4='".$cp_bullet_4."',cp_bullet_5='".$cp_bullet_5."',cp_bullet_6='".$cp_bullet_6."',"
        . "cp_url_4='".$cp_url_4."',cp_browse_node_1='".$cp_browse_node_1."',cp_browse_node_2='".$cp_browse_node_2."' where cp_number='$id'";
$bsql->ExecuteNoneQuery($query);

//根据价格系数更新状态10
$query="SELECT p_type, func_multiple (GROUP_CONCAT(p_value)) AS multiple_num
                    FROM jxc_static WHERE
                    p_name in ('price_1'
                                ,'price_2'
                                ,'price_3'
                                ,'price_4'
                                ,'price_5'
                                ,'price_6'
                                ,'price_7'
                                ,'price_8'
                                ,'price_9'
                                'price_10' )
                    GROUP BY p_type;";
//                         echo "<br/>$notesql";
$bsql->setquery($query);
$bsql->execute();
while ($row = $bsql->GetAssoc()) {
    $query="update jxc_mainkc a, jxc_basic b set a.l_state10=b.cp_jj*{$row["multiple_num"]}
    where a.p_id=b.cp_number and b.cp_jj >= (select p_value from jxc_static where p_type = '{$row["p_type"]}' and p_name = 'price_from')
    and  b.cp_jj <(select p_value from jxc_static where p_type = '{$row["p_type"]}' and p_name = 'price_to')";
    $bsql->ExecuteNoneQuery($query);
}
echo mysql_error();
// $query="update #@__mainkc a set a.l_state10=".$cp_jj."*(select `price_1`*`price_2`*`price_3`*`price_4`*`price_5`*`price_6`*`price_7`*`price_8`*`price_9` from #@__description) where a.p_id='$id'";
// $bsql->ExecuteNoneQuery($query);

showmsg('商品情報を修正しました。','system_basic_cp.php?action=seek');
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('VioomaUserID');
 WriteNote('商品'.$cp_number.' の情報を修正しました。',$logindate,$loginip,$username);
$bsql->close();
exit();
}
$seekrs=New Dedesql(falsh);
$squery="select basic.* from #@__basic basic left join #@__barcode barcode on barcode.productid = basic.cp_number where basic.cp_number='$id'";
$seekrs->SetQuery($squery);
$seekrs->Execute();
$rowcount=$seekrs->gettotalrow();
if($rowcount==0){
Showmsg('不正な引数','-1');
exit();
}
$row=$seekrs->GetOne();
$seekrs->close();
?>
<script language="javascript">
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
        		}
    		});
		}
    });
});
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
    cp_detail = document.forms[0].cp_detail.value;
    cp_gg = document.forms[0].cp_gg.value;
    cp_categories = document.forms[0].cp_categories.value;
    cp_categories_down = document.forms[0].cp_categories_down.value;
    cp_dwname = document.forms[0].cp_dwname.value;
    cp_sale1 = parseInt($("input[name='cp_sale1']").val());
    cp_sale = parseInt($("input[name='cp_sale']").val());
    cp_jj = parseInt($("input[name='cp_jj']").val());
    cp_saleall = parseInt($("input[name='cp_saleall']").val());
    
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
    if (cp_detail === '') {
        alert('商品詳細情報を入力してください。');
        return false;
    }
    if (cp_gg === '') {
        alert('商品仕様を入力してください。');
        return false;
    }
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
//    if (cp_jj !== '' && cp_jj.match(/[^0-9]+/)) {
//        alert("仕入単価に数字以外の文字が入力されています。もう一度確認して下さい。");
//        return false;
//    }
    if (cp_jj !== '' && cp_jj > cp_sale1) {
        alert('販売単価＜仕入単価のことが許可されません。');
        return false;
    }
//    if (cp_sale !== '' && cp_sale.match(/[^0-9]+/)) {
//        alert("メーカー希望小売価格に数字以外の文字が入力されています。もう一度確認して下さい。");
//        return false;
//    }
//    if (cp_saleall !== '' && cp_saleall.match(/[^0-9]+/)) {
//        alert("メーカー希望卸売価格に数字以外の文字が入力されています。もう一度確認して下さい。");
//        return false;
//    }
//    if(cp_sale1.match(/[^0-9]+/)){
//        alert("販売単価に数字以外の文字が入力されています。もう一度確認して下さい。");
//        return false;
//    }
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
      <td><strong>&nbsp;商品情報管理</strong>(※：オレンジ色が必須項目)&nbsp;&nbsp;
          - <input type="button" value="商品新規登録" onClick="location.href='system_basic_cp.php'">
          - <input type="button" value="商品基本情報検索へ戻る" onClick="backlist();">
      </td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
 <form action="system_basic_edit.php?action=save" method="post" name="form1">
        <input type="hidden" name="p_actions" value="<?php echo $action; ?>"/>
        <input type="hidden" name="p_cp_categories" value="<?php echo $cp_categories; ?>"/>
        <input type="hidden" name="p_cp_categories_down" value="<?php echo $cp_categories_down; ?>"/>
        <input type="hidden" name="p_sort" value="<?php echo $sort; ?>"/>
        <input type="hidden" name="p_stext" value="<?php echo $stext; ?>"/>
        <input type="hidden" name="p_sorting" value="<?php echo $sorting; ?>"/>
   <tr>
    <td class="cellcolor">商品コード：</td>
    <td>&nbsp;<input type="hidden" value="<?php echo $id?>" name="id"><input type="text" name="cp_number" value="<?php echo $row['cp_number'] ?>" style="color:red;" readonly="readonly"/>&nbsp;※編集不可</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">親子関連：</td>
    <td>&nbsp;<input type="text" style="background-color:#DCDCDC" name="cp_parent" value="<?php echo $row['cp_parent'] ?>" /></td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">バーコード：</td>
    <td>&nbsp;<input type="text" style="background-color:#DCDCDC" name="cp_tm" value="<?php echo $row['cp_tm'] ?>" readonly/>&nbsp;※編集不可</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">商品名：</td>
    <td>&nbsp;<input type="text" name="cp_name" id="need" onblur="pinyin(this.value)" maxlength="250" size="61" value="<?php echo $row['cp_name'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">タイトル：</td>
    <td>&nbsp;<textarea rows="5" cols="50" id="need" name="cp_title"><?php echo $row['cp_title'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">仕様：</td>
    <td>&nbsp;<textarea rows="10" cols="50" id="need" name="cp_detail"><?php echo $row['cp_detail'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">商品説明：</td>
    <td>&nbsp;<textarea rows="10" cols="50" id="need" name="cp_gg"><?php echo $row['cp_gg'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">箇条書き1：</td>
    <td>&nbsp;<input type="text" name="cp_bullet_1" value="<?php echo $row['cp_bullet_1'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">箇条書き2：</td>
    <td>&nbsp;<input type="text" name="cp_bullet_2" value="<?php echo $row['cp_bullet_2'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">箇条書き3：</td>
    <td>&nbsp;<input type="text" name="cp_bullet_3" value="<?php echo $row['cp_bullet_3'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">箇条書き4：</td>
    <td>&nbsp;<input type="text" name="cp_bullet_4" value="<?php echo $row['cp_bullet_4'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">箇条書き5：</td>
    <td>&nbsp;<input type="text" name="cp_bullet_5" value="<?php echo $row['cp_bullet_5'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">箇条書き6：</td>
    <td>&nbsp;<input type="text" name="cp_bullet_6" value="<?php echo $row['cp_bullet_6'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">商品分類：</td>
    <td>
    &nbsp;<?php
    getcategories($row['cp_categories'],$row['cp_categories_down']);
	?>	</td>
  </tr>
  <tr>
    <td class="cellcolor">単位：</td>
    <td>&nbsp;<?php getdw($row['cp_dwname']) ?></td>
  </tr>
  <td class="cellcolor">商品タイプ：</td>
  <td>
  &nbsp;<?php
  if($row['cp_style']=='1')
  echo "<select name='cp_style'><option selected value='1'>正常販売商品</option><option value='0'>非販売商品</option></select>";
  else
  echo "<select name='cp_style'><option value='1'>正常販売商品</option><option selected value='0'>非販売商品</option></select>";
  ?>&nbsp;販売一覧表に非販売商品を表示しません。
  </td>
  <tr id="price_in" style="display: none;">
    <td class="cellcolor">仕入単価：</td>
    <td>&nbsp;<input type="text" name="cp_jj" value="<?php echo $row['cp_jj'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">メーカー希望小売価格：</td>
    <td>&nbsp;<input type="text" name="cp_sale" value="<?php echo $row['cp_sale'] ?>"/></td>
  </tr>
  <tr>
    <td class="cellcolor">メーカー希望卸売価格：</td>
    <td>&nbsp;<input type="text" name="cp_saleall" value="<?php echo $row['cp_saleall'] ?>"/></td>
  </tr>
  <tr>
   <td class="cellcolor">販売価格：</td>
   <td>&nbsp;<input type="text" name="cp_sale1" id="need" value="<?php echo $row['cp_sale1'] ?>"/>
  </tr>  
  <tr>
    <td class="cellcolor">生産日付：</td>
    <td>&nbsp;<input type="text" name="cp_sdate" value="<?php echo $row['cp_sdate'] ?>" class="Wdate" onClick="WdatePicker()"/></td>
  </tr>
  <tr>
    <td class="cellcolor">廃棄日付：</td>
    <td>&nbsp;<input type="text" name="cp_edate" value="<?php echo $row['cp_edate'] ?>" class="Wdate" onClick="WdatePicker()"/></td>
  </tr>
  <tr>
    <td class="cellcolor">仕入先：</td>
    <td>&nbsp;<input type="text" name="cp_gys" readonly value="<?php echo $row['cp_gys'] ?>"/>&nbsp;<img src="images/up.gif" border="0" align="absmiddle" style="cursor:hand;" onclick="window.open('select_gys.php?form=form1&field=cp_gys','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=250,height=270,top=200,left=520')" />仕入先選択</td>
  </tr>
  <tr>
    <td class="cellcolor">URL：</td>
    <td>&nbsp;<input type="text" maxlength="100" size="61" name="cp_url" value="<?php echo $row['cp_url'] ?>"/>&nbsp;
    <?php 
//         if ($row['cp_url'] != "") {
//             $x=get_headers($row['cp_url']);
//             if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url']." rel=\"lightbox\"><img src=".$row['cp_url']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
//             }
//         }
    ?><img src="images/up.gif" style="cursor:hand;" onclick="window.open('system/file_upload.php?form=form1&field=cp_url','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" />
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL1：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_1" value="<?php echo $row['cp_url_1'] ?>"/>&nbsp;
    <?php 
//         if ($row['cp_url_1'] != "") {
//             $x=get_headers($row['cp_url_1']);
//             if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_1']." rel=\"lightbox\"><img src=".$row['cp_url_1']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
//             }
//         }
    ?><img src="images/up.gif" style="cursor:hand;" onclick="window.open('system/file_upload.php?form=form1&field=cp_url_1','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" />
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL2：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_2" value="<?php echo $row['cp_url_2'] ?>"/>&nbsp;
    <?php 
//         if ($row['cp_url_2'] != "") {
//             $x=get_headers($row['cp_url_2']);
//             if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_2']." rel=\"lightbox\"><img src=".$row['cp_url_2']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
//             }
//         }
    ?><img src="images/up.gif" style="cursor:hand;" onclick="window.open('system/file_upload.php?form=form1&field=cp_url_2','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" />
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL3：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_3" value="<?php echo $row['cp_url_3'] ?>"/>&nbsp;
    <?php 
//         if ($row['cp_url_3'] != "") {
//             $x=get_headers($row['cp_url_3']);
//             if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_3']." rel=\"lightbox\"><img src=".$row['cp_url_3']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
//             }
//         }
    ?><img src="images/up.gif" style="cursor:hand;" onclick="window.open('system/file_upload.php?form=form1&field=cp_url_3','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" />
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL4：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_4" value="<?php echo $row['cp_url_4'] ?>"/>&nbsp;
    <?php 
//         if ($row['cp_url_4'] != "") {
//             $x=get_headers($row['cp_url_4']);
//             if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_4']." rel=\"lightbox\"><img src=".$row['cp_url_4']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
//             }
//         }
    ?><img src="images/up.gif" style="cursor:hand;" onclick="window.open('system/file_upload.php?form=form1&field=cp_url_4','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" />
    </td>
  </tr>
  <tr>
    <td class="cellcolor">推奨ブラウズノード（Amazon用）：</td>
    <td>
        &nbsp;<input type="text" maxlength="10" size="20" name="cp_browse_node_1" value="<?php echo $row['cp_browse_node_1'] ?>"/>
        &nbsp;<input type="text" maxlength="10" size="20" name="cp_browse_node_2" value="<?php echo $row['cp_browse_node_2'] ?>"/>
    </td>
  </tr> 
  <tr>
    <td class="cellcolor">キーワード（Amazon用）：</td>
    <td>&nbsp;<input type="text" name="cp_helpword" maxlength="250" size="20" value="<?php echo $row['cp_helpword'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_1" maxlength="250" size="20" value="<?php echo $row['cp_helpword_1'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_2" maxlength="250" size="20" value="<?php echo $row['cp_helpword_2'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_3" maxlength="250" size="20" value="<?php echo $row['cp_helpword_3'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_4" maxlength="250" size="20" value="<?php echo $row['cp_helpword_4'] ?>"/><br />
            &nbsp;<input type="text" name="cp_helpword_5" maxlength="250" size="20" value="<?php echo $row['cp_helpword_5'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_6" maxlength="250" size="20" value="<?php echo $row['cp_helpword_6'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_7" maxlength="250" size="20" value="<?php echo $row['cp_helpword_7'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_8" maxlength="250" size="20" value="<?php echo $row['cp_helpword_8'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_9" maxlength="250" size="20" value="<?php echo $row['cp_helpword_9'] ?>"/>
            &nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">備考：</td>
    <td>&nbsp;<textarea rows="3" cols="50" name="cp_bz"><?php echo $row['cp_bz'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;<input type="button" value=" 修正 " onclick="checkForm()"/></td>
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
copyright();
?>
</body>
</html>