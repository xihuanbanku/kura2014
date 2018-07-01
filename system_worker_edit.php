<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($id=='')
ShowMsg('引数不正','-1');
if($action=='save'){
    if($s_no==''){
        ShowMsg('社員IDを入力してください。','-1');
        exit();
    }
    if($s_name==''){
        ShowMsg('社員名を入力してください。','-1');
        exit();
    }
    $addsql="update #@__staff set s_no='$s_no',s_name='$s_name',s_address='$s_address',s_phone='$s_phone',s_part='$s_part',s_way='$s_way',s_money='$s_money',s_utype='$s_utype' where id='$id'";
    $addsql2="update #@__boss set boss='$s_no',rank='$s_utype' where boss='$s_name_old'";
    $message= "社員：".$s_name."さんの情報を修正しました。";
    $loginip=getip();
    $logindate=getdatetimemk(time());
    $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
    $asql=New Dedesql(false);
    $asql->ExecuteNoneQuery($addsql);
    echo $addsql;
    echo $addsql2;
    $asql->ExecuteNoneQuery($addsql2);
    $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
    $asql->close();
    showmsg('社員情報を修正しました','system_worker.php');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>社員管理</title>
<script language="javascript">
function cway(value){
    if(value==0)
    document.forms[0].s_e.value="%";
    else
    document.forms[0].s_e.value="円/件";
}
</script>
</head>
<body>
<?php
$esql=New Dedesql(false);
$query="select * from #@__staff where id='$id'";
$esql->SetQuery($query);
$esql->Execute();
if($esql->GetTotalRow()==0){
    ShowMsg('引数エラー、もう一度実行してください。','-1');
    exit();
}
$row=$esql->GetOne($query);
$esql->close();
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
     <tr>
      <td><strong>&nbsp;社員情報修正</strong></td>
     </tr>
	 <form action="system_worker_edit.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
            <tr>
		 <td id="row_style">&nbsp;社員ID/登録ID：</td>
		 <td>
		 &nbsp;<input type="text" name="s_no" size="10" value="<?php echo $row['s_no'] ?>" id="need"/>
                     <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                     <input type="hidden" name="s_no_old" value="<?php echo $row['s_no'] ?>"/>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;名前：</td>
		 <td>
		 &nbsp;<input type="text" name="s_name" size="20" value="<?php echo $row['s_name'] ?>" id="need"/>
                     <input type="hidden" name="s_name_old" value="<?php echo $row['s_name'] ?>"/>
		 </td>
	    </tr>
	    <tr>
		 <td id="row_style">&nbsp;住所：</td>
		 <td>
		 &nbsp;<input type="text" name="s_address" size="30" value="<?php echo $row['s_address'] ?>"/></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;電話番号：</td>
		 <td>
		 &nbsp;<input type="text" name="s_phone" size="15" value="<?php echo $row['s_phone'] ?>"/></td>
	    </tr>	
		<tr>
		 <td id="row_style">&nbsp;所属：</td>
		 <td>
		 &nbsp;<input type="text" name="s_part" size="20" value="<?php echo $row['s_part'] ?>"/></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;職務：</td>
		 <td>
		 &nbsp;<?php echo getusertype($row['s_duty'],1) ?></td>
	    </tr>
		<tr>
		 <td id="row_style">&nbsp;割戻方法：</td>
		 <td>
		 <?php
		 if ($cfg_way=='1'){
		  if ($row['s_way']==0){
		 ?>
		 &nbsp;<select name="s_way" onchange="cway(this.value)"><option value="0" selected>売上総額の割合</option><option value="1">固定(件数より)</option></select>
		 <?php
		 }
		 else
		 echo "&nbsp;<select name=\"s_way\" onchange=\"cway(this.value)\"><option value=\"0\">売上総額の割合</option><option value=\"1\" selected>固定(件数より)</select>";
		 }
		 else
		 echo "&nbsp;割戻機能が禁止されている。";
		 ?>
		 </td>
	    </tr>		
		<tr>
		 <td id="row_style">&nbsp;割合(ブランクは割戻なし):</td>
		 <td>
		 <?php
		 if ($cfg_way=='1'){
		  if ($row['s_way']==0){
		 ?>
		 &nbsp;<input type="text" name="s_money" size="5" value="<?php echo $row['s_money'] ?>">
		 <input type="text" name="s_e" size="5" style="border:0px;background:transparent;" value="%" readonly>
		 <?php
		 }
		 else
		 echo "&nbsp;<input type=\"text\" name=\"s_money\" size=\"5\" value=\"".$row['s_money']."\">
		 <input type=\"text\" name=\"s_e\" size=\"5\" style=\"border:0px;background:transparent;\" value=\"円/件\" readonly>";
		 }
		 else
		 echo "&nbsp;";
		 ?>
		 </td>
	    </tr>							
		<tr>
		 <td id="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value=" 修正 "/></td>
	    </tr>
		</form>
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
