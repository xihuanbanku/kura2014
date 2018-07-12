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
        case "initPage4Admin":
            $result = initPage4Admin();
        break;
        case "update":
            $result = update();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    /**
     * 查看薪资
     */
    function initPage() {
        $user = $_COOKIE['userID'];
        $dutyYear = $_REQUEST["dutyYear"];
        $dutyMonth = $_REQUEST["dutyMonth"];

        $query = "select p_value, p_name, p_type from jxc_salary where salary_date = '{$dutyYear}{$dutyMonth}' and del_flag=0 and user_id={$user} order by p_type, sort ";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
		return json_encode($results, JSON_FORCE_OBJECT);
    }
    
    /**
     * 管理员初始化页面
     * @return string
     */
    function initPage4Admin() {
        $user = $_REQUEST['user'];
        $dutyYear = $_REQUEST["dutyYear"];
        $dutyMonth = $_REQUEST["dutyMonth"];

        $query = "select id, sort, p_value, p_name, p_type, p_func, mod_value, user_id from jxc_salary where salary_date = '{$dutyYear}{$dutyMonth}' and del_flag=0 and user_id={$user} order by p_type, sort ";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
		return json_encode($results, JSON_FORCE_OBJECT);
    }
    
    /**
     * 更新记录
     * @return boolean|number|mixed
     */
    function update() {
        $id = $_REQUEST["id"];
        $user = $_REQUEST["user"];
        $mod_value     =$_REQUEST["mod_value"];
        
        $newsql = new ezSQL_mysql();
        $query = "update jxc_salary
                set
                mod_value    ='".$mod_value."'
                where id = ".$id;
        return $newsql->query($query);
    }
    
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "实时流水";
		$headers = array('ID','日期','截止修改日期','科目','摘要','实际收入','实际支出','应收','应付',
		    '应收付日期','記入者ID','款项用途说明','会計要素ID','科目ID','科目明細ID','资金动向ID','实际结余',
		    '挂账结余','会計要素','科目','科目明細','资金动向','記入者','領収書','审核状态','发生日期');
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