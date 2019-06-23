<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
require_once '../PHPExcel.php';
date_default_timezone_set('PRC');
$excelTableService = new ExcelTableService();
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = $excelTableService->initPage();
        break;
        case "passChecked":
            $result = $excelTableService->passChecked();
            break;
        case "deleteChecked":
            $result = $excelTableService->deleteChecked();
            break;
        case "transfer2Other":
            $result = $excelTableService->transfer2Other();
            break;
        case "updateFinish":
            $result = $excelTableService->updateFinish();
            break;
        case "updateWantNumber":
            $result = $excelTableService->updateWantNumber();
            break;
        case "updateCol":
            $result = $excelTableService->updateCol();
            break;
        case "update_lab":
            $result = $excelTableService->update_lab();
            break;
        case "exportExcel":
            $result = $excelTableService->exportExcel();
            break;
        case "cleanAll":
            $result = $excelTableService->cleanAll();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  

class ExcelTableService {
    function initPage($pageCount=100000, $pageIndex=1) {
        if(isset($_REQUEST["pageCount"])) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if(isset($_REQUEST["pageIndex"])) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        if(isset($_REQUEST["pageId"])) {
            $pageId = $_REQUEST["pageId"];
        }
        $query = "select id, case when remark='' then '&nbsp;&nbsp;&nbsp;' else remark end remark,
                             case when a = '' then '&nbsp;&nbsp;&nbsp;' else a end a,
                             case when b = '' then '&nbsp;&nbsp;&nbsp;' else b end b,
                             case when c = '' then '&nbsp;&nbsp;&nbsp;' else c end c,
                             case when d = '' then '&nbsp;&nbsp;&nbsp;' else d end d,
                             case when e = '' then '&nbsp;&nbsp;&nbsp;' else e end e,
                             case when f = '' then '&nbsp;&nbsp;&nbsp;' else f end f,
                             case when g = '' then '&nbsp;&nbsp;&nbsp;' else g end g,
                             case when h = '' then '&nbsp;&nbsp;&nbsp;' else h end h,
                             case when i = '' then '&nbsp;&nbsp;&nbsp;' else i end i,
                             case when j = '' then '&nbsp;&nbsp;&nbsp;' else j end j,
                             case when k = '' then '&nbsp;&nbsp;&nbsp;' else k end k,
                             case when l = '' then '&nbsp;&nbsp;&nbsp;' else l end l,
                             case when m = '' then '&nbsp;&nbsp;&nbsp;' else m end m,
                             case when n = '' then '&nbsp;&nbsp;&nbsp;' else n end n,
                             case when o = '' then '&nbsp;&nbsp;&nbsp;' else o end o,
                             case when p = '' then '&nbsp;&nbsp;&nbsp;' else p end p,
                             case when q = '' then '&nbsp;&nbsp;&nbsp;' else q end q,
                             case when r = '' then '&nbsp;&nbsp;&nbsp;' else r end r,
                             case when s = '' then '&nbsp;&nbsp;&nbsp;' else s end s,
                             case when t = '' then '&nbsp;&nbsp;&nbsp;' else t end t,
                             case when u = '' then '&nbsp;&nbsp;&nbsp;' else u end u,
                             case when v = '' then '&nbsp;&nbsp;&nbsp;' else v end v,
                             case when w = '' then '&nbsp;&nbsp;&nbsp;' else w end w,
                             case when x = '' then '&nbsp;&nbsp;&nbsp;' else x end x,
                             case when y = '' then '&nbsp;&nbsp;&nbsp;' else y end y,
                             case when z = '' then '&nbsp;&nbsp;&nbsp;' else z end z,
                             finish_flag from jxc_excel_table
            where page_id ={$pageId}";
//         if(!empty($_REQUEST["s_text"])){
//            $s_text = $_REQUEST["s_text"];
//            $query .= " and (cp_number like '%{$s_text}%'
//                           or cp_name   like '%{$s_text}%'
//                           or cp_gg     like '%{$s_text}%')";
//         }
//         if(!empty($_REQUEST["strChk"])) {
//             $strChk = $_REQUEST["strChk"];
//             if($strChk != "") {
//                 $ids = array();
//                 foreach ($strChk as $id) {
//                     array_push($ids, $id);
//                 }
//                 $query .= " and a.kid in ('" . implode("','", $ids) . "')";
//             }
//         }
//         if(isset($_REQUEST["pageNameId"])) {
//             $pageNameId = $_REQUEST["pageNameId"];
//             if($pageNameId != "") {
//                 $query .= " and a.page_name_id = ".$pageNameId;
//             }
//         }
//         if(!empty($_REQUEST["orderByField"])) {
//             $query .= "   ".$_REQUEST["orderByField"];
//         }
//         $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
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
        if(isset($_REQUEST["pageId"])) {
            $pageId = $_REQUEST["pageId"];
        }
        $query = "select count(0) from jxc_excel_table
            where page_id ={$pageId}";
//         if(!empty($_REQUEST["s_text"])){
//             $s_text = $_REQUEST["s_text"];
//             $query .= " and (cp_number like  '%{$s_text}%'
//             or cp_name   like '%{$s_text}%'
//             or cp_gg     like '%{$s_text}%')";
//         }
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
//                     switch ($strs[0]) {
//                         case "u":
                            $index = 0;
                            $notesql="update `jxc_excel_table` set ";
                            $notesql.=" a = '".trim($strs[$index++])."',";
                            $notesql.=" b = '".trim($strs[$index++])."',";
                            $notesql.=" c = '".trim($strs[$index++])."',";
                            $notesql.=" d = '".trim($strs[$index++])."',";
                            $notesql.=" e = '".trim($strs[$index++])."',";
                            $notesql.=" f = '".trim($strs[$index++])."',";
                            $notesql.=" g = '".trim($strs[$index++])."',";
                            $notesql.=" h = '".trim($strs[$index++])."',";
                            $notesql.=" i = '".trim($strs[$index++])."',";
                            $notesql.=" j = '".trim($strs[$index++])."',";
                            $notesql.=" k = '".trim($strs[$index++])."',";
                            $notesql.=" l = '".trim($strs[$index++])."',";
                            $notesql.=" m = '".trim($strs[$index++])."',";
                            $notesql.=" n = '".trim($strs[$index++])."',";
                            $notesql.=" o = '".trim($strs[$index++])."',";
                            $notesql.=" p = '".trim($strs[$index++])."',";
                            $notesql.=" q = '".trim($strs[$index++])."',";
                            $notesql.=" r = '".trim($strs[$index++])."',";
                            $notesql.=" s = '".trim($strs[$index++])."',";
                            $notesql.=" t = '".trim($strs[$index++])."',";
                            $notesql.=" u = '".trim($strs[$index++])."',";
                            $notesql.=" v = '".trim($strs[$index++])."',";
                            $notesql.=" w = '".trim($strs[$index++])."',";
                            $notesql.=" x = '".trim($strs[$index++])."',";
                            $notesql.=" y = '".trim($strs[$index++])."',";
                            $notesql.=" z = '".trim($strs[$index++])."' 
                                 where id = '".($pageId*1000 + $j)."' and page_id = ".$pageId;
//                          break;
//                          case "d":
//                              $notesql="update `jxc_mainkc` set col1 = 0,
//                                     col2 = 0,
//                                     col3 = 0,
//                                     col4 = 0,
//                                     col5 = 0,
//                                     col6 = 0,
//                                     del_flag = 1
//                                      where p_id = '".trim($strs[1])."' and l_id = ".trim($strs[4]);
//                              break;
//                          default:
//                              $notesql = "select 1 from dual";
//                              break;
//                     }
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
        $kids = $_REQUEST["strChk"];
        $passCheckedSelect = $_REQUEST["passCheckedSelect"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        switch ($passCheckedSelect) {
            //'col1'更新至 col2, 清零col1
            case 0:
                for($i=0;$i<count($kids);$i++){
                    $query = "update jxc_mainkc
                set col2 = ifnull(col1,0),
                    col1 = 0
                    where kid = ".$kids[$i];
                    $count += $newsql->query($query);
                }
                break;
            //col2更新至col3, col3累加, 清零col2
            case 1:
                for($i=0;$i<count($kids);$i++){
                    $query = "update jxc_mainkc
                    set col3 = ifnull(col3,0) + ifnull(col2,0),
                    col2 = 0
                    where kid = ".$kids[$i];
                    $count += $newsql->query($query);
                }
                break;
            //'col4'更新至 col5, 清零col4
            case 2:
                for($i=0;$i<count($kids);$i++){
                    $query = "update jxc_mainkc
                        set col5 = ifnull(col4,0),
                            col4 = 0
                            where kid = ".$kids[$i];
                    $count += $newsql->query($query);
                }
                break;
            //col5更新至col6, col6累加, 清零col5
            case 3:
                for($i=0;$i<count($kids);$i++){
                    $query = "update jxc_mainkc
                        set col6 = ifnull(col6,0) + ifnull(col5,0),
                            col5 = 0
                            where kid = ".$kids[$i];
                    $count += $newsql->query($query);
                }
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
     * 切换finish flag状态
     * @return Ambigous <number, boolean, mixed>
     */
    function updateFinish() {
        $userID = trim($_REQUEST["userID"]);
        $kids = $_REQUEST["strChk"];
        $finishFlag = $_REQUEST["finishFlag"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_excel_table
            set finish_flag = '".$finishFlag."'
                where id = ".$kids;
        if($finishFlag==0) {
            $query.=" and POSITION('|".$userID."|' IN (select rank from jxc_menu where id = 131)) > 0";
        }
        return $newsql->query($query);
    }
    
    /**
     * 更新某一列的值
     * @return Ambigous <number, boolean, mixed>
     */
    function updateCol() {
        $userID = trim($_REQUEST["userID"]);
        $kids = $_REQUEST["strChk"];
        $newsql = new ezSQL_mysql();
        if(isset($_REQUEST["remark"])) {
            $remark = trim($_REQUEST["remark"]);
            $query = "update jxc_excel_table  set remark = '".$remark."' where id = ".$kids;
            return $newsql->query($query);
        }
        $query = "update jxc_excel_table";
        foreach ($_REQUEST as $key=>$val) {
            if(!strpos("1abcdefghijklmnopqrstuvwxyz",$key)) {
                continue;
            }
            $query.=" set {$key} = '".$val."'";
        }
        $query.=" where id = ".$kids."
                and POSITION('|".$userID."|' IN (select rank from jxc_menu where id = 131)) > 0";
        return $newsql->query($query);
    }
    
    /**
     * 清空所有内容
     * @return Ambigous <number, boolean, mixed>
     */
    
    function cleanAll() {
        $pageId = $_REQUEST["pageId"];
        $newsql = new ezSQL_mysql();
        //某一时间之前更新col1, 某一时间之后更新col4
        $query = "update jxc_excel_table  set 
                 a = '',
                 b = '',
                 c = '',
                 d = '',
                 e = '',
                 f = '',
                 g = '',
                 h = '',
                 i = '',
                 j = '',
                 k = '',
                 l = '',
                 m = '',
                 n = '',
                 o = '',
                 p = '',
                 q = '',
                 r = '',
                 s = '',
                 t = '',
                 u = '',
                 v = '',
                 w = '',
                 x = '',
                 y = '',
                 z = '',
                 remark = '',
                finish_flag = 0
             where page_id = ".$pageId;
           return $newsql->query($query);
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
        $title = "sheet1";
//         $headers = array('コントロール','商品コード','商品名','仕様','倉庫番号','在庫位置(階-棚-ゾーン-横-縦)','現在在庫数','周3前未审核数','周3前当日仕入れ数','周3前仕入れ総合','周3后未审核数','周3后当日仕入れ数','周3后仕入れ総合','販売平均数','発送日付','状態','備考1','到着荷物','備考2');
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
//         for ($i=0; $i<sizeof($headers); $i++) {
//             $workSheet->setCellValue($COLUMNINDEXS[$i].'1', $headers[$i]);
//         }
        
        $i = 1;
        if($results) {
            foreach ($results as $result) {
                $j = 0;
                foreach ($result as $key=> $value){
                    if($key == "id") {
                        continue;
                    }
                    $workSheet->setCellValue($COLUMNINDEXS[$j++].$i, str_replace("&nbsp;", " ", $value));
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
}
    
?>  