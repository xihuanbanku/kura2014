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
            $result = initFinanceType();
            break;
        case "initStatic":
            $result = initStatic();
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
        $query = "select a.id, SUBSTR(atime, 1, 10) atime, a.income, a.outgoing,
            a.remark2, a.finance_type_1, a.finance_type_2, a.finance_type_3, a.remark2, a.dr_cr,
            b.name finance_type_1_str, c.name finance_type_2_str, d.name finance_type_3_str, a.picture_name
            from jxc_finance_drcrflow a, jxc_finance_type b, jxc_finance_type c, jxc_finance_type d
            where del_flag=0
              and a.finance_type_1 = b.id
              and a.finance_type_2 = c.id
              and a.finance_type_3 = d.id";
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
                          or remark2     like '%{$s_text}%')";
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
        if (!empty($_REQUEST["orderBy"])) {
            switch ($_REQUEST["orderBy"]) {
                case 1:
                    $query .= "   order by  atime ";
                break;
                case 2:
                    $query .= "   order by  atime desc ";
                break;
            }
        } else {
            $query .= "   order by  atime desc";
        }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        if(!isset($_REQUEST["pageIndex"])) {
            return $results;
        }
		$total = initPageCount();
		return "{\"totalcount\":".json_encode($total, JSON_FORCE_OBJECT).",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }
    

    function initPageCount() {
        $query = "select count(0) c, sum(income) s_income, sum(outgoing) s_outgoing
        from jxc_finance_drcrflow
            where  del_flag=0";
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
                          or remark2     like '%{$s_text}%')";
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
        return $newsql->get_row($query);
    }
    
    /**
     * 插入一行记录
     * @return boolean|number|mixed
     */
    function insert() {
        $picture_name        =$_REQUEST["picture_name"];
//         $remark1      =$_REQUEST["remark1"];
        $remark2      =$_REQUEST["remark2"];
        $income       =$_REQUEST["income"];
        $outgoing     =$_REQUEST["outgoing"];
//         $real_income  =$_REQUEST["real_income"];
//         $real_outgoing=$_REQUEST["real_outgoing"];
//         $limit_date   =$_REQUEST["limit_date"];
        $owner        =$_REQUEST["owner"];
        $finance_type_1     =$_REQUEST["finance_type_1"];
        $finance_type_2     =$_REQUEST["finance_type_2"];
        $finance_type_3     =$_REQUEST["finance_type_3"];
        $dr_cr   =$_REQUEST["dr_cr"];
        
        //实际结余 = 实际收入 -实际支出
//         $real_rest    =$income-$outgoing;
        //挂账结余=实际收入- 实际支出+(应收 -应付)
//         $rest_not_back=$real_rest+($real_income-$real_outgoing);
        
        $newsql = new ezSQL_mysql();
        $query = "INSERT INTO `jxc_finance_drcrflow` (`income`, `outgoing`, `owner`,
        `remark2`, `finance_type_1`, `finance_type_2`, `finance_type_3`, `dr_cr`, `picture_name`)
            values('{$income}', '{$outgoing}', '{$owner}', '{$remark2}', '{$finance_type_1}', '{$finance_type_2}', '{$finance_type_3}',
            '{$dr_cr}', '{$picture_name}')";
        return $newsql->query($query);
    }
    /**
     * 删除一行记录
     * @return boolean|number|mixed
     */
    function delete() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];
        $query = "update jxc_finance_drcrflow set del_flag = 1 where id = ".$id."
                and (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0)";
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

        $picture_name        =$_REQUEST["picture_name"];
        $remark2      =$_REQUEST["remark2"];
        $income       =$_REQUEST["income"];
        $outgoing     =$_REQUEST["outgoing"];
//         $real_income  =$_REQUEST["real_income"];
//         $real_outgoing=$_REQUEST["real_outgoing"];
//         $limit_date   =$_REQUEST["limit_date"];
//         $owner        =$_REQUEST["owner"];
        $finance_type_1     =$_REQUEST["finance_type_1"];
        $finance_type_2     =$_REQUEST["finance_type_2"];
        $finance_type_3     =$_REQUEST["finance_type_3"];
        $dr_cr   =$_REQUEST["dr_cr"];

        $newsql = new ezSQL_mysql();
        $query = "update jxc_finance_drcrflow 
                set 
                remark2       ='".$remark2."',
                income        ='".$income."',
                outgoing      ='".$outgoing."',
                finance_type_1      ='".$finance_type_1."',
                finance_type_2      ='".$finance_type_2."',
                finance_type_3      ='".$finance_type_3."',
                dr_cr    ='".$dr_cr."',
                picture_name ='".$picture_name."'
                where id = ".$id."";
//         if($userID != 51) {
//             $query .= " and owner ={$userID}";
//         }
        return $newsql->query($query);
    }
    
    /**
     * 初始化财务类型选项
     */
    function initFinanceType() {
        $p_id  =$_REQUEST["p_id"];
        $selectHtml="<option value='1'>请选择</option>";
        $query = "select id, name from `jxc_finance_type` where p_id = '".$p_id."'";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        foreach ($results as $result) {
            $selectHtml .= "<option value='{$result->id}'>{$result->name}</option>";
        }
        return $selectHtml;
    }
    
    /**
     * 从static表读取内容
     */
    function initStatic() {
        $type  =$_REQUEST["p_type"];
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
        $userID = $_REQUEST["userID"];
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
        $title = "实时流水";
		$headers = array('管理番号','日期','实际收入','实际支出','会計要素','科目','科目明細','款项用途说明');
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