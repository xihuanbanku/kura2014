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
        $query = "select  a.*, case when b.cp_number is null then '未使用' else '使用中' end status_str, c.categories cp_categories_str, d.categories cp_categories_down_str";
        if(isset($_REQUEST["pageIndex"])) {
            $query .= " , case when b.cp_number is null then 0 else 1 end status ";
        }
        $query .= " from jxc_barcode a left join jxc_basic b
                on a.productid = b.cp_number
                join jxc_categories c
                on a.cp_categories = c.id
                join jxc_categories d
                on a.cp_categories_down = d.id
            where  1=1";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (productid like '%{$s_text}%'
                          or barcode   like '%{$s_text}%')";
        }
        $statusOptions = $_REQUEST["statusOptions"];
        if($statusOptions == 0) {
            $query .= " and b.cp_number is null";
        } else if ($statusOptions == 1) {
            $query .= " and b.cp_number is not null";
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
        $query = "select count(0)  from jxc_barcode a left join jxc_basic b 
            on a.productid = b.cp_number
            where  1=1";
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (productid like '%{$s_text}%'
                          or barcode   like '%{$s_text}%')";
        }
        $statusOptions = $_REQUEST["statusOptions"];
        if($statusOptions == 0) {
            $query .= " and b.cp_number is null";
        } else if ($statusOptions == 1) {
            $query .= " and b.cp_number is not null";
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

    function insert() {
        $productid   =$_REQUEST["productid"];
        $barcode   =$_REQUEST["barcode"];
        $cp_categories   =$_REQUEST["cp_categories"];
        $cp_categories_down   =$_REQUEST["cp_categories_down"];
        
        $newsql = new ezSQL_mysql();
        if(checkExists($productid, $barcode) > 0) {
             return 0;
        }
        $query = "INSERT INTO `jxc_barcode` (`productid`, `barcode`, `cp_categories`, `cp_categories_down`)
            values('$productid','$barcode','$cp_categories','$cp_categories_down')";
        $result = $newsql->query($query) or mysql_error();
        return $result;
    }
    function delete() {
        $userID = $_REQUEST["id"];
        $query = "delete from jxc_barcode where id = ".$userID;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
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
     * 检查是否商品ID或barcode已经存在
     * @param unknown $productid
     * @param unknown $barcode
     * @return number
     */
    function checkExists($productid, $barcode){
        $query = "select count(0) from `jxc_barcode` where `productid` = '{$productid}' or `barcode` = '{$barcode}'";
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query) or mysql_error();
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
        $title = "商品バーコード管理";
		$headers = array('ID','商品コード','バーコード','大分類','小分類','ステータス');
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