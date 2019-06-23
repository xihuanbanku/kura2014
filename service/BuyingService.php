<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
require_once '../PHPExcel.php';
date_default_timezone_set('PRC');
$buyingService = new BuyingService();
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = $buyingService->initPage();
        break;
        case "passChecked":
            $result = $buyingService->passChecked();
            break;
        case "deleteChecked":
            $result = $buyingService->deleteChecked();
            break;
        case "transfer2Other":
            $result = $buyingService->transfer2Other();
            break;
        case "updateRemark1":
            $result = $buyingService->updateRemark1();
            break;
        case "updateColNumber":
            $result = $buyingService->updateColNumber();
            break;
        case "update_buying":
            $result = $buyingService->update_buying();
            break;
        case "update_lab":
            $result = $buyingService->update_lab();
            break;
        case "exportExcel":
            $result = $buyingService->exportExcel();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  

class BuyingService {
    function initPage($pageCount=100000, $pageIndex=1) {
        if(isset($_REQUEST["pageCount"])) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if(isset($_REQUEST["pageIndex"])) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $query = "select a.kid,
                        b.cp_number,
                        b.cp_title,
                        concat(b.cp_gg, '\n', b.cp_detail) cp_gg,
                        a.l_id,
                        concat(a.l_floor,'-', a.l_shelf,'-', a.l_zone,'-', a.l_horizontal,'-', a.l_vertical) position,
                        a.number,
                        a.col1,
                        a.col2,
                        a.col3,
                        a.col4,
                        a.col5,
                        a.col6,
                        a.l_state9,
                        a.on_board_date,
                        case when a.col1+a.col4 >0 then '未审核' else '已审核' end check_flag,
                        a.remark1,
                        a.arrival_luggage,
                        a.remark2 from jxc_mainkc a, jxc_basic b
            where a.p_id = b.cp_number 
              and a.del_flag = 0";
        if(!empty($_REQUEST["s_text"])){
           $s_text = trim($_REQUEST["s_text"]);
           $query .= " and (cp_number like '%{$s_text}%'
                          or cp_name   like '%{$s_text}%'
                          or cp_gg     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.kid in ('" . implode("','", $ids) . "')";
            }
        }
        if(isset($_REQUEST["pageNameId"])) {
            $pageNameId = $_REQUEST["pageNameId"];
            if($pageNameId != "" && $pageNameId != "-1") {
                $query .= " and a.page_name_id = ".$pageNameId;
            }
        }
        if(!empty($_REQUEST["orderByField"])) {
            $query .= "   ".$_REQUEST["orderByField"];
        }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        if(!isset($_REQUEST["pageIndex"])) {
            return $results;
        }
		$total = $this->initPageCount();
		$totaljson = "[{\"totalcount\":\"".$total."\"}]";
		return "{\"totalproperty\":".$totaljson.",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }

    function initPageCount() {
        $query = "select count(0) from jxc_mainkc a, jxc_basic b
            where a.p_id = b.cp_number 
              and a.del_flag = 0";
        if(!empty($_REQUEST["s_text"])){
            $s_text = $_REQUEST["s_text"];
            $query .= " and (cp_number like  '%{$s_text}%'
            or cp_name   like '%{$s_text}%'
            or cp_gg     like '%{$s_text}%')";
        }
        if(isset($_REQUEST["pageNameId"])) {
            $pageNameId = $_REQUEST["pageNameId"];
            if($pageNameId != "") {
                $query .= " and a.page_name_id = ".$pageNameId;
            }
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }

    /**
     * 上传文件
     * @param int $type
     * @param  $file
     * @param  $filetempname
     * @return multitype:number string
     */
    function uploadFile($type, $file, $filetempname) {
        $importStat=array("n"=>0,"u"=>0,"d"=>0,"e"=>0,"m"=>0,"filename"=>0);
        // 自己设置的上传文件存放路径
        $filePath = '/home/p-mon/tousho.co.jp/public_html/kura2014/upload/';
        $str = "";
        // 下面的路径按照你PHPExcel的路径来修改
        set_include_path('/home/p-mon/tousho.co.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());
    
        $filename = explode(".", $file); // 把上传的文件名以“.”好为准做一个数组。
        $time = date("Ymd-H_i_s"); // 去当前上传的时间
        $filename[0] .= $time; // 取文件名t替换
        $name = implode(".", $filename); // 上传后的文件名
        $uploadfile = $filePath . $name; // 上传后的文件名地址
        // move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
        $result = move_uploaded_file($filetempname, $uploadfile); // 假如上传到当前目录下
        if ($result) {// 如果上传文件成功，就执行导入excel操作
            if($filename[1]=="xlsx") {
                $objReader = PHPExcel_IOFactory::createReader('Excel2007'); // use excel2007 for 2007 format
            } else {
                $objReader = PHPExcel_IOFactory::createReader('Excel5'); // use excel2007 for 2007 format
            }
            $objPHPExcel = $objReader->load($uploadfile);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()); // 取得总列数
			if($highestColumn !=  19) {
				echo date("Ymd-H:i:s")."----出现错误, 文件内容不正确";
				die();
			}
            $nsql= new ezSQL_mysql();
            // 循环读取excel文件,读取一条,插入一条
            for ($j = 2; $j <= $highestRow; $j ++) {
                $strs = array();
                for ($k = 0; $k < $highestColumn; $k ++) {
                    $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                    $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName$j")->getValue();
                    //                 echo "$columnName$j"."----".$cellValue."----";
                    array_push($strs, $cellValue); // 读取单元格
                }
                        //类型是1=进货上传
                if($type ==1) {
                    switch ($strs[0]) {
                        case "u":
                            $notesql="update `jxc_mainkc` set col1 = ".(trim($strs[7]) == '' ? 0 : trim($strs[7])).",
                                     col2 = ".(trim($strs[8]) == '' ? 0 : trim($strs[8])).", 
                                     col3 = ".(trim($strs[9]) == '' ? 0 : trim($strs[9])).", 
                                     col4 = ".(trim($strs[10]) == '' ? 0 : trim($strs[10])).", 
                                     col5 = ".(trim($strs[11]) == '' ? 0 : trim($strs[11])).", 
                                     col6 = ".(trim($strs[12]) == '' ? 0 : trim($strs[12])).", 
                                     del_flag = 0, ";
                             if(trim($strs[14]) != '') {
                                  $notesql.= "  on_board_date = str_to_date('".trim($strs[14])."','%Y%m%d'),";
                             }
                             $notesql.="    remark1 = '".trim($strs[16])."', 
                                     arrival_luggage = ".(trim($strs[17]) == '' ? 0 : trim($strs[17])).",
                                     remark2 = '".(trim($strs[18]) == '' ? 0 : trim($strs[18]))."'
                                 where p_id = '".trim($strs[1])."' and l_id = ".trim($strs[4]);
                         break;
                         case "d":
                             $notesql="update `jxc_mainkc` set col1 = 0,
                                    col2 = 0,
                                    col3 = 0,
                                    col4 = 0,
                                    col5 = 0,
                                    col6 = 0,
                                    del_flag = 1
                                     where p_id = '".trim($strs[1])."' and l_id = ".trim($strs[4]);
                             break;
                         default:
                             $notesql = "select 1 from dual";
                             break;
                    }
                //类型是2=发送中的物流上传
                } else {
                    $notesql="update `jxc_mainkc` set col5 = ".(trim($strs[10]) == '' ? 0 : trim($strs[10])).", 
                             col6 = ".(trim($strs[11]) == '' ? 0 : trim($strs[11])).", 
                             on_board_date = str_to_date('".trim($strs[12])."','%Y%m%d'),
                             remark1 = '".trim($strs[14])."'
                             where p_id = '".trim($strs[1])."' and l_id = ".trim($strs[4]);
                }
                $b1 = $nsql->query($notesql);
                echo mysql_error();
                $importStat['n']++;
            }
            $importStat["filename"] = $time;
        } else {
            $importStat["m"] = 5;
        }
    
        return $importStat;
    }
    /**
     * 
     * @return Ambigous <number, boolean, mixed>
     */
    function passChecked() {
        $VioomaUserID = trim($_REQUEST["VioomaUserID"]);
        $kids = $_REQUEST["strChk"];
        $passCheckedSelect = $_REQUEST["passCheckedSelect"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        //把更新之前的记录保留下来，写入日志
        //先确定要保存的列>0
        switch ($passCheckedSelect) {
            case 0:
                $col = "and ifnull(col1,0)> 0";
                break;
            case 1:
                $col = "and ifnull(col2,0)> 0";
                break;
            case 2:
                $col = "and ifnull(col4,0)> 0";
                break;
            case 3:
                $col = "and ifnull(col5,0)> 0";
                break;
        }
        $query = "insert into jxc_buying_log(`productid`, `member`, `col1`, `col2`, `col3`, `col4`, `col5`, `col6`, `pos`, `op_type`) 
            select p_id, '{$VioomaUserID}', col1, col2, col3, col4, col5, col6, concat(l_floor, '-', l_shelf, '-', l_zone, '-', l_horizontal, '-', l_vertical), {$passCheckedSelect}
            from jxc_mainkc where kid in ('" . implode("','", $kids) . "') ".$col;
        $results = $newsql->get_results($query);
        switch ($passCheckedSelect) {
            //'col1'更新至 col2, 清零col1
            case 0:
                $query = "update jxc_mainkc
            set col2 = ifnull(col2,0) + ifnull(col1,0),
                col1 = 0
                where kid in ('" . implode("','", $kids) . "')";
                $count += $newsql->query($query);
                break;
            //col2更新至col3, col3累加, 清零col2
            case 1:
                $query = "update jxc_mainkc
                set col3 = ifnull(col3,0) + ifnull(col2,0),
                col2 = 0
                where kid in ('" . implode("','", $kids) . "')";
                $count += $newsql->query($query);
                break;
            //'col4'更新至 col5, 清零col4
            case 2:
                $query = "update jxc_mainkc
                    set col5 = ifnull(col5,0)+ifnull(col4,0),
                        col4 = 0
                        where kid in ('" . implode("','", $kids) . "')";
                $count += $newsql->query($query);
                break;
            //col5更新至col6, col6累加, 清零col5
            case 3:
                $query = "update jxc_mainkc
                    set col6 = ifnull(col6,0) + ifnull(col5,0),
                        col5 = 0
                        where kid in ('" . implode("','", $kids) . "')";
                $count += $newsql->query($query);
                break;
        }
        return $count;
        
    }
    /**
     * 逻辑删除内容(将必要的字段设置为0)
     * @return Ambigous <boolean, number, mixed>
     */
    function deleteChecked() {
        $kids = $_REQUEST["strChk"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_mainkc
            set col1 = 0,
                col2 = 0,
                col3 = 0,
                col4 = 0,
                col5 = 0,
                col6 = 0,
                del_flag = 1
                where kid in ('" . implode("','", $kids) . "')";
        $count += $newsql->query($query);
        return $count;
    }
    /**
     * 切换pageNameId
     * @return Ambigous <boolean, number, mixed>
     */
    function transfer2Other() {
        $kids = $_REQUEST["strChk"];
        $transferSelect = $_REQUEST["transferSelect"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_mainkc
            set page_name_id = {$transferSelect}
                where kid in ('" . implode("','", $kids) . "')";
        $count += $newsql->query($query);
        return $count;
    }
    
    /**
     * 更新备注1,备注2
     * @return Ambigous <number, boolean, mixed>
     */
    function updateRemark1() {
        $kids = $_REQUEST["strChk"];
        $remark1s = $_REQUEST["remark1"];
        $remark2s = $_REQUEST["remark2"];
//         $col3 = $_REQUEST["col3"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        for($i=0;$i<count($kids);$i++){
            $query = "update jxc_mainkc
                set remark1 = '".$remark1s[$i]."',
                    remark2 = '".$remark2s[$i]."'
                    where kid = ".$kids[$i];
            $count += $newsql->query($query);
        }
        return $count;
    }
    
    /**
     * 更新某一列的值
     * @return Ambigous <number, boolean, mixed>
     */
    function updateColNumber() {
        $op_type = 0;
        $VioomaUserID = trim($_REQUEST["VioomaUserID"]);
        $userID = trim($_REQUEST["userID"]);
        $kids = $_REQUEST["strChk"];
        $col1 = $_REQUEST["col1"];
        $col2 = $_REQUEST["col2"];
        $col3 = $_REQUEST["col3"];
        $col4 = $_REQUEST["col4"];
        $col5 = $_REQUEST["col5"];
        $col6 = $_REQUEST["col6"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_mainkc";
        if(isset($col1)) {
            $query.=" set col1 = '".$col1."'";
            $op_type = 4;
        }
        if(isset($col2)) {
            $query.=" set col2 = '".$col2."'";
            $op_type = 5;
        }
        if(isset($col3)) {
            $query.=" set col3 = '".$col3."'";
            $op_type = 6;
        }
        if(isset($col4)) {
            $query.=" set col4 = '".$col4."'";
            $op_type = 7;
        }
        if(isset($col5)) {
            $query.=" set col5 = '".$col5."'";
            $op_type = 8;
        }
        if(isset($col6)) {
            $query.=" set col6 = '".$col6."'";
            $op_type = 9;
        }
        $query.=" where kid = ".$kids."
                and POSITION('|".$userID."|' IN (select rank from jxc_menu where id = 128)) > 0";
        $count = $newsql->query($query);
        $query = "insert into jxc_buying_log(`productid`, `member`, `col1`, `col2`, `col3`, `col4`, `col5`, `col6`, `pos`, `op_type`)
        select p_id, '{$VioomaUserID}', col1, col2, col3, col4, col5, col6, concat(l_floor, '-', l_shelf, '-', l_zone, '-', l_horizontal, '-', l_vertical), {$op_type}
        from jxc_mainkc where kid = ".$kids;
        $results = $newsql->get_results($query);
        return $count;
    }
    
    /**
     * 在库页面"手打ち仕入れ"
     * @return Ambigous <number, boolean, mixed>
     */
    
    function update_buying() {
        $VioomaUserID = trim($_REQUEST["VioomaUserID"]);
        //默认op_type=10更新仕入れ表0, op_type=11更新仕入れ表1
        $op_type=10;
        $kids = $_REQUEST["kid"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        for($i=0;$i<count($kids);$i++){
            //某一时间之前更新col1, 某一时间之后更新col4
            $query = "update jxc_mainkc  set 
                col1 = case when weekday(now()) < (select p_value from jxc_static where p_name = 'system_buying1_before_date') then col1 + '".$_REQUEST["col3"][$i]."' 
                            when weekday(now()) = (select p_value from jxc_static where p_name = 'system_buying1_before_date') 
                             and hour(now()) < (select p_value from jxc_static where p_name = 'system_buying1_before_hour') then col1 + '".$_REQUEST["col3"][$i]."'  else col1 end,
                col4 = case when  weekday(now()) > (select p_value from jxc_static where p_name = 'system_buying1_before_date') then col4 + '".$_REQUEST["col3"][$i]."' 
                            when weekday(now()) = (select p_value from jxc_static where p_name = 'system_buying1_before_date') 
                             and hour(now()) >= (select p_value from jxc_static where p_name = 'system_buying1_before_hour')  then col4 + '".$_REQUEST["col3"][$i]."' else col4 end,";
            //如果是从"自动上货表"提交的,需要将"状态11"更新为1
            if(!empty($_REQUEST["sstate12"])) {
                $query .= " dtime = now(),
                            l_state11 = 1,";
            }
			$query .= "del_flag = 0 ";
			$pageNameId = $_REQUEST["pageNameId"];
//			if($pageNameId != 0) {
				$query .= " , page_name_id = ".$pageNameId;
				$op_type="1".$pageNameId;
//			}
            $query .= "     where kid = ".$kids[$i]."";
//                 and hour(now()) < (select p_value from jxc_static where p_name = 'system_buying1_before_hour')
            $count += $newsql->query($query);

            //保存日志
            $query = "insert into jxc_buying_log(`productid`, `member`, `col1`, `pos`, `op_type`) 
                select p_id, '{$VioomaUserID}', ".$_REQUEST["col3"][$i].", concat(l_floor, '-', l_shelf, '-', l_zone, '-', l_horizontal, '-', l_vertical), '{$op_type}'
                from jxc_mainkc where kid = ".$kids[$i]."";
            $newsql->query($query);
        }
        return $count;
    }

    function update_lab() {
        $kids = $_REQUEST["kid"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        for($i=0;$i<count($kids);$i++){
            //修改库存位置
            $temp = explode("-",$_REQUEST["position"][$i]);
            $query = "update jxc_mainkc
                set l_id = '".$_REQUEST["lab_id"][$i]."',
                    `l_floor` = '".$temp[0]."',
                    `l_shelf` = '".$temp[1]."',
                    `l_zone` = '".$temp[2]."',
                    `l_horizontal` = '".$temp[3]."',
                    `l_vertical` = '".$temp[4]."'
                    where kid = ".$kids[$i];
            $count += $newsql->query($query);
            //修改价格
            $query = "update jxc_basic
                set cp_sale1 = '".$_REQUEST["cp_sale"][$i]."'
                    where cp_number = '".$_REQUEST["cp_number"][$i]."'";
            $count += $newsql->query($query);
        }
        return $count;
    }
    function exportExcel() {
        $title = "仕入れ表 ";
        $headers = array('コントロール','商品コード','商品名','仕様','倉庫番号','在庫位置(階-棚-ゾーン-横-縦)','現在在庫数','周3前未审核数','周3前当日仕入れ数','周3前仕入れ総合','周3后未审核数','周3后当日仕入れ数','周3后仕入れ総合','販売平均数','発送日付','状態','備考1','到着荷物','備考2');
        $results = $this->initPage();
          
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
                    if($j <= 0) {
                        $j++;
                        continue;
                    }
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
}
    
?>  
