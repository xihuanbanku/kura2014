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
<title>在庫商品修正</title>
<style type="text/css">
.rtext {background:transparent;border:0px;color:red;font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
</style>
<script language="javascript">
function chkInput() {
    var number = document.forms[0].kc_number.value;
    var v_number = document.forms[0].v_number.value;
    var floor = document.forms[0].kc_floor.value;
    var shelf = document.forms[0].kc_shelf.value;
    var zone = document.forms[0].kc_zone.value;
    var horizontal = document.forms[0].kc_horizontal.value;
    var vertical = document.forms[0].kc_vertical.value;
    var p_kid = document.forms[0].p_kid.value;
    var package_size = document.forms[0].package_size.value;

    if (isNaN(number) || number < 0) {
        alert('正しい入庫数を入力してください。');
        return false;
    }

    if (isNaN(v_number) || (number <= 0 && v_number > 0) || v_number < 0) {
        alert('正しい仮想数を入力してください。');
        return false;
    }
    if (floor == '') {
        alert('位置-->階を入力してください。');
        return false;
    }
    if (shelf == '') {
        alert('位置-->棚を入力してください。');
        return false;
    }
    if (zone == '') {
        alert('位置-->ゾーンを入力してください。');
        return false;
    }
    if (horizontal == '') {
        alert('位置-->横を入力してください。');
        return false;
    }
    if (vertical == '') {
        alert('位置-->縦を入力してください。');
        return false;
    }
    if (p_kid == '') {
        alert('父级关联K_IDを入力してください。');
        return false;
    }
    if (package_size == '') {
        alert('组合系数を入力してください。');
        return false;
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
    if (sorting == "") {
        sorting = "desc";
    }
    window.location.href = 'system_kc.php?action=' + action + '&labid=' + labid + '&cp_categories=' + cp_categories
        + '&cp_categories_down=' + cp_categories_down + '&sort=' + sort + '&stext=' + stext + '&sorting=' + sorting + '&sortkbn=' + sortkbn;
}
</script>
</head>
<?php
if ($action == 'save'){
    if ($kc_number == '' || $id == '') {
        showmsg('引数エラー', '-1');
        exit();
    }
    $bsql = New Dedesql(false);
    if($p_kid > 0) {
        $query = "select * from #@__mainkc where kid in ($id, $p_kid)";
        $bsql->SetQuery($query);
        $bsql->Execute();
        $rowcount = $bsql->GetTotalRow();
        if ($rowcount < 2) {
            ShowMsg('引数不正、または該当商品がありません。', '-1');
            exit();
        } else {
            if($package_size <= 0) {
                $package_size=1;
            }
            $bsql->executenonequery("update #@__mainkc set number=(select * from (select number/$package_size from #@__mainkc where kid =$p_kid) t1),v_number='$v_number',p_kid='$p_kid',package_size='$package_size',l_floor='$kc_floor',"
                . "l_shelf='$kc_shelf',l_zone='$kc_zone',l_horizontal='$kc_horizontal',l_vertical='$kc_vertical' where kid='" . $id . "'");
            $loginip = getip();
            $logindate = getdatetimemk(time());
            $username = Getcookie('VioomaUserID');
            WriteNote('商品' . $pid . 'をmain_kc修正しました。', $logindate, $loginip, $username);
            ShowMsg('商品情報を修正しました。', 'system_kc.php');
            $bsql->close();
            exit();
        }
    } else {
        $query = "select * from #@__mainkc where kid='$id'";
        $bsql->SetQuery($query);
        $bsql->Execute();
        $rowcount = $bsql->GetTotalRow();
        if ($rowcount == 0) {
            ShowMsg('引数不正、または該当商品がありません。', '-1');
            exit();
        } else {
            $bsql->executenonequery("update #@__mainkc set number='$kc_number',v_number='$v_number',l_floor='$kc_floor',"
                . "l_shelf='$kc_shelf',l_zone='$kc_zone',l_horizontal='$kc_horizontal',l_vertical='$kc_vertical' where kid='" . $id . "'");
            $loginip = getip();
            $logindate = getdatetimemk(time());
            $username = Getcookie('VioomaUserID');
            WriteNote('商品' . $pid . 'をmain_kc修正しました。', $logindate, $loginip, $username);
            ShowMsg('商品情報を修正しました。', 'system_kc.php');
            $bsql->close();
            exit();
        }
    }
}
else{
if ($id == '' || $lid == '') {
    echo "<script language='javascript'>alert('不正な引数');history.go(-1);</script>";
    exit();
}
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
      <td><strong><strong>&nbsp;在庫商品修正</strong>&nbsp;&nbsp;
              - <input type="button" value="商品在庫一覧へ戻る" onClick="backlist();">
      </td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
	   <form action="system_kc_edit.php?action=save" method="post" name="form1">
              <input type="hidden" name="p_actions" value="<?php echo $action; ?>">
              <input type="hidden" name="p_labid" value="<?php echo $labid; ?>">
              <input type="hidden" name="p_cp_categories" value="<?php echo $cp_categories; ?>">
              <input type="hidden" name="p_cp_categories_down" value="<?php echo $cp_categories_down; ?>">
              <input type="hidden" name="p_sort" value="<?php echo $sort; ?>">
              <input type="hidden" name="p_stext" value="<?php echo $stext; ?>">
              <input type="hidden" name="p_sorting" value="<?php echo $sorting; ?>">
              <input type="hidden" name="p_sortkbn" value="<?php echo $sortkbn; ?>">
    <tr height="30">
    <td class="cellcolor">商品コード：</td>
    <td><input type="text" name="pid" value="<?php echo $pid; ?>" readonly size="15"><input type="hidden" name="id" value="<?php echo $id; ?>" />
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">商品名：<br></td>
    <td>&nbsp;<input type="text" name="seek_text" value="<?php echo get_name($pid,'name')?>" readonly>&nbsp;
            (商品情報修正はこちらへ → <a href="system_basic_edit.php?formCd=kc&id=<?php echo $pid; ?>"><font color="red"><b>商品情報</b></font></a>)
	<input type="hidden" name="seek_number" value=""/>
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">倉庫：<br></td>
    <td>&nbsp;<?php echo get_name($lid,'lab') ?>
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">現在庫数：<br></td>
    <?php
        $rank = GetCookie("rank");
        $readonly = "";
        $styleClass = "";
        if ($rank != 1 and $rank != 100 and $rank != 105) {
            $readonly = "readonly";
            $styleClass = "rtext";
        }
    ?>
    <td>&nbsp;<input type="text" class="<?php echo $styleClass; ?>" <?php echo $readonly; ?> name="kc_number" size="5" value="<?php echo $n; ?>">&nbsp;<?php echo get_name(get_name($pid,'dwname'),'dw')?>
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">仮想数：<br></td>
    <?php
        $rank = GetCookie("rank");
        $readonly = "";
        $styleClass = "";
        if ($rank != 1 and $rank != 100 and $rank != 105) {
            $readonly = "readonly";
            $styleClass = "rtext";
        }
    ?>
    <td>&nbsp;<input type="text" class="<?php echo $styleClass; ?>" <?php echo $readonly; ?> name="v_number" size="5" value="<?php echo $v_number; ?>">&nbsp;<?php echo get_name(get_name($pid,'dwname'),'dw')?>
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">父级关联K_ID：<br></td>
    <?php
        $rank = GetCookie("rank");
        $readonly = "";
        $styleClass = "";
        if ($rank != 1 and $rank != 100 and $rank != 105) {
            $readonly = "readonly";
            $styleClass = "rtext";
        }
    ?>
    <td>&nbsp;<input type="text" class="<?php echo $styleClass; ?>" <?php echo $readonly; ?> name="p_kid" size="5" value="<?php echo $p_kid; ?>">
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">组合系数：<br></td>
    <?php
        $rank = GetCookie("rank");
        $readonly = "";
        $styleClass = "";
        if ($rank != 1 and $rank != 100 and $rank != 105) {
            $readonly = "readonly";
            $styleClass = "rtext";
        }
    ?>
    <td>&nbsp;<input type="number" class="<?php echo $styleClass; ?>" <?php echo $readonly; ?> name="package_size" size="5" value="<?php echo $package_size; ?>">
	</td>
  </tr>
  <tr>
    <td class="cellcolor" width="30%">位置：<br></td>
    <td>
        &nbsp;階:<input type="text" name="kc_floor" size="3" style="ime-mode: disabled;" maxlength="2" value="<?php echo $floor; ?>"/>&nbsp;&nbsp;
        棚:<input type="text" name="kc_shelf" size="3" style="ime-mode: disabled;" value="<?php echo $shelf; ?>" maxlength="2"/>&nbsp;&nbsp;
        ゾーン:<input type="text" name="kc_zone" size="3" style="ime-mode: disabled;" value="<?php echo $zone; ?>" maxlength="2"/>&nbsp;&nbsp;
        横:<input type="text" name="kc_horizontal" size="3" style="ime-mode: disabled;" value="<?php echo $horizontal; ?>" maxlength="2"/>&nbsp;&nbsp;
        縦:<input type="text" name="kc_vertical" size="3" style="ime-mode: disabled;" value="<?php echo $vertical; ?>" maxlength="2"/>
    </td>
   </tr>
  <tr id="product_date" style="display:block;">
   <td colspan="2">
   &nbsp;その他情報： <font color=red><?php echo "型番・詳細：".get_name($pid,'gg')."  分類：".get_name(get_name($pid,'bcate'),'categories')."->".get_name(get_name($pid,'scate'),'categories');?></font>
   </td>
  </tr>
  <tr>
    <td class="cellcolor">&nbsp;</td>
    <td>&nbsp;<input type="submit" onclick="return chkInput();" value="<?php echo get_name($pid,'name')?>の情報を保存"></td>
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
