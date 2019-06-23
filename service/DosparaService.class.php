<?php
require_once '../PHPExcel.php';
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set('PRC');
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = initPage();
        break;
        case "initPage2":
            $result = initPage2();
        break;
        case "insert":
            $result = insert();
            break;
        case "insert2":
            $result = insert2();
            break;
        case "delete":
            $result = delete();
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
        
        $query = "select a.id, a.d_id, a.destination, a.pc_type, a.parts_type, a.date1, a.date2, case a.status when 1 then '(中国から)発送済' when 2 then '未発送' when 3 then '遅延' end statusstr,
            a.date3, a.date4, a.want_price, a.date5, a.date6, a.remark1";
        if(isset($_REQUEST["pageIndex"])) {
            $query .= " ,a.status ";
        }
        $query .= " from jxc_dospara a
            where del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (d_id like '%{$s_text}%'
                          or destination   like '%{$s_text}%'
                          or pc_type     like '%{$s_text}%'
                          or parts_type     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        }
        if(!empty($_REQUEST["orderBy"])) {
            switch ($_REQUEST["orderBy"]) {
                case "1":
                $query .= "   order by date1 ";
                break;
                case "2":
                $query .= "   order by date1 desc";
                break;
                case "3":
                $query .= "   order by date2 ";
                break;
                case "4":
                $query .= "   order by date2 desc";
                break;
                case "5":
                $query .= "   order by date3 ";
                break;
                case "6":
                $query .= "   order by date3 desc";
                break;
                case "7":
                $query .= "   order by date4 ";
                break;
                case "8":
                $query .= "   order by date4 desc";
                break;
                case "9":
                $query .= "   order by date5 ";
                break;
                case "10":
                $query .= "   order by date5 desc";
                break;
                case "11":
                $query .= "   order by date6 ";
                break;
                case "12":
                $query .= "   order by date6 desc";
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
    
    /**
     * 分页
     * @return NULL
     */
    function initPageCount() {
        $query = "select count(0)  from jxc_dospara a
            where  del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (d_id like '%{$s_text}%'
                          or destination   like '%{$s_text}%'
                          or pc_type     like '%{$s_text}%'
                          or parts_type     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }
    
    /**
     * 初始化页面2
     * @param number $pageCount
     * @param number $pageIndex
     * @return multitype:|string
     */
    function initPage2($pageCount=100000, $pageIndex=1) {
        if(isset($_REQUEST["pageCount"])) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if(isset($_REQUEST["pageIndex"])) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        
        $query = "select d_id, shop_name, a_pc_type, a_part_number, a_parts_type, a_order_number, a_lcd_detail, a_photo1, a_photo2, a_photo3, a_photo4, a_photo5, a_contactor";
        $query .= " from jxc_dospara_2 a
            where del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (d_id like '%{$s_text}%'
                          or destination   like '%{$s_text}%'
                          or pc_type     like '%{$s_text}%'
                          or parts_type     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        }
        if(!empty($_REQUEST["orderBy"])) {
            switch ($_REQUEST["orderBy"]) {
                case "1":
                $query .= "   order by date1 ";
                break;
                case "2":
                $query .= "   order by date1 desc";
                break;
                case "3":
                $query .= "   order by date2 ";
                break;
                case "4":
                $query .= "   order by date2 desc";
                break;
                case "5":
                $query .= "   order by date3 ";
                break;
                case "6":
                $query .= "   order by date3 desc";
                break;
                case "7":
                $query .= "   order by date4 ";
                break;
                case "8":
                $query .= "   order by date4 desc";
                break;
                case "9":
                $query .= "   order by date5 ";
                break;
                case "10":
                $query .= "   order by date5 desc";
                break;
                case "11":
                $query .= "   order by date6 ";
                break;
                case "12":
                $query .= "   order by date6 desc";
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
    

    /**
     * 
     * 初始化分页2
     * @return NULL
     */
    function initPageCount2() {
        $query = "select count(0)  from jxc_dospara_2 a
            where  del_flag=0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (d_id like '%{$s_text}%'
                          or destination   like '%{$s_text}%'
                          or pc_type     like '%{$s_text}%'
                          or parts_type     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }
    
    /**
     * 新增记录
     * @return Ambigous <boolean, number, mixed>
     */
    function insert() {
        $d_id   =$_REQUEST["d_id"];
        $destination   =$_REQUEST["destination"];
        $pc_type   =$_REQUEST["pc_type"];
        $parts_type   =$_REQUEST["parts_type"];
        $date1   =$_REQUEST["date1"];
        $date2   =$_REQUEST["date2"];
        $status   =$_REQUEST["status"];
        $date3   =$_REQUEST["date3"];
        $date4   =$_REQUEST["date4"];
        $want_price   =$_REQUEST["want_price"];
        $date5   =$_REQUEST["date5"];
        $date6   =$_REQUEST["date6"];
        $remark1   =$_REQUEST["remark1"];
        
        $owner       =$_REQUEST["userID"];
        
        $newsql = new ezSQL_mysql();
        
        $query = "INSERT INTO `jxc_dospara` (`d_id`, `destination`, `pc_type`, `parts_type`, `date1`, `date2`, `status`, `date3`, `date4`, `want_price`, `date5`, `date6`, `remark1`, `owner`)
        VALUES ('$d_id','$destination','$pc_type','$parts_type','$date1','$date2','$status','$date3','$date4','$want_price','$date5','$date6','$remark1','$owner')";
        $result = $newsql->query($query) ;
        return $result;
    }
    
    /**
     * 
     * 新增记录2
     * @return Ambigous <boolean, number, mixed>
     */
    function insert2() {
        $d_id   =$_REQUEST["d_id"];
        $shop_name   =$_REQUEST["shop_name"];
        $a_pc_type   =$_REQUEST["a_pc_type"];
        $a_part_number   =$_REQUEST["a_part_number"];
        $a_parts_type   =$_REQUEST["a_parts_type"];
        $a_order_number   =$_REQUEST["a_order_number"];
        $a_lcd_detail   =$_REQUEST["a_lcd_detail"];
        $a_photo1   =$_REQUEST["a_photo1"];
        $a_photo2   =$_REQUEST["a_photo2"];
        $a_photo3   =$_REQUEST["a_photo3"];
        $a_photo4   =$_REQUEST["a_photo4"];
        $a_photo5   =$_REQUEST["a_photo5"];
        $a_contactor   =$_REQUEST["a_contactor"];
        $a_want_date   =$_REQUEST["a_want_date"];
        $a_status   =$_REQUEST["a_status"];
        $a_remark1   =$_REQUEST["a_remark1"];
        $b_estimate_amount   =$_REQUEST["b_estimate_amount"];
        $b_want_date   =$_REQUEST["b_want_date"];
        $b_delivery_date   =$_REQUEST["b_delivery_date"];
        $b_reply_date   =$_REQUEST["b_reply_date"];
        $b_expire_date   =$_REQUEST["b_expire_date"];
        $b_price   =$_REQUEST["b_price"];
        $b_deadline   =$_REQUEST["b_deadline"];
        $b_shenzhen_send_date   =$_REQUEST["b_shenzhen_send_date"];
        $b_delayed   =$_REQUEST["b_delayed"];
        $b_delayed_send_date   =$_REQUEST["b_delayed_send_date"];
        $b_arrival_date   =$_REQUEST["b_arrival_date"];
        $b_send_date   =$_REQUEST["b_send_date"];
        $b_track_number   =$_REQUEST["b_track_number"];
        $b_status   =$_REQUEST["b_status"];
        $b_p_number   =$_REQUEST["b_p_number"];
        $b_photo1   =$_REQUEST["b_photo1"];
        $b_photo2   =$_REQUEST["b_photo2"];
        $b_photo3   =$_REQUEST["b_photo3"];
        $b_remark1   =$_REQUEST["b_remark1"];
        $b_last_date   =$_REQUEST["b_last_date"];
        
        $owner       =$_REQUEST["userID"];
        
        $newsql = new ezSQL_mysql();
        
        $query = "INSERT INTO `ynomnc`.`jxc_dospara_2` (`d_id`, `shop_name`, `a_pc_type`, `a_part_number`, `a_parts_type`, `a_order_number`, `a_lcd_detail`, `a_photo1`, `a_photo2`, `a_photo3`, `a_photo4`, `a_photo5`, `a_contactor`, `a_want_date`, `a_status`, `a_remark1`, `b_estimate_amount`, `b_want_date`, `b_delivery_date`, `b_reply_date`, `b_expire_date`, `b_price`, `b_deadline`, `b_shenzhen_send_date`, `b_delayed`, `b_delayed_send_date`, `b_arrival_date`, `b_send_date`, `b_track_number`, `b_status`, `b_p_number`, `b_photo1`, `b_photo2`, `b_photo3`, `b_remark1`, `b_last_date`, `owner`)
        VALUES ('$d_id','$shop_name','$a_pc_type','$a_part_number','$a_parts_type','$a_order_number','$a_lcd_detail','$a_photo1','$a_photo2','$a_photo3','$a_photo4','$a_photo5','$a_contactor','$a_want_date','$a_status','$a_remark1','$b_estimate_amount','$b_want_date','$b_delivery_date','$b_reply_date','$b_expire_date','$b_price','$b_deadline','$b_shenzhen_send_date','$b_delayed','$b_delayed_send_date','$b_arrival_date','$b_send_date','$b_track_number','$b_status','$b_p_number','$b_photo1','$b_photo2','$b_photo3','$b_remark1','$b_last_date','$owner')";
        $result = $newsql->query($query) ;
        return $result;
    }
    function delete() {
        $userID = $_REQUEST["id"];
        $query = "update jxc_dospara set del_flag = 1 where id = ".$userID;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    function update() {
        $userID = $_REQUEST["userID"];
        $id = $_REQUEST["id"];
        
//         $d_id   =$_REQUEST["d_id"];
        $destination   =$_REQUEST["destination"];
        $pc_type   =$_REQUEST["pc_type"];
        $parts_type   =$_REQUEST["parts_type"];
        $date1   =$_REQUEST["date1"];
        $date2   =$_REQUEST["date2"];
        $status   =$_REQUEST["status"];
        $date3   =$_REQUEST["date3"];
        $date4   =$_REQUEST["date4"];
        $want_price   =$_REQUEST["want_price"];
        $date5   =$_REQUEST["date5"];
        $date6   =$_REQUEST["date6"];
        $remark1   =$_REQUEST["remark1"];

        $newsql = new ezSQL_mysql();
        $query = "update jxc_dospara 
            set destination   = '".$destination."',
                pc_type   = '".$pc_type."',
                parts_type   = '".$parts_type."',
                date1   = '".$date1."',
                date2   = '".$date2."',
                status   = '".$status."',
                date3   = '".$date3."',
                date4   = '".$date4."',
                want_price   = '".$want_price."',
                date5   = '".$date5."',
                date6   = '".$date6."',
                remark1   = '".$remark1."'
                where id = ".$id."";
//         if($userID != 51) {
//             $query .= " and owner ={$userID}";
//         }
//         $newsql->query($query);
//         $query = "update jxc_dospara 
//             set remark3    = '".$remark3    ."'
//                 where id = ".$id;
        return $newsql->query($query);
    }
    /**
     * 批量更新状态
     * @return Ambigous <boolean, number, mixed>
     */
    function updateStatus() {
        $userID = $_REQUEST["userID"];
        $kids = $_REQUEST["strChk"];
        $statusOptions = $_REQUEST["statusOptions"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_dospara
            set status = {$statusOptions}
                where id in ('" . implode("','", $kids) . "')";
//         if($userID != 51) {
//             $query .= " and owner ={$userID}";
//         }
        return $newsql->query($query);
    }
	
    /**
     * 批量删除
     * @return Ambigous <boolean, number, mixed>
     */
    function deleteChked() {
        $userID = $_REQUEST["userID"];
        $kids = $_REQUEST["strChk"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_dospara
            set del_flag = 1 
                where id in ('" . implode("','", $kids) . "')";
//         if($userID != 51) {
//             $query .= " and owner ={$userID}";
//         }
        return $newsql->query($query);
    }
	
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "ドスパラ仕入状況管理";
		$headers = array('ID','管理番号','発送先','パソコン型番','パーツ型番','見積り回答日','請求予定月','仕入状況','中国からの発送日','日本到着予定日','見積り金額','発送期日','最終対応日','備考');
        $results = initPage();
          
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('PRC');
        
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
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }
?>  