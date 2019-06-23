<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save')
{
$configfile = dirname(__FILE__)."/include/config_hand.php";
$configfile_bak = dirname(__FILE__)."/include/config_hand_bak.php";
if(!is_writeable($configfile)){
	echo "設定ファイル'{$configfile}'修正禁止！";
	exit();
}
$savesql = new DedeSql(false);
foreach($_POST as $k=>$v){
	if(preg_match("^edit___",$k)){
		$v = ${$k};

	}else continue;
	$k = preg_replace("/^edit___/","",$k);
		if(strlen($v) > 250){
			showmsg("$k 長さが250バイト超えている",'-1');
			exit;
		}
		$savesql->ExecuteNoneQuery("Update #@__config set `config_value`='$v' where `config_name`='$k' ");
		}
	$savesql->SetQuery("Select `config_name`,`config_value` From `#@__config` order by `id` asc");
  $savesql->Execute();
  if($savesql->GetTotalRow()<=0){
		$savesql->Close();
		ShowMsg("環境変数を修正しましたが、データーベース書き込みエラーが発生しました。","javascript:;");
	  exit();
	}
  @copy($configfile,$configfile_bak);
	$fp = @fopen($configfile,'w');
	@flock($fp,3);
	@fwrite($fp,"<"."?php\r\n") or die("設定ファイル'{$configfile}'書込禁止<a href='system_basic.php'>戻る</a>");
  while($row = $savesql->GetArray()){
  	$row['value'] = str_replace("'","\\'",$row['config_value']);
  	fwrite($fp,"\${$row['config_name']} = '".$row['config_value']."';\r\n");
  }
  fwrite($fp,"?>");
  fclose($fp);
    $message="設定ファイルconfig_base.phpを修正しました。";
	$logindate=getdatetimemk(time());;
	$loginip=getip();
	//$username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
        $username=GetCookie('VioomaUserID');
	$savesql->Close();
	WriteNote($message,$logindate,$loginip,$username);
	ShowMsg("設定が成功しました。","system_basic.php");
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>システム環境設定</title>
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
      <td><strong>&nbsp;システム環境設定</strong></td>
     </tr><form action="system_basic.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
	      <td class="cellcolor" width="50%">会社名： </td>
	      <td>&nbsp;<input type="" name="edit___cfg_webname" value="<?php echo $cfg_webname;?>" size="50"/></td></tr>
		<tr>
	    <tr>
	      <td class="cellcolor">住所： </td>
	      <td>&nbsp;<input type="" name="edit___cfg_address" value="<?php echo $cfg_address;?>" size="50"/></td></tr>
		<tr>
	    <tr>
	      <td class="cellcolor">連絡担当者： </td>
	      <td>&nbsp;<input type="" name="edit___cfg_conact" value="<?php echo $cfg_conact;?>" size="20"/></td></tr>
		<tr>
	    <tr>
	      <td class="cellcolor">電話番号： </td>
	      <td>&nbsp;<input type="" name="edit___cfg_phone" value="<?php echo $cfg_phone;?>" size="20"/></td></tr>
		<tr>				
		  <td class="cellcolor">システムパス：</td>
		  <td>&nbsp;<input type="" name="edit___cfg_basehost" value="<?php echo $cfg_basehost;?>" size="50"/></td></tr>
		<tr>
		  <td class="cellcolor">システムセットアップパス:</td>
		  <td>&nbsp;<input type="" name="edit___cfg_cmspath" value="<?php echo $cfg_cmspath;?>" size="20"/></td></tr>
		<tr>
		  <td class="cellcolor">Cookie暗号化： <br>(セキュリティの為Cookie暗号化を行う。出来る限り複雑な暗号文字列を使ってください)</td>
		  <td>&nbsp;<input type="" name="edit___cfg_cookie_encode" value="<?php echo $cfg_cookie_encode;?>" size="20"/></td></tr>
		<tr>
		  <td class="cellcolor">セッション保持時間(単位：時間): </td>
		  <td>&nbsp;<input type="" name="edit___cfg_keeptime" value="<?php echo $cfg_keeptime;?>" size="5"/>&nbsp;時間</td></tr>
		<tr>
		  <td class="cellcolor">会員レベルを使用: </td>
		  <td>
		  &nbsp;
		  <?php 
		  if($cfg_islevel==1) 
		  echo "<input type=\"radio\" name=\"edit___cfg_islevel\" checked value=\"1\">はい&nbsp;<input type=\"radio\" name=\"edit___cfg_islevel\" value=\"0\">いいえ" ;
		  else 
		  echo "<input type=\"radio\" name=\"edit___cfg_islevel\" value=\"1\">はい&nbsp;<input checked type=\"radio\" name=\"edit___cfg_islevel\" value=\"0\">いいえ";
		  ?></td></tr>
		<tr>
		  <td class="cellcolor">会員レベルよって割引販売を行う: </td>
		  <td>
		  &nbsp;
		  <?php 
		  if($cfg_isdiscount==1) 
		  echo "<input type=\"radio\" name=\"edit___cfg_isdiscount\" checked value=\"1\">はい&nbsp;<input type=\"radio\" name=\"edit___cfg_isdiscount\" value=\"0\">いいえ" ;
		  else 
		  echo "<input type=\"radio\" name=\"edit___cfg_isdiscount\" value=\"1\">はい&nbsp;<input checked type=\"radio\" name=\"edit___cfg_isdiscount\" value=\"0\">いいえ";
		  ?></td></tr>
		<tr>
		  <td class="cellcolor">在庫アラームを使用: </td>
		  <td>
		  &nbsp;
		  <?php 
		  if($cfg_isalarm==1) 
		  echo "<input type=\"radio\" name=\"edit___cfg_isalarm\" checked value=\"1\">はい&nbsp;<input type=\"radio\" name=\"edit___cfg_isalarm\" value=\"0\">いいえ" ;
		  else 
		  echo "<input type=\"radio\" name=\"edit___cfg_isalarm\" value=\"1\">はい&nbsp;<input checked type=\"radio\" name=\"edit___cfg_isalarm\" value=\"0\">いいえ";
		  ?></td></tr>
		<tr>
		<tr>
		  <td class="cellcolor">レポート印刷時会社情報を表示: </td>
		  <td>
		  &nbsp;
		  <?php 
		  if($cfg_isshow==1) 
		  echo "<input type=\"radio\" name=\"edit___cfg_isshow\" checked value=\"1\">はい&nbsp;<input type=\"radio\" name=\"edit___cfg_isshow\" value=\"0\">いいえ" ;
		  else 
		  echo "<input type=\"radio\" name=\"edit___cfg_isshow\" value=\"1\">はい&nbsp;<input checked type=\"radio\" name=\"edit___cfg_isshow\" value=\"0\">いいえ";
		  ?></td></tr>
		<tr>
		  <td class="cellcolor">社員業務割戻機能を使用: </td>
		  <td>
		  &nbsp;
		  <?php 
		  if($cfg_way==1) 
		  echo "<input type=\"radio\" name=\"edit___cfg_way\" checked value=\"1\">はい&nbsp;<input type=\"radio\" name=\"edit___cfg_way\" value=\"0\">いいえ" ;
		  else 
		  echo "<input type=\"radio\" name=\"edit___cfg_way\" value=\"1\">はい&nbsp;<input checked type=\"radio\" name=\"edit___cfg_way\" value=\"0\">いいえ";
		  ?></td></tr>		  
		<tr>		
		  <td class="cellcolor">レコード/ページ：</td>
		  <td>&nbsp;<input type="" name="edit___cfg_record" value="<?php echo $cfg_record;?>" size="5"/>&nbsp;件/ページ</td></tr>
		  <tr>		
		  <td class="cellcolor">データーバックアップディレクトリ: </td>
		  <td>&nbsp;<input type="" name="edit___cfg_backup_dir" value="<?php echo $cfg_backup_dir;?>" size="20"/>&nbsp;</td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" value=" 設定保存 "></td></tr>
	   </table>
	  </td>
     </tr>
    </table>
	</td>
    <td>&nbsp;</td>
  </tr></form>
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
