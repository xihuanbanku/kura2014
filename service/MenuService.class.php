<?php
require_once dirname(__FILE__)."/../include/config_passport.php";
require_once dirname(__FILE__)."/../include/config.php";
require_once dirname(__FILE__)."/../include/config_base.php";
require_once dirname(__FILE__)."/../include/inc_functions.php";
define("COMMA", ",");
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initTop":
            $result = initTop();
        break;
        case "initMenu":
            $result = initMenu();
        break;
        case "initUser":
            $result = initUser();
        break;
        case "initGrant":
            $result = initGrant();
        break;
        case "saveGrant":
            $result = saveGrant();
        break;
        case "initButton":
            $result = initButton();
        break;
        case "initAlarmCount":
            $result = initAlarmCount();
        break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function initTop() {
        $userID = $_REQUEST["userID"];
        $result= array();
        $ksql = new Dedesql(false);
        $query = "select * from #@__menu where reid=0 and LOCATE('|".$userID."|',rank)>=1 and type = 1 order by sort";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $result[] = $row;
        }
        $ksql->close();
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    
    function initMenu() {
        $userID = $_REQUEST["userID"];
        $c = $_REQUEST["c"];
        $ksql = new Dedesql(false);
        $query = "select * from #@__menu where reid='$c' and LOCATE('|".$userID."|',rank)>=1 and type = 1 order by sort";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $result[] = $row;
        }
        $ksql->close();
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    
    function initAlarmCount() {
        $ksql = new Dedesql(false);
        $query = "select count(0) c from #@__mainkc where number<=5";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $result[] = $row;
        }
        $ksql->close();
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    
    function initUser() {
        $ksql = new Dedesql(false);
        $query = "select id, s_no, s_name from #@__staff order by id";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $result[] = $row;
        }
        $ksql->close();
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    function initGrant() {
        $user = $_REQUEST["user"];
        $ksql = new Dedesql(false);
        $query = "SELECT a.id aid, a.name aname, LOCATE('|".$user."|',a.rank) aloc, a.type atype,
            b.id bid, b.name bname, b.url, LOCATE('|".$user."|',b.rank) bloc, b.type btype
                FROM `#@__menu`a join #@__menu b on a.id = b.reid order by a.id, b.type, b.sort";
        $ksql->setquery($query);
        $ksql->execute();
        $result = array();
        $key="";
        $value = array();
        $temp="";
        $i=0;
        while ($row = $ksql->GetAssoc()) {
            $key = $row["aid"]."#".$row["aname"]."#".$row["aloc"]."#".$row["atype"];
            if($key != $temp && $i>0) {
                $result[$temp]=$value;
                $value = array();
                $temp = $key;
            }
            if($i ==0){
                $temp = $key;
            }
            array_push($value, $row);
            $i++;
        }
        $result[$key]=$value;
        $ksql->close();
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    function initButton() {
        $user = $_REQUEST["user"];
        $reid = $_REQUEST["reid"];
        $ksql = new Dedesql(false);
        $query = "select id, name, url, LOCATE('|".$user."|',rank) loc from #@__menu where reid = ".$reid." and type = 2 order by id";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $result[] = $row;
        }
        $ksql->close();
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    function saveGrant() {
        $r = $_REQUEST["r"];
        $user = $_REQUEST["user"];
        $ksql = new Dedesql(false);
        $ids = implode("','", $r);
        $query = "update #@__menu set rank = CONCAT(REPLACE(rank, '|".$user."|', '|'), '".$user."|') where id in ('".$ids."')";
        $i=$ksql->ExecuteNoneQuery2($query);
        $query = "update #@__menu set rank = REPLACE(rank, '|".$user."|', '|') where id not in ('".$ids."')";
        $i+=$ksql->ExecuteNoneQuery2($query);
        $ksql->close();
		return $i;
    }
?>  