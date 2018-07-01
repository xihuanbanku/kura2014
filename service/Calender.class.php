<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set("PRC");
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "hbDays":
            $result = hbDays();
        break;
        case "hbThisMonth":
            $result = hbThisMonth();
        break;
        case "hbEvents":
            $result = hbEvents();
        break;
        case "writeCalender":
            $result = writeCalender();
        break;
        case "closeMe":
            $result = closeMe();
        break;
        default:
            return "error";
        break;
    }
    echo $result;  

    function hbDays() {
        $currentMonth = $_REQUEST["currentMonth"];
        $currentYear = $_REQUEST["currentYear"];
        $userID = $_REQUEST["userID"];
        
        $returnDays = "";
        $ksql = new ezSQL_mysql();
        $query = "select hbdays from jxc_calender a where a.delFlag = 0 and a.type = 1 and receiver = ".$userID;
        $results = $ksql->get_results($query);
        if($results) {
            foreach ($results as $result) {
                $set = $result->hbdays;
                $dayOfWeek = date('w',strtotime($currentYear."-".$currentMonth."-"."01"));
                $days = $dayOfWeek;
                if($set<$dayOfWeek) {
                    $days = 7+($set-$dayOfWeek);
                } else {
                    $days = $set-$dayOfWeek;
                }
                
                $now = strtotime($currentYear."-".$currentMonth."-"."01".$days." day");
                $temp = "01";
                for ($i=0; $i<5; $i++) {
                    $weeks = date('Y-m-d', strtotime("+".$i." week" ,$now));
                    $explodes = explode("-", $weeks);
                    $last = $explodes[2];
                    if($temp <= $last) {
                        $returnDays.=$last.",";
                        $temp = $last;
                    }
                }
            }
        }
        $query = "select hbdays from jxc_calender a where a.delFlag = 0 and a.type = 2 and receiver = ".$userID;
        $results = $ksql->get_results($query);
        if($results) {
            foreach ($results as $result) {
                $returnDays.=$result->hbdays.",";
            }
        }
        
        return $returnDays;
    }
    function hbThisMonth() {
        $currentMonth = $_REQUEST["currentMonth"];
        $currentYear = $_REQUEST["currentYear"];
        $userID = $_REQUEST["userID"];
        
        $returnDays = ",";
        $ksql = new ezSQL_mysql();
        $query = "select replace(hbdays, '".$currentMonth.",', '') hbdays from jxc_calender a where a.delFlag = 0 and a.type = 3 and hbdays like '%".$currentMonth.",%' and receiver = ".$userID;
        $results = $ksql->get_results($query);
        if($results) {
            foreach ($results as $result) {
                $returnDays.=$result->hbdays.",";
            }
        }
        
        return $returnDays;
    }
    
    function hbEvents() {
        $currentYear = $_REQUEST["currentYear"];
        $currentMonth = $_REQUEST["currentMonth"];
        $currentDay = $_REQUEST["currentDay"];
        $userID = $_REQUEST["userID"];
        
        $dayOfWeek = date('w',strtotime($currentYear."-".$currentMonth."-".$currentDay));
        $returnEvents = "";
        
        $ksql = new ezSQL_mysql();
        $query = "select * from jxc_calender a where a.delFlag = 0 and a.hbdays = {$dayOfWeek} and a.type = 1 and receiver = ".$userID."
                    union select * from jxc_calender a where a.delFlag = 0 and a.hbdays = {$currentDay} and a.type = 2 and receiver = ".$userID."
                    union select * from jxc_calender a where a.delFlag = 0 and a.hbdays = '{$currentMonth},{$currentDay}' and a.type = 3 and receiver = ".$userID;
        $results = $ksql->get_results($query);
        if($results) {
            for($i=0; $i<count($results); $i++) {
                $returnEvents.= "<div class=\"event\" >
                            		<h3>
                            			Event ".($i+1).". 
                            			<a style='float: right;' href='javascript:void(0);' onclick='closeMe(this, {$results[$i]->id})'>&nbsp;&nbsp;&nbsp;X</a>
                            			<small>{$currentYear}-{$currentMonth}-{$currentDay}</small>
                            		</h3>
                            		<p>{$results[$i]->content}</p>
                            	</div>";
            }
        }
        
        return $returnEvents;
    }
    
    function writeCalender() {
        $userID = $_REQUEST["userID"];
        $content = $_REQUEST["content"];
        $hbdays = $_REQUEST["weekDay"];
        $type = $_REQUEST["type"];
        $strChk = $_REQUEST["strChk"];
        
        if($type == 2) {
            $hbdays = $_REQUEST["monthDay"];
        } else if($type == 3) {
            $hbdays = $_REQUEST["thisMonth"].",".$_REQUEST["thisDay"];
        }
        $result=0;
        $newsql = new ezSQL_mysql();
        foreach ($strChk as $chk) {
            $result += $newsql->query("insert into jxc_calender(`hbdays`, `type`, `sender`, `receiver`, `content`)
                values('$hbdays', '$type', '$userID','$chk', '$content')") or mysql_error();
        }
        return $result;
    }
    
    function closeMe() {
        $userID = $_REQUEST["userID"];
        $id = $_REQUEST["id"];
        
        $newsql = new ezSQL_mysql();
        return $newsql->query("update jxc_calender set delFlag = 2 where id = {$id}");
    }
    
?>  