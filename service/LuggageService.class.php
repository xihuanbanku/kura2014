<?php
require_once '../PHPExcel.php';
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set('PRC');
session_start();
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = initPage();
        break;
        case "insert":
            $result = insert();
            break;
        case "delete":
            $result = delete_a();
            break;
        case "update":
            $result = update();
            break;
        case "exportExcel":
            exportExcel();
            break;
        case "updateStatus":
            $result = updateStatus();
            break;
        case "deleteChked":
            $result = deleteChked();
            break;
        case "updateRemark3":
            $result = updateRemark3();
            break;
        default:
            $result = "error";
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
        $query = "select  a.l_id, a.cust_name, a.destination, a.content, a.cp_number, case a.method when 1 then 'ヤマト運輸' when 2 then '佐川急便' when 3 then '日本郵便' when 4 then 'そのほか' end methodstr,
            case a.type when 1 then '発払い' when 2 then '着払い' when 3 then '代金引換'  end typestr, a.remark1, a.want_date, a.limit_date, a.query_num,
            case a.status when 0 then '未着' when 1 then '到着' when 2 then '済' when 3 then '未完成' end statusstr, a.is_arrival, b.s_name, c.s_name remark2Name, a.remark3, a.id as luggage_id ";
        if(isset($_REQUEST["pageIndex"])) {
            $query .= " , a.method, a.status, a.type, b.id owner, a.remark2 ";
        }
        $query .= " from jxc_luggage a, jxc_staff b , jxc_staff c
            where  a.owner= b.id and a.remark2= c.id 
            and del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (l_id like '%{$s_text}%'
                          or cust_name   like '%{$s_text}%'
                          or content     like '%{$s_text}%'
                          or cp_number     like '%{$s_text}%'
                          or destination     like '%{$s_text}%'
                          or is_arrival     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or remark3    like '%{$s_text}%'
                          or remark2     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.l_id in ('" . implode("','", $ids) . "')";
            }
        }
        if(!empty($_REQUEST["orderWantDate"])) {
            $query .= "   order by  want_date ".$_REQUEST["orderWantDate"];
        } else if(!empty($_REQUEST["orderStatus"])) {
            $query .= "   order by  status ".$_REQUEST["orderStatus"];
        } else if(!empty($_REQUEST["orderLimitDate"])) {
            $query .= "   order by  limit_date ".$_REQUEST["orderLimitDate"];
        } else if(!empty($_REQUEST["orderOwner"])) {
            $query .= "   order by  owner ".$_REQUEST["orderOwner"];
        } else {
            $query .= "   order by want_date desc";
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
        $query = "select count(0)  from jxc_luggage a, jxc_staff b 
            where  a.owner= b.id 
            and del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (l_id like '%{$s_text}%'
                          or cust_name   like '%{$s_text}%'
                          or content     like '%{$s_text}%'
                          or cp_number     like '%{$s_text}%'
                          or destination     like '%{$s_text}%'
                          or is_arrival     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or remark3    like '%{$s_text}%'
                          or remark2     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.l_id in ('" . implode("','", $ids) . "')";
            }
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }
    function insert() {
    //页面不再提交ID, 直接默认
        $l_id ="TSBACK";
        //转为半角形式, 并且转大写
//         $l_id = strtoupper(SBC_DBC($l_id, 1));
        $cust_name   =$_REQUEST["cust_name"];
        $destination     =$_REQUEST["destination"];
        $content    =$_REQUEST["content"];
        $cp_number    =$_REQUEST["cp_number"];
        $method =$_REQUEST["method"];
        $type        =$_REQUEST["type"];
        $remark1     =$_REQUEST["remark1"];
//        $want_date   =$_REQUEST["want_date"];
//        $limit_date   =$_REQUEST["limit_date"];
        $query_num   =$_REQUEST["query_num"];
        $status       =$_REQUEST["status"];
        $is_arrival    =$_REQUEST["is_arrival"];
        $remark2     =$_REQUEST["remark2"];
        $remark3     =$_REQUEST["remark3"];
        $owner       =$_REQUEST["owner"];
        
        $status = 0;
        if(!empty($_REQUEST["status"])) {
            $status=$_REQUEST["status"];
        }
        
        $newsql = new ezSQL_mysql();
        $query = "select max(REPLACE(l_id, '{$l_id}', '')+0) from jxc_luggage where l_id like '{$l_id}%'";
        $result = $newsql->get_var($query);
        preg_match("/\d+/", $result, $number);
        if($number) {
            $next_id = intval($number[0])+1;
        } else {
            $next_id = 1;
        }
        
        $serialID = $l_id.sprintf("%03d",$next_id);
        $query = "INSERT INTO `jxc_luggage` (`l_id`, `content`, `cp_number`, `remark3`, `query_num`, `owner`, `remark2`, `type`, `is_arrival`, `remark1`, `destination`, `cust_name`, `limit_date`, `status`, `method`)
            values('$serialID','$content','$cp_number','$remark3','$query_num','$owner','$remark2','$type','$is_arrival','$remark1','$destination','$cust_name', ADDDATE(now(),INTERVAL 3 day),'$status','$method')";
        $result = $newsql->query($query) or mysql_error();
        return $result;
    }
    function delete_a() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];
        $query = "update jxc_luggage set del_flag = 1 
                where id = ".$id."
                and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    function update() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];
        
//         $l_id   =$_REQUEST["l_id"];
        $cust_name   =$_REQUEST["cust_name"];
        $destination     =$_REQUEST["destination"];
        $content    =$_REQUEST["content"];
        $cp_number    =$_REQUEST["cp_number"];
        $method     =$_REQUEST["method"];
        $type        =$_REQUEST["type"];
        $remark1     =$_REQUEST["remark1"];
       // $want_date   =$_REQUEST["want_date"];
        $query_num         =$_REQUEST["query_num"];
        $status       =$_REQUEST["status"];
        $is_arrival    =$_REQUEST["is_arrival"];
        $remark2     =$_REQUEST["remark2"];
        $remark3     =$_REQUEST["remark3"];
        $owner       =$_REQUEST["owner"];

        $count = 0;
        $status = 0;
        if(!empty($_REQUEST["status"])) {
            $status=$_REQUEST["status"];
        }

        $newsql = new ezSQL_mysql();
        $query = "update jxc_luggage 
            set cust_name = '".$cust_name."',
                destination   = '".$destination    ."',
                content       = '".$content   ."',
                method        = '".$method   ."',
                type          = '".$type        ."',
                remark1       = '".$remark1     ."',
                query_num     = '".$query_num   ."',
                status        = '".$status       ."',
                is_arrival    = '".$is_arrival  ."',
                remark2       = '".$remark2     ."',
                remark3       = '".$remark3     ."',
                owner         = '".$owner     ."'
                where id = ".$id."
                and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        $count += $newsql->query($query);
        $query = "update jxc_luggage 
            set remark3    = '".$remark3    ."',
                cp_number     = '".$cp_number   ."'
                where id = ".$id;
        $count += $newsql->query($query);
        return $count;
    }
    /**
     * 批量更新状态
     * @return Ambigous <boolean, number, mixed>
     */
    function updateStatus() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $statusOptions = $_REQUEST["statusOptions"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_luggage
            set status = {$statusOptions}
                where l_id in ('" . implode("','", $kids) . "')
                and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        return $newsql->query($query);
    }
    /**
     * 批量更新备注3
     * @return Ambigous <boolean, number, mixed>
     */
    function updateRemark3() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $remark3Input = $_REQUEST["remark3Input"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_luggage
            set remark3 = '{$remark3Input}'
                where l_id in ('" . implode("','", $kids) . "')
                and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 批量删除
     * @return Ambigous <boolean, number, mixed>
     */
    function deleteChked() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $remark3Input = $_REQUEST["remark3Input"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_luggage
            set del_flag = 1 
                where l_id in ('" . implode("','", $kids) . "')
                and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "荷物・返品管理";
		$headers = array('管理番号','お客様名前','宛先','内容原因','商品番号','運送方法','荷物分類','備考1','受付日','到期日','問い合わせ番号','状态','到着先','記入者','返品処理','返品対策', 'ID');
        $results = initPage();
          
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
        
        // Add some data
        $workSheet = $objPHPExcel->setActiveSheetIndex(0);
    
        $COLUMNINDEXS = unserialize(COLUMNINDEXS);
        for ($i=0; $i<sizeof($headers); $i++) {
            $workSheet->setCellValue($COLUMNINDEXS[$i].'1', $headers[$i]);
        }
        
        $i = 2;
        if($results) {
            foreach ($results as $result) {
                $j = 0;
                foreach ($result as $key=> $value){
                    $workSheet->setCellValue($COLUMNINDEXS[$j++].$i, $value);
                }
                $i++;
            }
        }
     
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($title);
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $time = date("Ymd-H_i_s");
        header('Content-Disposition: attachment;filename="'.$title.$time.'.xlsx"');
        header('Cache-Control: max-age=0');
        
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
