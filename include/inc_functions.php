<?php
require_once 'JSON.php';
$pinyins = Array();
$g_ftpLink = false;

if( !isset($cfg_ver_lang) ){
    if(preg_match('utf',$cfg_version)) $cfg_ver_lang = 'utf-8';
    else $cfg_ver_lang = 'Shift-JIS';
}

// if($cfg_ver_lang=='utf-8') require_once(dirname(__FILE__).'/pub_charset.php');

function mytime()
{
	return time();
}
function GetCurUrl(){
	if(!empty($_SERVER["REQUEST_URI"])){
		$scriptName = $_SERVER["REQUEST_URI"];
		$nowurl = $scriptName;
	}else{
		$scriptName = $_SERVER["PHP_SELF"];
		if(empty($_SERVER["QUERY_STRING"])) $nowurl = $scriptName;
		else $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
	}
	return $nowurl;
}

function GetClientMac() { 
    $return_array = array(); 
    $temp_array = array(); 
    $mac_addr = ""; 
    @exec("arp -a",$return_array); 

       foreach($return_array as $value) { 
       if(StrPos($value,$_SERVER["REMOTE_ADDR"]) !== false && preg_match("/(:?[0-9a-f]{2}[:-]){5}[0-9a-f]{2}/i",$value,$temp_array)) { 
            $mac_addr = $temp_array[0]; 
            break; 
       }
    }
    return ($mac_addr); 
}

function GetAlabNum($fnum){
	if($GLOBALS['cfg_ver_lang']=='utf-8') $fnum = utf82gb($fnum);
	$nums = array('０','１','２','３','４','５','６','７','８','９','．','ー','＋','：');
	$fnums = array('0','1',  '2','3',  '4','5',  '6', '7','8',  '9','.',  '-', '+',':');
	$fnlen = count($fnums);
	for($i=0;$i<$fnlen;$i++) $fnum = str_replace($nums[$i],$fnums[$i],$fnum);
	$slen = strlen($fnum);
	$oknum = '';
	for($i=0;$i<$slen;$i++){
		if(ord($fnum[$i]) > 0x80) $i++;
		else $oknum .= $fnum[$i];
	}
	if($oknum=="") $oknum=0;
	return $oknum;
}
function Text2Html($txt){
	$txt = str_replace("  ","　",$txt);
	$txt = str_replace("<","&lt;",$txt);
	$txt = str_replace(">","&gt;",$txt);
	$txt = preg_replace("/[\r\n]{1,}/isU","<br/>\r\n",$txt);
	return $txt;
}
function Html2Text($str){
	$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU",'',$str);
	$str = str_replace(array('<br />','<br>','<br/>'), "\n", $str);
	$str = strip_tags($str);
	return $str;
}
function ClearHtml($str){
	$str = Html2Text($str);
	$str = str_replace('<','&lt;',$str);
	$str = str_replace('>','&gt;',$str);
	return $str;
}
function cnw_left($str,$len){
  if($GLOBALS['cfg_ver_lang']=='utf-8'){
    $str =  utf82gb($str);
    return gb2utf8(cn_substrGb($str,$slen,$startdd));
  }else{
    return cnw_mid($str,0,$len);
  }
}
function cnw_mid($str,$start,$slen){
  if(!isset($GLOBALS['__funString'])) require_once(dirname(__FILE__)."/inc/inc_fun_funString.php");
  return Spcnw_mid($str,$start,$slen);
}

function cn_substrGb($str,$slen,$startdd=0){
	$restr = "";
	$c = "";
	$str_len = strlen($str);
	if($str_len < $startdd+1) return "";
	if($str_len < $startdd + $slen || $slen==0) $slen = $str_len - $startdd;
	$enddd = $startdd + $slen - 1;
	for($i=0;$i<$str_len;$i++)
	{
		if($startdd==0) $restr .= $c;
		else if($i > $startdd) $restr .= $c;

		if(ord($str[$i])>127){
			if($str_len>$i+1) $c = $str[$i].$str[$i+1];
			$i++;
		}
		else{	$c = $str[$i]; }

		if($i >= $enddd){
			if(strlen($restr)+strlen($c)>$slen) break;
			else{ $restr .= $c; break; }
		}
	}
	return $restr;
}

function cn_substr($str,$slen,$startdd=0){
	if($GLOBALS['cfg_ver_lang']=='utf-8'){
	  $str =  utf82gb($str);
    return gb2utf8(cn_substrGb($str,$slen,$startdd));
  }else{
  	return cn_substrGb($str,$slen,$startdd);
  }
}

function cn_midstr($str,$start,$len){
	if($GLOBALS['cfg_ver_lang']=='utf-8'){
	  $str =  utf82gb($str);
    return gb2utf8(cn_substrGb($str,$slen,$startdd));
  }else{
  	return cn_substrGb($str,$slen,$startdd);
  }
}

function GetMkTime($dtime)
{
	if(!preg_match("[^0-9]",$dtime)) return $dtime;
	$dt = Array(1970,1,1,0,0,0);
	$dtime = preg_replace("[\r\n\t]|日|秒"," ",$dtime);
	$dtime = str_replace("年","-",$dtime);
	$dtime = str_replace("月","-",$dtime);
	$dtime = str_replace("時",":",$dtime);
	$dtime = str_replace("分",":",$dtime);
	$dtime = trim(preg_replace("[ ]{1,}"," ",$dtime));
	$ds = explode(" ",$dtime);
	$ymd = explode("-",$ds[0]);
	if(isset($ymd[0])) $dt[0] = $ymd[0];
	if(isset($ymd[1])) $dt[1] = $ymd[1];
	if(isset($ymd[2])) $dt[2] = $ymd[2];
	if(strlen($dt[0])==2) $dt[0] = '20'.$dt[0];
	if(isset($ds[1])){
		$hms = explode(":",$ds[1]);
		if(isset($hms[0])) $dt[3] = $hms[0];
		if(isset($hms[1])) $dt[4] = $hms[1];
		if(isset($hms[2])) $dt[5] = $hms[2];
	}
  foreach($dt as $k=>$v){
  	$v = preg_replace("/^0{1,}/","",trim($v));
  	if($v=="") $dt[$k] = 0;
  }
	$mt = @mktime($dt[3],$dt[4],$dt[5],$dt[1],$dt[2],$dt[0]);
	if($mt>0) return $mt;
	else return time();
}

function SubDay($ntime,$ctime){
	$dayst = 3600 * 24;
	$cday = ceil(($ntime-$ctime)/$dayst);
	return $cday;
}

function AddDay($ntime,$aday){
	$dayst = 3600 * 24;
	$oktime = $ntime + ($aday * $dayst);
	return $oktime;
}

function GetDateTimeMk($mktime){
	global $cfg_cli_time;
	if($mktime==""||preg_match("[^0-9]",$mktime)) return "";
	return gmdate("Y-m-d H:i:s",$mktime + 3600 * $cfg_cli_time);
}

function GetDateMk($mktime){
	global $cfg_cli_time;
	if($mktime==""||preg_match("[^0-9]",$mktime)) return "";
	return gmdate("Y-m-d",$mktime + 3600 * $cfg_cli_time);
}

function ChangeDateTime($dt) {
    return date('Y/m/d', strtotime($dt));
}

function delSpace($str) {
    return preg_replace('/(\s|　)/','',$str);
}

function strRepacre($str) {
    $str = preg_replace("/\r/", "<br>", $str);
    $str = preg_replace("/\n/", "<br>", $str);
    $str = preg_replace("/\r\n/", "<br>", $str);
    $str = preg_replace_callback('/\xa3([\xa1-\xfe])/', function ($matches) {
        return 'chr(ord(\1)-0x80)';
    }, $str);
    return $str;
}

function strRepacreBrToSpace($str) {
    $str = preg_replace("/\r/", " ", $str);
    $str = preg_replace("/\n/", " ", $str);
    $str = preg_replace("/\r\n/", " ", $str);
    return $str;
}

function GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) $cip = $_SERVER["HTTP_CLIENT_IP"];
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if(!empty($_SERVER["REMOTE_ADDR"])) $cip = $_SERVER["REMOTE_ADDR"];
	else $cip = "";
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = $cips[0] ? $cips[0] : 'unknown';
	unset($cips);
	return $cip;
}

function GetPinyin($str,$ishead=0,$isclose=1){
	if($GLOBALS['cfg_ver_lang']=='utf-8') $str = utf82gb($str);
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc_fun_funAdmin.php");
  return SpGetPinyin($str,$ishead,$isclose);
}

function GetNewInfo(){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc_fun_funAdmin.php");
  return SpGetNewInfo();
}

function ShowMsg($msg,$gourl='-1',$onlymsg=0,$limittime=0)
{
	  global $dsql,$cfg_ver_lang;
	  if( preg_match("^gb",$cfg_ver_lang) ) $cfg_ver_lang = 'gb2312';
		$htmlhead  = "<html>\r\n<head>\r\n<title>警告メッセージ</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\" />\r\n";
		$htmlhead .= "<base target='_self'/>\r\n</head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
		$htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";

		if($limittime==0) $litime = 2000;
		else $litime = $limittime;

		if($gourl=="-1"){
			if($limittime==0) $litime = 2000;
			$gourl = "javascript:history.go(-1);";
		}

		if($gourl==""||$onlymsg==1){
			$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
		}else{
			$func = "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
			$rmsg = $func;
			$rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #547ac7;border-top:1px solid #547ac7;border-right:1px solid #547ac7;background-color:#668cd8;'>メッセージ：</div>\");\r\n";
			$rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #547ac7;background-color:#f3f3fc'><br/><br/>\");\r\n";
			$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
			$rmsg .= "document.write(\"";
			if($onlymsg==0){
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "<br/><br/><a href='".$gourl."'>画面遷移しない場合、こちらをクリック...</a>"; }
				$rmsg .= "<br/><br/></div>\");\r\n";
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "setTimeout('JumpUrl()',$litime);"; }
			}else{ $rmsg .= "<br/><br/></div>\");\r\n"; }
			$msg  = $htmlhead.$rmsg.$htmlfoot;
		}
		if(isset($dsql) && is_object($dsql)) @$dsql->Close();
		echo $msg;
}

function ExecTime(){
	$time = explode(" ", microtime());
	$usec = (double)$time[0];
	$sec = (double)$time[1];
	return $sec + $usec;
}

function GetEditor($fname,$fvalue,$nheight="350",$etype="Basic",$gtype="print",$isfullpage="false"){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpGetEditor($fname,$fvalue,$nheight,$etype,$gtype,$isfullpage);
}

function GetTemplets($filename){
	if(file_exists($filename)){
     $fp = fopen($filename,"r");
     $rstr = fread($fp,filesize($filename));
     fclose($fp);
     return $rstr;
	}else{ return ""; }
}
function GetSysTemplets($filename){
	return GetTemplets($GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'].'/system/'.$filename);
}

function AttDef($oldvar,$nv){
 	if(empty($oldvar)) return $nv;
 	else return $oldvar;
}

function dd2char($dd){
	if($GLOBALS['cfg_ver_lang']=='utf-8') $dd = utf82gb($dd);
	$slen = strlen($dd);
	$okdd = "";
	for($i=0;$i<$slen;$i++){
		if(isset($dd[$i+1]))
		{
			$n = $dd[$i].$dd[$i+1];
			if(($n>96 && $n<123)||($n>64 && $n<91)){
				$okdd .= chr($n); $i++;
			}
			else $okdd .= $dd[$i];
		}else $okdd .= $dd[$i];
	}
	return $okdd;
}

function PutCookie($key,$value,$cfg_keeptime){
	if(empty($cfg_keeptime)) {
        setcookie($key,$value,time()+3600);
	} else {
        setcookie($key,$value,time()+$cfg_keeptime*3600);
    }
}

function DropCookie($key){
  @session_start();
  global $cfg_cookie_encode;
  setcookie($key,"",time()-3600000);
  $_SESSION['VioomaUserID']='';
  echo "<script language='javascript'>window.location.href='login.php';</script>";
  //header("Location:login.php");
}

function GetCookie($key){
// 	 return str_replace($cfg_cookie_encode,'',$_SESSION[$key]);
	 return $_COOKIE[$key];
}

function GetCkVdValue(){
	@session_start();
	if(isset($_SESSION['v_ckstr'])) $ckvalue = $_SESSION['v_ckstr'];
	else $ckvalue = '';
	return $ckvalue;
}


function MkdirAll($truepath,$mmode){
	global $cfg_ftp_mkdir,$cfg_isSafeMode;
	if($cfg_isSafeMode||$cfg_ftp_mkdir=='Y'){ return FtpMkdir($truepath,$mmode); }
	else{
		  if(!file_exists($truepath)){
		  	 mkdir($truepath,$GLOBALS['cfg_dir_purview']);
		  	 chmod($truepath,$GLOBALS['cfg_dir_purview']);
		  	 return true;
		  }else{
		  	return true;
		  }
  }
}

function ChmodAll($truepath,$mmode){
	global $cfg_ftp_mkdir,$cfg_isSafeMode;
	if($cfg_isSafeMode||$cfg_ftp_mkdir=='Y'){ return FtpChmod($truepath,$mmode); }
	else{ return chmod($truepath,'0'.$mmode); }
}


function CreateDir($spath,$siterefer="",$sitepath=""){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpCreateDir($spath,$siterefer,$sitepath);
}


function StringFilterSearch($str,$isint=0){
	return $str;
}

function GetEncodePwd($pwd){
	global $cfg_pwdtype,$cfg_md5len,$cfg_ddsign;
	$cfg_pwdtype = strtolower($cfg_pwdtype);
	if($cfg_pwdtype=='md5'){
		if(empty($cfg_md5len)) $cfg_md5len = 32;
		if($cfg_md5len>=32) return md5($pwd);
		else return substr(md5($pwd),0,$cfg_md5len);
	}else if($cfg_pwdtype=='dd'){
		return DdPwdEncode($pwd,$cfg_ddsign);
	}else if($cfg_pwdtype=='md5m16'){
		return substr(md5($pwd),8,16);
	}else{
		return $pwd;
	}
}

function TestStringSafe($uid){
	if($uid!=addslashes($uid)) return false;
	if(preg_match("[><\$\r\n\t '\"`\\]",$uid)) return false;
	return true;
}

function DdPwdEncode($pwd,$sign=''){
	global $cfg_ddsign;
	if($sign=='') $sign = $cfg_ddsign;
	$rtstr = '';
	$plen = strlen($pwd);
	if($plen<10) $plenstr = '0'.$plen;
	else $plenstr = "$plen";
	$sign = substr(md5($sign),0,$plen);
	$poshandle = mt_rand(65,90);
	$rtstr .= chr($poshandle);
	$pwd = base64_encode($pwd);
	if($poshandle%2==0){
		$rtstr .= chr(ord($plenstr[0])+18);
		$rtstr .= chr(ord($plenstr[1])+36);
	}
	for($i=0;$i<strlen($pwd);$i++){
		 if($i < $plen){
		   if($poshandle%2==0) $rtstr .= $pwd[$i].$sign[$i];
		   else $rtstr .= $sign[$i].$pwd[$i];
		 }else{ $rtstr .= $pwd[$i]; }
	}
	if($poshandle%2!=0){
		$rtstr .= chr(ord($plenstr[0])+20);
		$rtstr .= chr(ord($plenstr[1])+25);
	}
	return $rtstr;
}

function DdPwdDecode($epwd,$sign=''){
	global $cfg_ddsign;
	$n1=0;
	$n2=0;
	$pwstr='';
	$restr='';
	if($sign=='') $sign = $cfg_ddsign;
	$rtstr = '';
	$poshandle = ord($epwd[0]);
	if($poshandle%2==0)
	{
		$n1 = chr(ord($epwd[1])-18);
		$n2 = chr(ord($epwd[2])-36);
		$pwstr = substr($epwd,3,strlen($epwd)-3);
	}else{
		$n1 = chr(ord($epwd[strlen($epwd)-2])-20);
		$n2 = chr(ord($epwd[strlen($epwd)-1])-25);
		$pwstr = substr($epwd,1,strlen($epwd)-3);
	}
	$pwdlen = ($n1.$n2)*2;
	$pwstrlen = strlen($pwstr);
	for($i=0;$i < $pwstrlen;$i++)
	{
		if($i < $pwdlen){
			if($poshandle%2==0){
				$restr .= $pwstr[$i]; $i++;
			}else{
				$i++; 
				$restr .= $pwstr[$i]; 
			}
		}else{ $restr .= $pwstr[$i]; }
	}
	$restr = base64_decode($restr);
	return $restr;
}

function htmlEncode($string) {
	$string=trim($string);
	$string=str_replace("&","&amp;",$string);
	$string=str_replace("'","&#39;",$string);
	$string=str_replace("&amp;amp;","&amp;",$string);
	$string=str_replace("&amp;quot;","&quot;",$string);
	$string=str_replace("\"","&quot;",$string);
	$string=str_replace("&amp;lt;","&lt;",$string);
	$string=str_replace("<","&lt;",$string);
	$string=str_replace("&amp;gt;","&gt;",$string);
	$string=str_replace(">","&gt;",$string);
	$string=str_replace("&amp;nbsp;","&nbsp;",$string);
	$string=nl2br($string);
	return $string;
}

function filterscript($str) {
	$str = preg_replace("/iframe/","ｉｆｒａｍｅ",$str);
	$str = preg_replace("/script/","ｓｃｒｉｐｔ",$str);
	return $str;
}

function AjaxHead()
{
	global $cfg_ver_lang;
	@header("Pragma:no-cache\r\n");
	@header("Cache-Control:no-cache\r\n");
	@header("Expires:0\r\n");
	@header("Content-Type: text/html; charset={$cfg_ver_lang}");
}

function getarea($areaid)
{
	global $dsql;
	if($areaid==0) return '';
	if(!is_object($dsql)) $dsql = new dedesql(false);
	$areaname = $dsql->GetOne("select name from #@__area where id=$areaid");
	return $areaname['name'];
}


if(file_exists( dirname(__FILE__).'/inc_extend_functions.php' )){
	require_once(dirname(__FILE__).'/inc_extend_functions.php');
}

function GetChannelTable($dsql,$id,$formtype='channel')
{
	global $cfg_dbprefix;
	$retables = array();
	$oldarrays = array(1=>'addonarticle',2=>'addonimages',3=>'addonsoft',4=>'addonflash',5=>'addonproduct',-2=>'addoninfos',-1=>'addonspec');
	if(isset($oldarrays[$id]) && $formtype!='arc')
	{
		$retables['addtable'] = $cfg_dbprefix.$oldarrays[$id];
		$retables['maintable'] = $cfg_dbprefix.'archives';
		if($id==-1) $retables['maintable'] = $cfg_dbprefix.'archivesspec';
		else if($id==-2) $retables['maintable'] = $cfg_dbprefix.'infos';
		$retables['channelid'] = $id;
	}else
	{
	   if($formtype=='arc'){
	   	$retables = $dsql->GetOne(" select c.ID as channelid,c.maintable,c.addtable from `#@__full_search` a left join  #@__channeltype c on  c.ID = a.channelid where a.aid='$id' ",mysql_ASSOC);
	   }
	   else{
	   	$retables = $dsql->GetOne(" Select ID as channelid,maintable,addtable From #@__channeltype where ID='$id' ",mysql_ASSOC);
	   }
	   if(!isset($retables['maintable'])) $retables['maintable'] = $cfg_dbprefix.'archives';
	   if(!isset($retables['addtable'])) $retables['addtable'] = '';
  }
	return $retables;
}

function GetIndexKey($dsql,$typeid=0,$channelid=0)
{
	global $typeid,$channelid,$arcrank,$title,$cfg_plus_dir;
	$typeid = (empty($typeid) ? 0 : $typeid);
	$channelid = (empty($channelid) ? 0 : $channelid);
	$arcrank = (empty($arcrank) ? 0 : $arcrank);
	$iquery = "INSERT INTO `#@__full_search` (`typeid` , `channelid` , `adminid` , `mid` , `att` , `arcrank` ,
	            `uptime` , `title` , `url` , `litpic` , `keywords` , `addinfos` , `digg` , `diggtime` )
                 VALUES ('$typeid', '$channelid', '0', '0', '0', '$arcrank',
              '0', '$title', '', '', '', '', '0', '0');
           ";
	$dsql->ExecuteNoneQuery($iquery);
	return $dsql->GetLastID();
}

function WriteSearchIndex($dsql,&$datas)
{
	UpSearchIndex($dsql,$datas);
}

function UpSearchIndex($dsql,&$datas)
{
	
	$addf = '';
	foreach($datas as $k=>$v){
		if($k!='aid') $addf .= ($addf=='' ? "`$k`='$v'" : ",`$k`='$v'");
	}

	$uquery = "update `#@__full_search` set $addf where aid = '".$datas['aid']."';";

	$rs = $dsql->ExecuteNoneQuery($uquery);

	if(!$rs){
		$gerr = $dsql->GetError();
		//$tbs = GetChannelTable($dsql,$datas['channelid'],'channel');
		//$dsql->ExecuteNoneQuery("Delete From `{$tbs['maintable']}` where ID='{$datas['aid']}'");
		//$dsql->ExecuteNoneQuery("Delete From `{$tbs['addtable']}` where aid='{$datas['aid']}'");
	  //$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='{$datas['aid']}'");
		echo "索引更新が失敗しました，エラー原因： [".$gerr."]";
		echo "<br /> SQL文：<font color='red'>{$uquery}</font>";
		$dsql->Close();
		exit();
	}

	return $rs;
}

function TestHasChannel($cid,$channelid,$issend=-1,$carr='')
{
	global $_Cs;
	if(!is_array($_Cs) && !is_array($carr)){ require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php"); }
	if($channelid==0) return 1;
	if(!isset($_Cs[$cid])) return 0;
	if($issend==-1){
	  if($_Cs[$cid][1]==$channelid||$channelid==0) return 1;
	  else{
	    foreach($_Cs as $k=>$vs){
	  	  if($vs[0]==$cid) return TestHasChannel($k,$channelid,$issend,$_Cs);
	    }
	  }
	}else
	{
		if($_Cs[$cid][2]==$issend && ($_Cs[$cid][1]==$channelid||$channelid==0)) return 1;
	  else{
	    foreach($_Cs as $k=>$vs){
	  	  if($vs[0]==$cid) return TestHasChannel($k,$channelid,$issend,$_Cs);
	    }
	  }
	}
	return 0;
}

function UpDateCatCache($dsql)
{
	$cache1 = dirname(__FILE__)."/../data/cache/inc_catalog_base.php";
	$dsql->SetQuery("Select ID,reID,channeltype,issend From #@__arctype");
	$dsql->Execute();
	$fp1 = fopen($cache1,'w');
	$phph = '?';
	$fp1Header = "<{$phph}php\r\nglobal \$_Cs;\r\n\$_Cs=array();\r\n";
	fwrite($fp1,$fp1Header);
	while($row=$dsql->GetObject()){
		fwrite($fp1,"\$_Cs[{$row->ID}]=array({$row->reID},{$row->channeltype},{$row->issend});\r\n");
	}
	fwrite($fp1,"{$phph}>");
	fclose($fp1);
}

function sendmail($email, $mailtitle, $mailbody, $headers)
{
	global $cfg_sendmail_bysmtp, $cfg_smtp_server, $cfg_smtp_port, $cfg_smtp_usermail, $cfg_smtp_user, $cfg_smtp_password, $cfg_adminemail;
	if($cfg_sendmail_bysmtp == 'Y'){
		$mailtype = 'TXT';
		require_once(dirname(__FILE__).'/mail.class.php');
		$smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
		$smtp->debug = false;
		$smtp->sendmail($email, $cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
	}else{
		@mail($email, $mailtitle, $mailbody, $headers);
	}
}

function highlight($string, $words, $hrefs='',$pretext='', $step='')
{
	if($step != 'me'){
		return preg_replace('/(^|>)([^<]+)(?=<|$)/sUe', "highlight('\\2',\$words, \$hrefs, '\\1', 'me')", $string);
	}
	
	if(is_array($words)){
			$string = str_replace('\"', '"', $string);
			foreach($words as $k => $word){
				if(empty($hrefs[$k])){
					$string = preg_replace('/(^|>)([^<]+)(?=<|$)/sUe', "highlight('\\2',\$word, '', '\\1', 'me')", $string);
				}else{
					$string = preg_replace('/(^|>)([^<]+)(?=<|$)/sUe', "highlight('\\2',\$word, \$hrefs[\$k], '\\1', 'me')", $string);
				}
			}
		return $pretext.$string;
	}else{
		if($hrefs == ''){
			$string = str_replace($words,'<strong><font color="#ff0000">'.$words.'</font></strong>',$string);
		}else{
			if(strpos($string, $words) !== false){
				$string = str_replace($words, '<a href="'.$hrefs.'" style="color:#ff0000;font-weight:bold;">'.$words.'</a>', $string);
			}
		}
		return $pretext.$string;
	}
	
}

$startRunMe = ExecTime();
function my_json_encode($phparr){
    if(function_exists("json_encode")){
        return json_encode($phparr, JSON_FORCE_OBJECT);
    }else{
        $json = new Services_JSON();
        return $json->encode($phparr);
    }
}
function my_json_decode($phparr){
    if(function_exists("json_decode")){
        return json_decode($phparr, true);
    }else{
        $json = new Services_JSON();
        return $json->decode($phparr, true);
    }
}

function php_self(){
    $url = $_SERVER['PHP_SELF']; 
    $filename = end(explode('/',$url)); 
    return $filename;  
}
?>