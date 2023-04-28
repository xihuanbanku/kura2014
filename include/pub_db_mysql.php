<?php
include_once('fix_mysql.inc.php');

date_default_timezone_set("PRC");
$dsql = new DedeSql(false);
class DedeSql
{
	var $linkID;
	var $dbHost;
	var $dbUser;
	var $dbPwd;
	var $dbName;
	var $dbPrefix;
	var $result;
	var $queryString;
	var $parameters;
	var $isClose;
	function __construct($pconnect=false,$nconnect=true)
 	{
 		$this->isClose = false;
 		if($nconnect) $this->Init($pconnect);
  }

	function DedeSql($pconnect=false,$nconnect=true)
	{
		$this->__construct($pconnect,$nconnect);
	}

	function Init($pconnect=false)
	{
		$this->linkID = 0;
		$this->queryString = "";
		$this->parameters = Array();
		$this->dbHost = $GLOBALS["cfg_dbhost"];
		$this->dbUser = $GLOBALS["cfg_dbuser"];
		$this->dbPwd = $GLOBALS["cfg_dbpwd"];
		$this->dbName = $GLOBALS["cfg_dbname"];
		$this->dbPrefix = $GLOBALS["cfg_dbprefix"];
		$this->result["me"] = 0;
		$this->Open($pconnect);
	}
	function SetSource($host,$username,$pwd,$dbname,$dbprefix="jxc_")
	{
		$this->dbHost = $host;
		$this->dbUser = $username;
		$this->dbPwd = $pwd;
		$this->dbName = $dbname;
		$this->dbPrefix = $dbprefix;
		$this->result["me"] = 0;
	}
	function SelectDB($dbname)
	{
		mysql_select_db($dbname);
	}
	function SetParameter($key,$value){
		$this->parameters[$key]=$value;
	}
	function Open($pconnect=false)
	{
        global $dsql;
        if ($dsql && !$dsql->isClose) {
            $this->linkID = $dsql->linkID;
        } else {
            if (!$pconnect) {
                $this->linkID = @mysql_connect($this->dbHost, $this->dbUser, $this->dbPwd);
            } else {
                $this->linkID = @mysql_pconnect($this->dbHost, $this->dbUser, $this->dbPwd);
            }
            CopySQLPoint($this);
        }
        if (!$this->linkID) {
            $this->DisplayError("エラー：<font color='red'>データベース接続エラー</font>");
            exit();
        }
        @mysql_select_db($this->dbName);
        $mysqlver = explode('.', $this->GetVersion());
        $mysqlver = $mysqlver[0] . '.' . $mysqlver[1];
        if ($mysqlver > 4.0) @mysql_query("SET NAMES '" . $GLOBALS['cfg_db_language'] . "';", $this->linkID);
        if ($mysqlver > 5.0) @mysql_query("SET sql_mode='' ;", $this->linkID);
        return true;
    }
	function GetError()
	{
		$str = preg_replace("'|\"","`",mysql_error());
		return $str;
	}


	function Close()
	{
		@mysql_close($this->linkID);
		$this->isClose = true;
		if(is_object($GLOBALS['dsql'])){ $GLOBALS['dsql']->isClose = true; }
		$this->FreeResultAll();
	}


	function ClearErrLink()
	{
		global $cfg_dbkill_time;
		if(empty($cfg_dbkill_time)) $cfg_dbkill_time = 30;
		@$result=mysql_query("SHOW PROCESSLIST",$this->linkID);
    if($result)
    {
       while($proc=mysql_fetch_assoc($result))
       {
          if($proc['Command']=='Sleep'
             && $proc['Time']>$cfg_dbkill_time) @mysql_query("KILL ".$proc["Id"],$this->linkID);
       }
    }
	}

	function CloseLink($dblink)
	{
		@mysql_close($dblink);
	}
	function ExecuteNoneQuery($sql="")
	{
		global $dsql;
		if($dsql->isClose){
			$this->Open(false);
			$dsql->isClose = false;
		}
		if($sql!="") $this->SetQuery($sql);
		if(is_array($this->parameters)){
			foreach($this->parameters as $key=>$value){
				$this->queryString = str_replace("@".$key,"'$value'",$this->queryString);
			}
		}
		return mysql_query($this->queryString,$this->linkID);
	}
	function ExecuteNoneQuery2($sql="")
	{
		global $dsql;
		if($dsql->isClose){
			$this->Open(false);
			$dsql->isClose = false;
		}
		if($sql!="") $this->SetQuery($sql);
		if(is_array($this->parameters)){
			foreach($this->parameters as $key=>$value){
				$this->queryString = str_replace("@".$key,"'$value'",$this->queryString);
			}
		}
		mysql_query($this->queryString,$this->linkID);
		return mysql_affected_rows($this->linkID);
	}
	function ExecNoneQuery($sql="")
	{
		return $this->ExecuteNoneQuery($sql);
	}
	function Execute($id="me",$sql="")
	{
		global $dsql;
		if($dsql->isClose){
			$this->Open(false);
			$dsql->isClose = false;
		}
		if($sql!="") $this->SetQuery($sql);
		$this->result[$id] = @mysql_query($this->queryString,$this->linkID);
		if(!$this->result[$id]){
			$this->DisplayError(mysql_error()." - Execute Query False! <font color='red'>".$this->queryString."</font>");
		}
	}
	function Query($id="me",$sql="")
	{
		$this->Execute($id,$sql);
	}
	function GetOne($sql="",$acctype=MYSQL_BOTH)
	{
		global $dsql;
		if($dsql->isClose){
			$this->Open(false);
			$dsql->isClose = false;
		}
		if($sql!=""){
		  if(!preg_match("limit",$sql)) $this->SetQuery(preg_replace("/[,;]$/","",trim($sql))." limit 0,1;");
		  else $this->SetQuery($sql);
		}
		$this->Execute("one");
		$arr = $this->GetArray("one",$acctype);

        if(!is_array($arr)) return("");
		else { @mysql_free_result($this->result["one"]); return($arr);}

	}
	function ExecuteSafeQuery($sql,$id="me")
	{
		global $dsql;
		if($dsql->isClose){
			$this->Open(false);
			$dsql->isClose = false;
		}
		$this->result[$id] = @mysql_query($sql,$this->linkID);
	}
	function GetArray($id="me",$acctype=MYSQL_BOTH)
	{
		if($this->result[$id]==0) return false;
		else return mysql_fetch_array($this->result[$id],$acctype);
	}
	function GetAssoc($id="me",$acctype=MYSQL_BOTH)
	{
		if($this->result[$id]==0) return false;
		else return mysql_fetch_assoc($this->result[$id],$acctype);
	}
	function GetObject($id="me")
	{
		if($this->result[$id]==0) return false;
		else return mysql_fetch_object($this->result[$id]);
	}
	function IsTable($tbname)
	{
		$this->result[0] = mysql_list_tables($this->dbName,$this->linkID);
		while ($row = mysql_fetch_array($this->result[0]))
		{
			if(strtolower($row[0])==strtolower($tbname))
			{
				mysql_freeresult($this->result[0]);
				return true;
			}
		}
		mysql_freeresult($this->result[0]);
		return false;
	}
	function GetVersion()
	{
		global $dsql;
		if($dsql->isClose){
			$this->Open(false);
			$dsql->isClose = false;
		}
		$rs = mysql_query("SELECT VERSION();",$this->linkID);
		$row = mysql_fetch_array($rs);
		$mysql_version = $row[0];
		mysql_free_result($rs);
		return $mysql_version;
	}
	function GetTableFields($tbname,$id="me")
	{
		$this->result[$id] = mysql_list_fields($this->dbName,$tbname,$this->linkID);
	}
	function GetFieldObject($id="me")
	{
		return mysql_fetch_field($this->result[$id]);
	}
	function GetTotalRow($id="me")
	{
		if($this->result[$id]==0) return -1;
		else return mysql_num_rows($this->result[$id]);
	}
	function GetLastID()
	{
		return mysql_insert_id($this->linkID);
	}
	function FreeResult($id="me")
	{
		@mysql_free_result($this->result[$id]);
	}
	function FreeResultAll()
	{
		if(!is_array($this->result)) return "";
		foreach($this->result as $kk => $vv){
			if($vv) @mysql_free_result($vv);
		}
	}
	function SetQuery($sql)
	{
		$prefix="#@__";
		$sql = str_replace($prefix,$this->dbPrefix,$sql);
		$this->queryString = $sql;
	    error_log(date("Ymd-H:i:s")."----[".$_COOKIE["VioomaUserID"]."]".$sql."\n", 3, dirname(__FILE__)."/../logs/sql".date("Ymd").".log");
	}
	function SetSql($sql)
	{
		$this->SetQuery($sql);
	}
	function DisplayError($msg)
	{
		echo "<html>\r\n";
		echo "<head>\r\n";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>\r\n";
		echo "<title>在庫管理　Error Track</title>\r\n";
		echo "</head>\r\n";
		echo "<body>\r\n<p style='line-helght:150%;font-size:10pt'>\r\n";
		echo $msg;
		echo "<br/><br/>";
		echo "</p>\r\n</body>\r\n";
		echo "</html>";
		//$this->Close();
		//exit();
	}


	function setAutocommit($b)
	{
	    if($b==true) {
	        mysql_query("set autocommit=1");
	    } else {
	        mysql_query("set autocommit=0");
	    }
	    
// 	   var_dump(mysql_fetch_array(mysql_query("select @@autocommit")));
	     
	}
	
	function commit()
	{
	    mysql_query("commit;");
	}
	
	function rollback()
	{
	    mysql_query("rollback;");
	}
}

function CopySQLPoint(&$ndsql)
{
	$GLOBALS['dsql'] = $ndsql;
}


?>