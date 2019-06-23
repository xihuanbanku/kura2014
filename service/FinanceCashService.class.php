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
        case "insert":
            $result = insert();
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
        case "initFinanceType":
            $result = initStatic("FINANCE_TYPE");
            break;
        case "initGoingType":
            $result = initStatic("GOING_TYPE");
        break;
        default:
            $result = "error";
        break;
    }
    echo $result;  
    
    /**
     * THE OTHER WAY 2017-11-09 18:27:52
                应该是 资金动向的2 等于（ 实时流水 该帐号 5的和）+资金动向 1
                
                资金动向 3 等于  （该帐号的 实时流水5的和）+（该帐号的 实时流水6的和）+资金动向 1
     * @param number $pageCount
     * @param number $pageIndex
     * @return unknown|string
     */
    function initPage($pageCount=100000, $pageIndex=1) {
        checkGoingTypeExists();
        if(isset($_REQUEST["pageCount"])) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if(isset($_REQUEST["pageIndex"])) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $query = "select x.p_name going_type_str, y.* from (select * from jxc_static where p_type='GOING_TYPE') x
                    left join (
				select c.id, c.aid, c.going_type, c.initial+sum(a.income-a.outgoing) s_real_rest,
                        c.initial+sum(a.income-a.outgoing)+sum(a.real_income+a.real_outgoing) s_rest_not_back, c.initial, c.remark1
                        from jxc_finance_cash c
                        left join jxc_finance_cashflow a
                        on a.going_type = c.going_type
                        and  IFNULL(a.del_flag,0)=0
                        where c.del_flag=0 ";
        if(!empty($_REQUEST["s_atime"])){
           $s_atime = $_REQUEST["s_atime"];
           $query .= " and a.atime >='{$s_atime}'";
        }
        if(!empty($_REQUEST["e_atime"])){
           $e_atime = $_REQUEST["e_atime"];
           $query .= " and a.atime <='{$e_atime}'";
        }
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (id like '%{$s_text}%'
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
        $query .= "   group by c.going_type ) y 
                on y.going_type = x.p_value ";
//         if (!empty($_REQUEST["orderBy"])) {
//             switch ($_REQUEST["orderBy"]) {
//                 case 1:
//                     $query .= "   order by  a.atime ";
//                 break;
//                 case 2:
//                     $query .= "   order by  a.atime desc ";
//                 break;
//                 case 3:
//                     $query .= "   order by  a.going_type ";
//                 break;
//                 case 4:
//                     $query .= "   order by  a.going_type desc";
//                 break;
//             }
//         } else {
//             $query .= "   order by  a.atime desc";
//         }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        if(!isset($_REQUEST["pageIndex"])) {
            return $results;
        }
		$total = initPageCount();
		return "{\"totalcount\":".$total.",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }
    

    function initPageCount() {
        $query = "select count(0) from jxc_static where p_type='GOING_TYPE'";
        if(!empty($_REQUEST["s_atime"])){
           $s_atime = $_REQUEST["s_atime"];
           $query .= " and atime >='{$s_atime}'";
        }
        if(!empty($_REQUEST["e_atime"])){
           $e_atime = $_REQUEST["e_atime"];
           $query .= " and atime <='{$e_atime}'";
        }
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (id like '%{$s_text}%'
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
     * 插入一行记录
     * @return boolean|number|mixed
     */
    function insert() {
        $atime        =$_REQUEST["atime"];
        $type         =$_REQUEST["type"];
        $remark1      =$_REQUEST["remark1"];
        $income       =$_REQUEST["income"];
        $outgoing     =$_REQUEST["outgoing"];
        $real_income  =$_REQUEST["real_income"];
        $real_outgoing=$_REQUEST["real_outgoing"];
        $limit_date   =$_REQUEST["limit_date"];
        $owner        =$_REQUEST["owner"];
        $finance_type     =$_REQUEST["finance_type"];
        $going_type   =$_REQUEST["going_type"];
        $real_rest    =$_REQUEST["real_rest"];
        $rest_not_back=$_REQUEST["rest_not_back"];
        
        $newsql = new ezSQL_mysql();
        $query = "INSERT INTO `jxc_finance_cashflow` (`atime`, `type`, `remark1`, `income`, `outgoing`, `real_income`, 
            `real_outgoing`, `limit_date`, `owner`, `finance_type`, `going_type`, `real_rest`, `rest_not_back`)
            values('{$atime}', '{$type}', '{$remark1}', '{$income}', '{$outgoing}', '{$real_income}', 
            '{$real_outgoing}', '{$limit_date}', '{$owner}', '{$finance_type}', '{$going_type}', '{$real_rest}', '{$rest_not_back}')";
        $result = $newsql->query($query);
        return $result;
    }
    /**
     * 删除一行记录
     * @return boolean|number|mixed
     */
    function delete() {
        $userID = $_REQUEST["id"];
        $query = "update jxc_finance_cash set del_flag = 1 where id = ".$userID;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    /**
     * 更新记录
     * @return boolean|number|mixed
     */
    function update() {
        $userID = $_REQUEST["userID"];
        $id = $_REQUEST["id"];

//         $initial         =$_REQUEST["initial"];
        $aid      =$_REQUEST["aid"];

        $newsql = new ezSQL_mysql();
        $query = "update jxc_finance_cash 
                set 
                aid          ='".$aid."'
                where id = ".$id."";
//         if($userID != 51) {
//             $query .= " and owner ={$userID}";
//         }
        return $newsql->query($query);
    }
    
    /**
     * 从static表读取内容
     */
    function initStatic($type) {
        $selectHtml="";
        $query = "select p_name, p_value from `jxc_static` where p_type = '".$type."'";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        foreach ($results as $result) {
            $selectHtml .= "<option value='{$result->p_value}'>{$result->p_name}</option>";
        }
        return $selectHtml;
    }
    
    /**
     * 从static表确认going_type已经齐全
     */
    function checkGoingTypeExists() {
        $query = "select p_value from `jxc_static` where p_type = 'GOING_TYPE' and p_value not in (select going_type from jxc_finance_cash where del_flag=0)";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        if($results) {
            foreach ($results as $result) {
                $query = "insert into jxc_finance_cash(going_type) values ('{$result->p_value}')";
                $newsql->query($query);
            }
        }
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
        $query = "update jxc_luggage
            set status = {$statusOptions}
                where l_id in ('" . implode("','", $kids) . "')";
        if($userID != 51) {
            $query .= " and owner ={$userID}";
        }
        return $newsql->query($query);
    }
    /**
     * 批量更新备注3
     * @return Ambigous <boolean, number, mixed>
     */
    function updateRemark3() {
        $userID = $_REQUEST["userID"];
        $kids = $_REQUEST["strChk"];
        $remark3Input = $_REQUEST["remark3Input"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_luggage
            set remark3 = '{$remark3Input}'
                where l_id in ('" . implode("','", $kids) . "')";
        if($userID != 51) {
            $query .= " and owner ={$userID}";
        }
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 批量删除
     * @return Ambigous <boolean, number, mixed>
     */
    function deleteChked() {
        $userID = $_REQUEST["userID"];
        $kids = $_REQUEST["strChk"];
        $remark3Input = $_REQUEST["remark3Input"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_luggage
            set del_flag = 1 
                where l_id in ('" . implode("','", $kids) . "')";
        if($userID != 51) {
            $query .= " and owner ={$userID}";
        }
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "资金动向";
		$headers = array('番号','资金帐号名称','初期金额','实际金额');
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
        $objWriter->save('php://output');
        exit;
    }
?>  