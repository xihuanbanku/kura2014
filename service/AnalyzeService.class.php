<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
require_once '../PHPExcel.php';
date_default_timezone_set('PRC');
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = initPage();
        break;
        case "initState":
            $result = initState();
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
        case "writeState":
            $result = writeState();
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
        $cp_categories = $_REQUEST["cp_categories"];
        $cp_categories_down = $_REQUEST["cp_categories_down"];
        $query = "select a.cp_number, a.number, 
                            b.p_name astate1_str, c.p_name astate2_str, d.p_name astate3_str,
                            e.p_name astate4_str, f.p_name astate5_str, a.mindate, a.maxdate
                            from jxc_analyze a,
                                (select * from jxc_static where p_type='SYSTEM_ANALYZE_STATE1') b,
                                (select * from jxc_static where p_type='SYSTEM_ANALYZE_STATE2') c,
                                (select * from jxc_static where p_type='SYSTEM_ANALYZE_STATE3') d,
                                (select * from jxc_static where p_type='SYSTEM_ANALYZE_STATE4') e,
                                (select * from jxc_static where p_type='SYSTEM_ANALYZE_STATE5') f
            where a.astate1 = b.p_value
              and a.astate2 = c.p_value
              and a.astate3 = d.p_value
              and a.astate4 = e.p_value
              and a.astate5 = f.p_value";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and a.cp_number LIKE '%" . $s_text . "%' ";
        }
        if((int)$cp_categories>0) {
            $query .= " and a.cp_categories = '{$cp_categories}'";
        }
        if((int)$cp_categories_down>0) {
            $query .= " and a.cp_categories_down = '{$cp_categories_down}'";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.cp_number in ('" . implode("','", $ids) . "')";
            }
        }
        if(!empty($_REQUEST["orderBy"])) {
            $sort = $_REQUEST["orderBy"];
            switch ($sort) {
                case 1:
                    $query .= " order by mindate";
                    break;
                case 2:
                    $query .= " order by mindate desc";
                    break;
                case 3:
                    $query .= " order by maxdate";
                    break;
                case 4:
                    $query .= " order by maxdate desc";
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
		$totaljson = "\"totalcount\":\"".$total."\"";
		return "{".$totaljson.",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }
    

    function initPageCount() {
        $cp_categories = $_REQUEST["cp_categories"];
        $cp_categories_down = $_REQUEST["cp_categories_down"];
        $query = "select count(0)  
            from jxc_analyze a
            where  1=1";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and a.cp_number LIKE '%" . $s_text . "%' ";
        }
        if((int)$cp_categories>0) {
            $query .= " and a.cp_categories = '{$cp_categories}'";
        }
        if((int)$cp_categories_down>0) {
            $query .= " and a.cp_categories_down = '{$cp_categories_down}'";
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }

    /**
     * 上传文件
     * @param unknown $pageId
     * @param unknown $file
     * @param unknown $filetempname
     * @return multitype:number string
     */
    function uploadFile($pageId, $file, $filetempname) {
        $importStat=array("n"=>0,"u"=>0,"d"=>0,"e"=>0,"m"=>0,"filename"=>0);
        // 自己设置的上传文件存放路径
        $filePath = '/home/p-mon/pmon.jp/public_html/kura2014/upload/';
        $str = "";
        // 下面的路径按照你PHPExcel的路径来修改
        set_include_path('/home/p-mon/pmon.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());
    
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
            $highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());; // 取得总列数
    
            $nsql= new ezSQL_mysql();
            // 循环读取excel文件,读取一条,插入一条
            for ($j = 1; $j <= $highestRow; $j ++) {
                $strs = array();
                for ($k = 0; $k < $highestColumn; $k ++) {
                    $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                    $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName$j")->getValue();
                    //                 echo "$columnName$j"."----".$cellValue."----";
                    array_push($strs, $cellValue); // 读取单元格
                }
                    switch ($strs[0]) {
                        case "n":
                            $index = 1;
                            if(checkExists($strs[1], $strs[2]) > 0) {
                                $importStat["m"] = 1;
                                echo date("Ymd-H:i:s")."----".$j."出现错误[该记录已存在]商品CODE:".$strs[1]."---BarCode:".$strs[2];
                                error_log(date("Ymd-H:i:s")."----".$j."出现错误[该记录已存在]商品CODE:".$strs[1]."---BarCode:".$strs[2]."\n", 3, "../logs/upload.log");
                                break 2;
                            }
                            $catilogs = explode("/", trim($strs[3]));
                            $notesql="insert into `jxc_barcode`(`productid`, `barcode`, `cp_categories`, `cp_categories_down`) values( ";
                            $notesql.="'".trim($strs[$index++])."',";
                            $notesql.="'".trim($strs[$index++])."',";
                            $notesql.="'".trim($catilogs[0])."',";
                            $notesql.="'".trim($catilogs[1])."')";
                            $importStat['n']++;
                         break;
                         case "d":
                             $notesql="delete from `jxc_barcode` 
                                     where productid = '".trim($strs[1])."'";
                            $importStat['d']++;
                             break;
                         default:
                             $notesql = "select 1 from dual";
                             break;
                    }
                $b1 = $nsql->query($notesql);
                echo mysql_error();
            }
            $importStat["filename"] = $time;
        } else {
            $importStat["m"] = 5;
        }
    
        return $importStat;
    }

    /**
     * 初始化状态下拉框
     * @return string
     */
    function initState() {
        $parent_id = $_REQUEST["sid"];
        $newsql = new ezSQL_mysql();
        $query = "select * from jxc_static where p_type = 'SYSTEM_ANALYZE_STATE".$parent_id."'";
        $results = $newsql->get_results($query);
        $selectHtml = "";
        foreach ($results as $result) {
            $selectHtml .= "<option value='{$result->p_value}'>{$result->p_name}</option>";
        }
        return $selectHtml;
    }
    
    function update() {
        $id = $_REQUEST["id"];

        $productid   =$_REQUEST["productid"];
        $barcode   =$_REQUEST["barcode"];
        $cp_categories   =$_REQUEST["cp_categories"];
        $cp_categories_down   =$_REQUEST["cp_categories_down"];

//         if(checkExists($productid, $barcode) > 0) {
//             return 0;
//         }
        $newsql = new ezSQL_mysql();
        $query = "update jxc_barcode 
            set productid = '".$productid."',
                barcode   = '".$barcode    ."',
                cp_categories       = '".$cp_categories   ."',
                cp_categories_down        = '".$cp_categories_down   ."'
                where id = ".$id."";
        return $newsql->query($query);
    }

    /**
     * 更新状态
     */
    function writeState() {
        $newsql = new ezSQL_mysql();
        $query = "update jxc_analyze a set ";
        for ($i=1; $i<5; $i++) {
            if(isset($_REQUEST["writeState{$i}Select"]) && $_REQUEST["writeState{$i}Select"] !="") {
                $query .= "a.astate{$i} = '" . $_REQUEST["writeState{$i}Select"]. "',";
            }
        }
        if(strripos($query, ",") == strlen($query)-1) {
            $query = subStr($query, 0, strlen($query) - 1);
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            $query .= " where a.cp_number in ('" . implode("','", $strChk) . "')";
        }
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
        $query = "delete from jxc_barcode
                where l_id in ('" . implode("','", $kids) . "')";
        return $newsql->query($query);
    }
	
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "商品贩卖分析";
		$headers = array('商品コード','库存','状态1','状态2','状态3','状态4','状态5','第一次售出日','最近一次售出日');
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
