<?php 
error_reporting(E_ALL || ~E_NOTICE);

define('VIOOMAINC',dirname(__FILE__));

$ckvs = Array('_GET','_POST','_COOKIE','_FILES');
$ckvs4 = Array('HTTP_GET_VARS','HTTP_POST_VARS','HTTP_COOKIE_VARS','HTTP_POST_FILES');

$phpold = 0;
foreach($ckvs4 as $_k=>$_v){ 
	if(!@is_array(${$_v})) continue;
	if(!@is_array(${$ckvs[$_k]})){ 
		${$ckvs[$_k]} = ${$_v}; unset(${$_v}); $phpold=1;
	}
}
foreach($ckvs as $ckv){
   foreach($$ckv AS $_k => $_v){ 
      if(preg_match("^(_|globals|cfg_)",$_k)) unset(${$ckv}[$_k]);
   }
}

require_once(VIOOMAINC."/config_hand.php");

// if(PHP_VERSION > '5.1') {
// 	$time51 = 'Etc/GMT'.($cfg_cli_time > 0 ? '+' : '-').abs($cfg_cli_time);
// 	function_exists('date_default_timezone_set') ? @date_default_timezone_set($time51) : '';
// }


$sessSavePath = VIOOMAINC."/../data/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

$cfg_dbhost = 'mysql410.db.sakura.ne.jp';
$cfg_dbname = 'test';
$cfg_dbuser = 'root';
$cfg_dbpwd = '';
$cfg_dbprefix = 'jxc_';
$cfg_db_language = 'SJIS';

$cfg_softname = "Web在庫管理システム";
$cfg_soft_enname = "2013版";
$cfg_soft_devteam = "イーライズ";
$cfg_version = 'v2013';
$cfg_ver_lang = 'Shift-JIS';

require_once(VIOOMAINC.'/config_passport.php');
require_once(VIOOMAINC.'/config.php');
if(!$__ONLYCONFIG) include_once(VIOOMAINC.'/pub_db_mysql.php');
if(!$__ONLYDB) include_once(VIOOMAINC.'/inc_functions.php');
?>