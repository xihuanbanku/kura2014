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
<title>商品情報照会</title>
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
    labid = document.forms[0].p_labid.value;
    cp_categories = document.forms[0].p_cp_categories.value;
    cp_categories_down = document.forms[0].p_cp_categories_down.value;
    sort = document.forms[0].p_sort.value;
    stext = document.forms[0].p_stext.value;
    sorting = document.forms[0].p_sorting.value;
    sortkbn = document.forms[0].p_sortkbn.value;
    if (sorting === "") {
        sorting = "desc";
    }
    if (sort === "") {
        sort = "3";
    }
    window.location.href='system_kc.php?action='+action+'&labid='+labid+'&cp_categories='+cp_categories
            +'&cp_categories_down='+cp_categories_down+'&sort='+sort+'&stext='+stext+'&sorting='+sorting+"&sortkbn="+sortkbn;
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
 if($cp_name=='') echo "<script language='javascript'>alert('商品名を入力してください。');history.go(-1)</script>";
 if($cp_gg=='') echo "<script language='javascript'>alert('商品規格を入力してください。');history.go(-1)</script>";
 if($cp_categories=='') echo "<script language='javascript'>alert('商品大分類を選択してください。');history.go(-1)</script>";
 if($cp_categories_down=='') echo "<script language='javascript'>alert('商品小分類を選択してください。');history.go(-1)</script>";
 if($cp_dwname=='') echo "<script language='javascript'>alert('商品単位を選択してください。');history.go(-1)</script>";
 if($cp_sale=='') echo "<script language='javascript'>alert('販売単価を入力してください。');history.go(-1)</script>";
 if(!(is_numeric($cp_jj) && is_numeric($cp_sale) )) echo "<script language='javascript'>alert('単価は数字限り。');history.go(-1)</script>";
 if($cp_jj!='' and $cp_jj>$cp_sale) echo "<script language='javascript'>alert('販売単価＜仕入単価のことが許可されません。');history.go(-1)</script>";
$bsql=New Dedesql(false);
$query="update #@__basic set cp_number='".$cp_number."',cp_tm='".$cp_tm."',cp_name='".$cp_name."',cp_gg='".$cp_gg."',cp_categories='".$cp_categories."',cp_categories_down='".$cp_categories_down."',cp_dwname='".$cp_dwname."',cp_style='".$cp_style."',cp_jj='".$cp_jj."',cp_sale='".$cp_sale."',cp_saleall='".$cp_saleall."',cp_sale1='".$cp_sale1."',cp_sdate='".$cp_sdate."',cp_edate='".$cp_edate."',cp_gys='".$cp_gys."',cp_helpword='".$cp_helpword."',cp_bz='".$cp_bz."' where id='$id'";
$bsql->ExecuteNoneQuery($query);
showmsg('商品情報を修正しました。','system_basic_cp.php?action=seek');
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=GetCookie('UserID');
 WriteNote('商品'.$cp_name.'['.$id.'] の情報を修正しました。',$logindate,$loginip,$username);
$bsql->close();
exit();
}
$seekrs=New Dedesql(falsh);
$squery="select * from #@__basic where cp_number='$id'";
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
function check(e){
var e = window.event ? window.event : e;
    if(e.keyCode == 13){
    document.forms[0].cp_name.focus();
	return false;
    }
}
function checkForm(){
document.forms[0].submit();
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
         <td><strong>&nbsp;商品情報詳細</strong>&nbsp;&nbsp;&nbsp;<input type="button" value="商品在庫一覧へ戻る" onClick="backlist();"></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
 <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
 <form action="system_basic_edit.php?action=save" method="post" name="form1">
              <input type="hidden" name="p_actions" value="<?php echo $action; ?>">
              <input type="hidden" name="p_labid" value="<?php echo $labid; ?>">
              <input type="hidden" name="p_cp_categories" value="<?php echo $cp_categories; ?>">
              <input type="hidden" name="p_cp_categories_down" value="<?php echo $cp_categories_down; ?>">
              <input type="hidden" name="p_sort" value="<?php echo $sort; ?>">
              <input type="hidden" name="p_stext" value="<?php echo $stext; ?>">
              <input type="hidden" name="p_sorting" value="<?php echo $sorting; ?>">
              <input type="hidden" name="p_sortkbn" value="<?php echo $sortkbn; ?>">
   <tr>
    <td class="cellcolor">商品コード：</td>
    <td>&nbsp;<input type="hidden" value="<?php echo $id?>" name="id"><input type="text" size="20" name="cp_number" value="<?php echo $row['cp_number'] ?>" style="color:red;" readonly="readonly"></td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">バーコード</td>
    <td>&nbsp;<input type="text" name="cp_tm" size="20" value="<?php echo $row['cp_tm'] ?>" readonly>&nbsp;</td>
  </tr>  
  <tr>
    <td class="cellcolor" width="30%">商品名：</td>
    <td>&nbsp;<input type="text" name="cp_name" size="103" onblur="pinyin(this.value)" value="<?php echo $row['cp_name'] ?>" readonly></td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">販売価格：</td>
    <td>&nbsp;<font color="blue">&yen;&nbsp;<?php echo number_format($row['cp_sale1']) ?></font>&nbsp;円</td>
  </tr>
  <tr>
    <td class="cellcolor">タイトル：</td>
    <td>&nbsp;<textarea cols="85" name="cp_detail" rows="5" readonly><?php echo $row['cp_title'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">詳細：</td>
    <td>&nbsp;<textarea cols="85" name="cp_detail" rows="5" readonly><?php echo $row['cp_detail'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">仕様：</td>   
    <td>&nbsp;<textarea cols="85" name="cp_gg" rows="5" readonly><?php echo $row['cp_gg'] ?></textarea></td>
    </td>
  </tr>
  <tr>
    <td class="cellcolor">URL：</td>
    <td>&nbsp;<input type="text" size="61" name="cp_url" readonly value="<?php echo $row['cp_url'] ?>">&nbsp;
    <?php 
        if ($row['cp_url'] != "") {
            $x=get_headers($row['cp_url']);
            if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url']." rel=\"lightbox\"><img src=".$row['cp_url']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
            }
        }
    ?>
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL1：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_1" readonly value="<?php echo $row['cp_url_1'] ?>">&nbsp;
    <?php 
        if ($row['cp_url_1'] != "") {
            $x=get_headers($row['cp_url_1']);
            if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_1']." rel=\"lightbox\"><img src=".$row['cp_url_1']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
            }
        }
    ?>
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL2：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_2" readonly value="<?php echo $row['cp_url_2'] ?>">&nbsp;
    <?php 
        if ($row['cp_url_2'] != "") {
            $x=get_headers($row['cp_url_2']);
            if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_2']." rel=\"lightbox\"><img src=".$row['cp_url_2']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
            }
        }
    ?>
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL3：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_3" readonly value="<?php echo $row['cp_url_3'] ?>">&nbsp;
    <?php 
        if ($row['cp_url_3'] != "") {
            $x=get_headers($row['cp_url_3']);
            if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_3']." rel=\"lightbox\"><img src=".$row['cp_url_3']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
            }
        }
    ?>
    </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;&nbsp;&nbsp;サブURL4：</td>
    <td>&nbsp;<input type="text" maxlength="200" size="61" name="cp_url_4" readonly value="<?php echo $row['cp_url_4'] ?>">&nbsp;
    <?php 
        if ($row['cp_url_4'] != "") {
            $x=get_headers($row['cp_url_4']);
            if(preg_match("/OK$/",$x[0])){
                echo "<a href=".$row['cp_url_4']." rel=\"lightbox\"><img src=".$row['cp_url_4']." width=\"40\" height=\"18\" alt=\"画像\"/></a>";
            }
        }
    ?>
    </td>
  </tr>
  <tr>
    <td class="cellcolor">推奨ブラウズノード（Amazon用）：</td>
    <td>
        &nbsp;<input type="text" maxlength="10" size="20" name="cp_browse_node_1" readonly value="<?php echo $row['cp_browse_node_1'] ?>">
        &nbsp;<input type="text" maxlength="10" size="20" name="cp_browse_node_2" readonly value="<?php echo $row['cp_browse_node_2'] ?>">
    </td>
  </tr>
  <tr>
    <td class="cellcolor">キーワード（Amazon用）：</td>
    <td>&nbsp;<input type="text" size="20" name="cp_helpword" readonly="readonly" value="<?php echo $row['cp_helpword'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_1" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_1'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_2" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_2'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_3" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_3'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_4" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_4'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_5" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_5'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_6" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_6'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_7" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_7'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_8" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_8'] ?>"/>
            &nbsp;<input type="text" name="cp_helpword_9" maxlength="250" size="20" readonly="readonly" value="<?php echo $row['cp_helpword_9'] ?>"/>
            &nbsp;</td>
  </tr>
  <tr>
    <td class="cellcolor">備考：</td>
    <td>&nbsp;<textarea rows="5" cols="85" name="cp_bz" readonly><?php echo $row['cp_bz'] ?></textarea></td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;</td>
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