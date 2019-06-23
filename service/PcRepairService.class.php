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
    
    function initPage($pageCount=100000, $pageIndex=1) {
        if(isset($_REQUEST["pageCount"])) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if(isset($_REQUEST["pageIndex"])) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $query = "select  *, a.id as pc_id, b.id as l_id from jxc_pc_repair a, jxc_lab b , jxc_staff c 
            where a.pc_store = b.id 
            and a.owner= c.id 
            and del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (pc_number like '%.$s_text.%'
                          or cust_name   like '%{$s_text}%'
                          or problem     like '%{$s_text}%'
                          or pc_type     like '%{$s_text}%'
                          or accessories like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or progress    like '%{$s_text}%'
                          or eta         like '%{$s_text}%'
                          or phone       like '%{$s_text}%'
                          or status      like '%{$s_text}%'
                          or pc_store    like '%{$s_text}%'
                          or remark3    like '%{$s_text}%'
                          or remark2     like '%{$s_text}%')";
        }
        $sort = $_REQUEST["orderBy"];
        if($sort){
            switch ($sort) {
                case 1:
                    $query .= " order by want_date";
                    break;
                case 2:
                    $query .= " order by want_date desc";
                    break;
                case 3:
                    $query .= " order by status";
                    break;
                case 4:
                    $query .= " order by status desc";
                    break;
                case 5:
                    $query .= " order by pc_number";
                    break;
                case 6:
                    $query .= " order by pc_number desc";
                    break;
            }
        }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        if(!isset($_REQUEST["pageIndex"])) {
            return $results;
        }
		$total = initPageCount();
		$totaljson = "[{\"totalcount\":\"".$total."\"}]";
		return "{\"totalproperty\":".$totaljson.",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }

    function initPageCount() {
        $query = "select count(0)  from jxc_pc_repair a, jxc_lab b , jxc_staff c 
            where a.pc_store = b.id 
            and a.owner= c.id 
            and del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (pc_number like '%.$s_text.%'
                          or cust_name   like '%{$s_text}%'
                          or problem     like '%{$s_text}%'
                          or pc_type     like '%{$s_text}%'
                          or accessories like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or progress    like '%{$s_text}%'
                          or eta         like '%{$s_text}%'
                          or phone       like '%{$s_text}%'
                          or status      like '%{$s_text}%'
                          or pc_store    like '%{$s_text}%'
                          or remark3    like '%{$s_text}%'
                          or remark2     like '%{$s_text}%')";
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }
    function insert() {
        $pc_number   =$_REQUEST["pc_number"];
        $cust_name   =$_REQUEST["cust_name"];
        $problem     =$_REQUEST["problem"];
        $pc_type     =$_REQUEST["pc_type"];
        $accessories =$_REQUEST["accessories"];
        $remark1     =$_REQUEST["remark1"];
        $want_date   =$_REQUEST["want_date"];
        $progress    =$_REQUEST["progress"];
        $eta         =$_REQUEST["eta"];
        $phone       =$_REQUEST["phone"];
        $pc_store    =$_REQUEST["pc_store"];
        $remark2     =$_REQUEST["remark2"];
        $remark2     =$_REQUEST["remark3"];
        $owner       =$_REQUEST["owner"];
        
        $status = 0;
        if(!empty($_REQUEST["status"])) {
            $status=$_REQUEST["status"];
        }
        
        $newsql = new ezSQL_mysql();
        $query = "select max(REPLACE(pc_number, '{$pc_number}', '')+0) from jxc_pc_repair where pc_number like '{$pc_number}%'";
        $result = $newsql->get_var($query);
        preg_match("/\d+/", $result, $number);
        if($number) {
            $next_id = intval($number[0])+1;
        } else {
            $next_id = 1;
        }
        
        $serialID = $pc_number.sprintf("%03d",$next_id);
        $query = "INSERT INTO `pmon_kura`.`jxc_pc_repair` (`pc_number`, `cust_name`, `problem`, `pc_type`, `accessories`, `remark1`, `want_date`, `progress`, `eta`, `phone`, `status`, `pc_store`, `remark2`, `owner`, `remark3`)
            values('$serialID','$cust_name','$problem','$pc_type','$accessories','$remark1','$want_date','$progress','$eta','$phone','$status','$pc_store','$remark2','$owner','$remark3')";
        $result = $newsql->query($query) or mysql_error();
        return $result;
         
    }
    function delete() {
        $userID = $_REQUEST["id"];
        $query = "update jxc_pc_repair set del_flag = 1 where id = ".$userID;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    function update() {
        $userID = $_REQUEST["userID"];
        $id = $_REQUEST["id"];
        $pc_number   =$_REQUEST["pc_number"];
        $cust_name   =$_REQUEST["cust_name"];
        $problem     =$_REQUEST["problem"];
        $pc_type     =$_REQUEST["pc_type"];
        $accessories =$_REQUEST["accessories"];
        $remark1     =$_REQUEST["remark1"];
//         $want_date   =$_REQUEST["want_date"];
        $progress    =$_REQUEST["progress"];
        $eta         =$_REQUEST["eta"];
        $phone       =$_REQUEST["phone"];
        $pc_store    =$_REQUEST["pc_store"];
        $remark2     =$_REQUEST["remark2"];
        $remark3     =$_REQUEST["remark3"];
        $owner       =$_REQUEST["owner"];
        
        $status = 0;
        if(!empty($_REQUEST["status"])) {
            $status=$_REQUEST["status"];
        }

        $newsql = new ezSQL_mysql();
        $query = "update jxc_pc_repair 
            set pc_number = '".$pc_number."',
                cust_name = '".$cust_name."',
                problem     = '".$problem    ."',
                pc_type     = '".$pc_type    ."',
                accessories = '".$accessories."',
                remark1     = '".$remark1    ."',
                progress    = '".$progress   ."',
                eta         = '".$eta        ."',
                phone       = '".$phone      ."',
                status      = '".$status      ."',
                pc_store    = '".$pc_store   ."',
                remark2     = '".$remark2    ."',
                owner     = '".$owner    ."'
                where id = ".$id." and owner = ".$userID;
        $newsql->query($query);
        $query = "update jxc_pc_repair 
            set remark3    = '".$remark3    ."'
                where id = ".$id;
        return $newsql->query($query);
    }
    
?>  