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
        $query = "select * from jxc_mails a
            where del_flag=0";
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
           $query .= " and (cp_number like '%{$s_text}%'
                          or cp_name     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or track_number     like '%{$s_text}%')";
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
                case 3:
                    $query .= "   order by status ";
                break;
                case 4:
                    $query .= "   order by status desc ";
                break;
                case 5:
                    $query .= "   order by send_status ";
                break;
                case 6:
                    $query .= "   order by send_status desc ";
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
        $query = "select count(0) c
        from jxc_mails
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
           $query .= " and (cp_number like '%{$s_text}%'
                          or cp_name     like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or track_number     like '%{$s_text}%')";
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
            // 循环读取excel文件,读取一条,插入一条,行号j没有0,从1开始
            for ($j = 2; $j <= $highestRow; $j ++) {
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
//                         if(checkExists($strs[1], $strs[2]) > 0) {
//                             $importStat["m"] = 1;
//                             echo date("Ymd-H:i:s")."----".$j."出现错误[该记录已存在]商品CODE:".$strs[1]."---BarCode:".$strs[2];
//                             error_log(date("Ymd-H:i:s")."----".$j."出现错误[该记录已存在]商品CODE:".$strs[1]."---BarCode:".$strs[2]."\n", 3, "../logs/upload.log");
//                             break 2;
//                         }
                        $total = intval($strs[4])*intval($strs[5]);
                        $notesql="insert into `jxc_finance_booking`(`cp_number`, `cp_name`, `cp_type`, `cp_price`, `cp_count`, `total`, `track_number`, `remark1`, send_status, status)
                        values('".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".$total."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."')";
                        $importStat['n']++;
                        break;
                    case "u":
                        $index = 2;
                        $total = intval($strs[4])*intval($strs[5]);
                        $notesql="update `jxc_finance_booking`
                            set `cp_name` = '".trim($strs[$index++])."',
                                `cp_type` = '".trim($strs[$index++])."',
                                `cp_price` = '".trim($strs[$index++])."',
                                `cp_count` = '".trim($strs[$index++])."',
                                `total` = '".$total."',
                                `track_number` = '".trim($strs[$index++])."',
                                `remark1` = '".trim($strs[$index++])."'
                                `send_status` = '".trim($strs[$index++])."'
                                `status` = '".trim($strs[$index++])."'
                                     where cp_number = '".trim($strs[1])."'";
                        $importStat['u']++;
                        break;
                    case "d":
                        $notesql="update `jxc_finance_booking` set del_flag=1
                                     where cp_number = '".trim($strs[1])."'";
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
     * 插入一行记录
     * @return boolean|number|mixed
     */
    function insert() {
        
        $cp_number        =$_REQUEST["cp_number"];
        $cp_name        =$_REQUEST["cp_name"];
        $cp_type        =$_REQUEST["cp_type"];
        $cp_price        =$_REQUEST["cp_price"];
        $cp_count        =$_REQUEST["cp_count"];
        $track_number        =$_REQUEST["track_number"];
        $send_status        =$_REQUEST["send_status"];
        $remark1        =$_REQUEST["remark1"];
        $picture_name        =$_REQUEST["picture_name"];
        
        //总金额 =单价*数量
        $total    =intval($cp_price)*intval($cp_count);
        
        $newsql = new ezSQL_mysql();
        $query = "INSERT INTO `jxc_finance_booking` (`cp_number`, `cp_name`, `cp_type`, `cp_price`, `cp_count`, `total`, `track_number`, `remark1`, send_status, `picture_name`)
            values('{$cp_number}', '{$cp_name}', '{$cp_type}', '{$cp_price}', '{$cp_count}', '{$total}', 
            '{$track_number}', '{$remark1}', '{$send_status}','{$picture_name}')";
        return $newsql->query($query);
    }
    /**
     * 删除一行记录
     * @return boolean|number|mixed
     */
    function delete() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];
        $query = "update jxc_finance_booking set del_flag = 1 where id = ".$id ."
                and (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0)";
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    /**
     * 更新记录
     * @return boolean|number|mixed
     */
    function update() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];

        $cp_number        =$_REQUEST["cp_number"];
        $cp_name        =$_REQUEST["cp_name"];
        $cp_type        =$_REQUEST["cp_type"];
        $cp_price        =$_REQUEST["cp_price"];
        $cp_count        =$_REQUEST["cp_count"];
        $track_number        =$_REQUEST["track_number"];
        $remark1        =$_REQUEST["remark1"];
        $send_status        =$_REQUEST["send_status"];
        $picture_name        =$_REQUEST["picture_name"];
        
        //总金额 =单价*数量
        $total    =intval($cp_price)*intval($cp_count);
        
        $newsql = new ezSQL_mysql();
        $query = "update jxc_finance_booking 
                set 
                cp_number     ='".$cp_number."',
                cp_name       ='".$cp_name."',
                cp_type       ='".$cp_type."',
                cp_price      ='".$cp_price."',
                cp_count      ='".$cp_count."',
                total         ='".$total."',
                track_number  ='".$track_number."',
                remark1       ='".$remark1."',
                send_status       ='".$send_status."',
                picture_name  ='".$picture_name."'
                where id = ".$id." and (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0)";
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
     * 批量更新状态
     * @return Ambigous <boolean, number, mixed>
     */
    function updateStatus() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $statusOptions = $_REQUEST["statusOptions"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_finance_booking
            set status = {$statusOptions}
                where id in ('" . implode("','", $kids) . "')
                and (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0)";
        return $newsql->query($query);
    }

    /**
     * 批量删除
     * @return Ambigous <boolean, number, mixed>
     */
    function deleteChked() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_finance_booking
            set del_flag = 1
                where id in ('" . implode("','", $kids) . "')
                    and (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0)";
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "購入予定";
		$headers = array('管理番号','商品管理番号','商品名称','仕様','単価','数量','总金额','追踪番号','備考','審査状態','登记日期','領収書');
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
