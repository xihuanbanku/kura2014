<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set('PRC');
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = initPage();
        break;
        case "insert":
            $result = insert();
            break;
        case "delete":
            $result = delete();
            break;
        case "update":
            $result = update();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function initPage() {
        $query = "select * from jxc_customer_want a  where del_flag=0  order by want_date desc";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
		return json_encode($results, JSON_FORCE_OBJECT);
    }
    function insert() {
        $cp_number=$_REQUEST["cp_number"];
        $want_content=$_REQUEST["want_content"];
        $want_date=$_REQUEST["want_date"];
        $remark1=$_REQUEST["remark1"];
        $remark2=$_REQUEST["remark2"];
        $remark3=$_REQUEST["remark3"];
        $remark4=$_REQUEST["remark4"];
        $remark5=$_REQUEST["remark5"];
        $is_bought = 0;
        $is_transfor = 0;
        $is_arrived = 0;
        $is_passed = 0;
        if(!empty($_REQUEST["is_bought"])) {
            $is_bought=$_REQUEST["is_bought"];
        }
        if(!empty($_REQUEST["is_transfor"])) {
            $is_transfor=$_REQUEST["is_transfor"];
        }
        if(!empty($_REQUEST["is_arrived"])) {
            $is_arrived=$_REQUEST["is_arrived"];
        }
        if(!empty($_REQUEST["is_passed"])) {
            $is_passed=$_REQUEST["is_passed"];
        }
        
        $newsql = new ezSQL_mysql();
        $query = "select max(REPLACE(cp_number, '{$cp_number}', '')+0) from jxc_customer_want where cp_number like '{$cp_number}%'";
        $result = $newsql->get_var($query);
        preg_match("/\d+/", $result, $number);
        if($number) {
            $next_id = intval($number[0])+1;
        } else {
            $next_id = 1;
        }
        
        $serialID = $cp_number.sprintf("%03d",$next_id);
        $query = "INSERT INTO `jxc_customer_want` (`cp_number`, `want_content`, `want_date`, `is_bought`, `is_transfor`, `is_arrived`, `is_passed`, `remark1`, `remark2`, `remark3`, `remark4`, `remark5`)
            values('$serialID','$want_content','$want_date','$is_bought','$is_transfor','$is_arrived','$is_passed','$remark1','$remark2','$remark3','$remark4','$remark5')";
        $result = $newsql->query($query) or mysql_error();
        return $result;
         
    }
    function delete() {
        $userID = $_REQUEST["id"];
        $query = "update jxc_customer_want set del_flag = 1 where id = ".$userID;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    function update() {
        $userID = $_REQUEST["userID"];
        $id = $_REQUEST["id"];
        $want_content = $_REQUEST["want_content"];
        $remark1 = $_REQUEST["remark1"];
        $remark2 = $_REQUEST["remark2"];
        $remark3 = $_REQUEST["remark3"];
        $remark4 = $_REQUEST["remark4"];
        $remark5 = $_REQUEST["remark5"];
        $is_bought = 0;
        $is_transfor = 0;
        $is_arrived = 0;
        $is_passed = 0;
        if(!empty($_REQUEST["is_bought"])) {
            $is_bought=$_REQUEST["is_bought"];
        }
        if(!empty($_REQUEST["is_transfor"])) {
            $is_transfor=$_REQUEST["is_transfor"];
        }
        if(!empty($_REQUEST["is_arrived"])) {
            $is_arrived=$_REQUEST["is_arrived"];
        }
        if(!empty($_REQUEST["is_passed"])) {
            $is_passed=$_REQUEST["is_passed"];
        }

        $newsql = new ezSQL_mysql();
        $query = "update jxc_customer_want 
            set is_bought = ".$is_bought.", 
                is_transfor = ".$is_transfor.", 
                is_arrived = ".$is_arrived.", 
                is_passed = ".$is_passed.", 
                want_content = '".$want_content."', 
                remark1 = '".$remark1."', 
                remark2 = '".$remark2."',
                remark3 = '".$remark3."',
                remark4 = '".$remark4."',
                remark5 = '".$remark5."'
                where id = ".$id;
        return $newsql->query($query);
    }
    
?>  