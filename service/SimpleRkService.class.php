<?php
/** Include PHPExcel */
require_once '../PHPExcel.php';
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
define("BARCODE_BASE_START", 100000000000);
define("ID_BASE_START", 500);
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "insert":
            $result = insert();
        break;
        case "out_excel":
            $result = out_excel();
        break;
        case "loadItem":
            $result = loadItem();
        break;
        case "submitColParam":
            $result = submitColParam();
        break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function loadItem() {
        $cp_categories = trim($_REQUEST["cp_categories"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "select * from jxc_simple_rk_config where id='".$cp_categories."'";
        $result = $newsql->get_results($sql);
        return json_encode($result);
    }
    
    function submitColParam() {
        $cp_categories = trim($_REQUEST["cp_categories"]);
        $item_1 = trim($_REQUEST["item_1"]);
        $item_2 = trim($_REQUEST["item_2"]);
        $item_3 = trim($_REQUEST["item_3"]);
        $item_4 = trim($_REQUEST["item_4"]);
        $item_5 = trim($_REQUEST["item_5"]);
        $item_6 = trim($_REQUEST["item_6"]);
        $item_7 = trim($_REQUEST["item_7"]);
        $item_8 = trim($_REQUEST["item_8"]);
        $item_9 = trim($_REQUEST["item_9"]);
        $item_10 = trim($_REQUEST["item_10"]);
        $item_type_1 = trim($_REQUEST["item_type_1"]);
        $item_type_2 = trim($_REQUEST["item_type_2"]);
        $item_type_3 = trim($_REQUEST["item_type_3"]);
        $item_type_4 = trim($_REQUEST["item_type_4"]);
        $item_type_5 = trim($_REQUEST["item_type_5"]);
        $item_type_6 = trim($_REQUEST["item_type_6"]);
        $item_type_7 = trim($_REQUEST["item_type_7"]);
        $item_type_8 = trim($_REQUEST["item_type_8"]);
        $item_type_9 = trim($_REQUEST["item_type_9"]);
        $item_type_10 = trim($_REQUEST["item_type_10"]);
        $bullet_cols1 = "";
        $bullet_cols2 = "";
        $bullet_cols3 = "";
        $bullet_cols4 = "";
        $bullet_cols5 = "";
        $bullet_cols6 = "";
        for($i=1;$i<11;$i++) {
            $bullet_cols1 .= $_REQUEST["bullet_cols_".$i][0].",'' '',";
            $bullet_cols2 .= $_REQUEST["bullet_cols_".$i][1].",'' '',";
            $bullet_cols3 .= $_REQUEST["bullet_cols_".$i][2].",'' '',";
            $bullet_cols4 .= $_REQUEST["bullet_cols_".$i][3].",'' '',";
            $bullet_cols5 .= $_REQUEST["bullet_cols_".$i][4].",'' '',";
            $bullet_cols6 .= $_REQUEST["bullet_cols_".$i][5].",'' '',";
        }
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_simple_rk_config set `item_1`='{$item_1}',
                                                `item_2`='{$item_2}',
                                                `item_3`='{$item_3}',
                                                `item_4`='{$item_4}',
                                                `item_5`='{$item_5}',
                                                `item_6`='{$item_6}',
                                                `item_7`='{$item_7}',
                                                `item_8`='{$item_8}',
                                                `item_9`='{$item_9}',
                                                `item_10`='{$item_10}',
                                                `item_type_1`='{$item_type_1}',
                                                `item_type_2`='{$item_type_2}',
                                                `item_type_3`='{$item_type_3}',
                                                `item_type_4`='{$item_type_4}',
                                                `item_type_5`='{$item_type_5}',
                                                `item_type_6`='{$item_type_6}',
                                                `item_type_7`='{$item_type_7}',
                                                `item_type_8`='{$item_type_8}',
                                                `item_type_9`='{$item_type_9}',
                                                `item_type_10`='{$item_type_10}',
                                                `bullet_cols_1`='{$bullet_cols1}''''',
                                                `bullet_cols_2`='{$bullet_cols2}''''',
                                                `bullet_cols_3`='{$bullet_cols3}''''',
                                                `bullet_cols_4`='{$bullet_cols4}''''',
                                                `bullet_cols_5`='{$bullet_cols5}''''',
                                                `bullet_cols_6`='{$bullet_cols6}'''''
               where id='".$cp_categories."'"; 
        return $newsql->query($sql);
    }
    
    function insert() {
        $userID = $_REQUEST["userID"];
        $new = $_REQUEST["new"];
        $year = $_REQUEST["year"];
        $labid = $_REQUEST["labid"];
        $l_state1 = $_REQUEST["l_state1"];
        $l_state2 = $_REQUEST["l_state2"];
        $l_state3 = $_REQUEST["l_state3"];
        $l_state4 = $_REQUEST["l_state4"];
        $l_state5 = $_REQUEST["l_state5"];
        $l_state6 = $_REQUEST["l_state6"];
        $l_state7 = $_REQUEST["l_state7"];
        $l_state8 = $_REQUEST["l_state8"];
        $l_state9 = $_REQUEST["l_state9"];
        $l_state10 = $_REQUEST["l_state10"];
        $cp_categories = $_REQUEST["cp_categories"];
        $cp_categories_down = $_REQUEST["cp_categories_down"];
        $serial = trim($_REQUEST["serial"]);
        $model = trim($_REQUEST["model"]);
        $modelDetail = trim($_REQUEST["modelDetail"]);
        $item_1 = $_REQUEST["item_1"];
        $item_2 = $_REQUEST["item_2"];
        $item_3 = $_REQUEST["item_3"];
        $item_4 = $_REQUEST["item_4"];
        $item_5 = $_REQUEST["item_5"];
        $item_6 = $_REQUEST["item_6"];
        $item_7 = $_REQUEST["item_7"];
        $item_8 = $_REQUEST["item_8"];
        $item_9 = $_REQUEST["item_9"];
        $item_10 = $_REQUEST["item_10"];
        $remark = trim($_REQUEST["remark"]);
        
        $newsql = new ezSQL_mysql();
        //获取该serial中最大的id, +1作为当前新商品的id
        $result = $newsql->get_var("select max(REPLACE(cp_number, '{$serial}', '')+0) from jxc_basic where cp_number like '{$serial}%'");
        preg_match("/\d+/", $result, $number);
        if($number) {
            $next_id = intval($number[0])+1;
        } else {
            $next_id = 1;
        }
        
        $serialID = $serial.sprintf("%03d",$next_id);
        $result = $newsql->query("insert into jxc_simple_rk_new(`new`, `year`, `category`, `category_down`, `serial`, `model`, `modelDetail`, `userID`, item_1, item_2, item_3, item_4, item_5, item_6, item_7, item_8, item_9, item_10, remark, lid)
            values('$new','$year','$cp_categories','$cp_categories_down','$serialID','$model','$modelDetail','$userID', '$item_1', '$item_2', '$item_3', '$item_4', '$item_5', '$item_6', '$item_7', '$item_8', '$item_9', '$item_10', '$remark','$labid')") or mysql_error();
        $current_id = mysql_insert_id();
        
        //获取当前分类item1-10的组合顺序
        $sql = "select bullet_cols_1,bullet_cols_2,bullet_cols_3,bullet_cols_4,bullet_cols_5,bullet_cols_6 from jxc_simple_rk_config where id='{$cp_categories}'";
        $result = $newsql->get_row($sql);
        //拼接sql
        $bullet1_6 ="concat(".$result->bullet_cols_1.") cp_bullet_1,concat(".$result->bullet_cols_2.") cp_bullet_2,concat(".$result->bullet_cols_3.") cp_bullet_3,
            concat(".$result->bullet_cols_4.") cp_bullet_4,concat(".$result->bullet_cols_5.") cp_bullet_5,concat(".$result->bullet_cols_6.") cp_bullet_6";
        
        //获取当前分类title的组合顺序
        $sql = "select price_1, price_2, price_3, price_4, price_5 from jxc_description where id='{$cp_categories}'";
        $result = $newsql->get_row($sql);
        //拼接sql
        $cp_title ="concat(".$result->price_1.",' ', ".$result->price_2.",' ', ".$result->price_3.",' ', ".$result->price_4.",' ', ".$result->price_5.") cp_title";
        
        $sql = "select a.bar_code,a.serial, a.model, a.modelDetail, {$bullet1_6}, {$cp_title},
            a.remark, f.name url_prefix, g.name des, b.`score` new_score, e.`score` year_score,
            c.categories category, d.categories category_down, c.id category_id, d.id category_down_id, c.score category_score, d.score category_down_score,
            c.browse_node browse_node, d.browse_node category_down_browse_node, c.addword addword, d.addword category_down_addword
            from jxc_simple_rk_new a, jxc_new b, jxc_categories c, jxc_categories d, jxc_year e, jxc_url_prefix f, jxc_description g
            where a.new = b.id and a.category = c.id  and  a.category = f.category_id and a.new = f.new_id  and a.category = g.id and a.category_down = d.id and a.year = e.id and a.serial='{$serialID}'";
        $results = $newsql->get_row($sql);
        $insert = "INSERT INTO `jxc_basic` (`cp_number`, `cp_tm`, `cp_name`, `cp_title`, `cp_detail`, `cp_gg`, `cp_categories`, 
            `cp_categories_down`, `cp_sale1`, `cp_url`, `cp_url_1`, `cp_url_2`, `cp_browse_node_1`, `cp_browse_node_2`, 
            `cp_helpword`, `cp_helpword_1`, cp_bullet_1, cp_bullet_2, cp_bullet_3, cp_bullet_4, cp_bullet_5, cp_bullet_6, `cp_bz`, `userID`)";
        $insert.=" VALUES ('".$serialID."',
            '".($results->bar_code+BARCODE_BASE_START)."',
            '"."【".$results->category."】"."・".$results->category_down."',
            '".$results->cp_title."',
            '"."対応機種：".$results->model."\n仕様：".$results->modelDetail.
            "\n".$results->cp_bullet_1."\n".$results->cp_bullet_2."\n".$results->cp_bullet_3.
            "\n".$results->cp_bullet_4."\n".$results->cp_bullet_5."\n".$results->cp_bullet_6."\n商品コード：".$serialID."',
            '".$results->des."',
            '".$results->category_id."',
            '".$results->category_down_id."', 
            '".$results->category_score * ($results->new_score+$results->year_score+$results->category_down_score)."',
            '".$results->url_prefix.strtolower($results->serial).".jpg"."',
            '".$results->url_prefix.strtolower($results->serial)."-1.jpg"."',
            '".$results->url_prefix.strtolower($results->serial)."-2.jpg"."',
            '".$results->browse_node."',
            '".$results->category_down_browse_node."',
            '".$results->model."',
            '".$results->modelDetail."',
            '".$results->cp_bullet_1."',
            '".$results->cp_bullet_2."',
            '".$results->cp_bullet_3."',
            '".$results->cp_bullet_4."',
            '".$results->cp_bullet_5."',
            '".$results->cp_bullet_6."',
            '".$results->remark."',
            '".$userID."')";
        $newsql->query($insert);
        
        $insert = "INSERT INTO `jxc_mainkc` (`p_id`, l_id, l_state1, l_state2, l_state3, l_state4, l_state5, l_state6, l_state7, l_state8, l_state9, l_state10)
            VALUES ('".$serialID."', '".$labid."', '".$l_state1."', '".$l_state2."', '".$l_state3."', '".$l_state4."', '".$l_state5."',
                '".$l_state6."', '".$l_state7."', '".$l_state8."', '".$l_state9."', '".$l_state10."')";
        $newsql->query($insert);
        
        return "[{\"current_id\":\"".$current_id."\",\"next_id\":\"".sprintf("%03d",$next_id)."\"}]";
    }
    
    function out_excel() {
        $sday = $_REQUEST["sday"];
        $eday = $_REQUEST["eday"];
        $obj = $_REQUEST["obj"];
        
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

        $sql = "select a.cp_tm,a.cp_number,a.cp_title, a.cp_detail, a.cp_gg, a.cp_helpword, a.cp_helpword_1, a.cp_bullet_1, a.cp_bullet_2, a.cp_bullet_3, a.cp_bullet_4, a.cp_bullet_5, a.cp_bullet_6,
            a.cp_bz,a.cp_url,a.cp_url_1,a.cp_url_2,a.cp_sale1, a.cp_browse_node_1, a.cp_browse_node_2,
            c.categories category, d.categories category_down, c.id category_id, d.id category_down_id,
            h.l_state1, h.l_state2, h.l_state3, h.l_state4, h.l_state5, h.l_state6, h.l_state7, h.l_state8, h.l_state9, h.l_state10
            from jxc_basic a, jxc_categories c, jxc_categories d, jxc_mainkc h
            where a.cp_categories = c.id and a.cp_categories_down = d.id and a.cp_number = h.p_id and a.cp_dtime>='".$sday."' and a.cp_dtime<= '".$eday."'";
        if($obj != "all") {
            $sql.= " and userID = {$obj}";
        }
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($sql);
        
        $workSheet->setCellValue('A1', "コントロール")
        ->setCellValue('B1', "商品コード")
        ->setCellValue('C1', "バーコード")
        ->setCellValue('D1', "親子関連")
        ->setCellValue('E1', "メーカ")
        ->setCellValue('F1', "タイトル")
        ->setCellValue('G1', "仕様")
        ->setCellValue('H1', "商品説明")
        ->setCellValue('I1', "箇条書き１")
        ->setCellValue('J1', "箇条書き２")
        ->setCellValue('K1', "箇条書き３")
        ->setCellValue('L1', "箇条書き４")
        ->setCellValue('M1', "箇条書き５")
        ->setCellValue('N1', "箇条書き６")
        ->setCellValue('O1', "商品分類")
        ->setCellValue('P1', "単位")
        ->setCellValue('Q1', "商品タイプ")
        ->setCellValue('R1', "仕入単価")
        ->setCellValue('S1', "メーカー希望卸売価格")
        ->setCellValue('T1', "販売価格")
        ->setCellValue('U1', "生産日付")
        ->setCellValue('V1', "廃棄日付")
        ->setCellValue('W1', "仕入先")
        ->setCellValue('X1', "メインURL")
        ->setCellValue('Y1', "サブURL1")
        ->setCellValue('Z1', "サブURL2")
        ->setCellValue('AA1', "サブURL3")
        ->setCellValue('AB1', "サブURL4")
        ->setCellValue('AC1', "推奨ブラウズノード1")
        ->setCellValue('AD1', "推奨ブラウズノード2")
        ->setCellValue('AE1', "キーワード1")
        ->setCellValue('AF1', "キーワード2")
        ->setCellValue('AG1', "キーワード3")
        ->setCellValue('AH1', "キーワード4")
        ->setCellValue('AI1', "キーワード5")
        ->setCellValue('AJ1', "キーワード6")
        ->setCellValue('AK1', "キーワード7")
        ->setCellValue('AL1', "キーワード8")
        ->setCellValue('AM1', "キーワード9")
        ->setCellValue('AN1', "キーワード10")
        ->setCellValue('AO1', "仓库号")
        ->setCellValue('AP1', "在库位置")
        ->setCellValue('AQ1', "在庫数")
        ->setCellValue('AR1', "状態1")
        ->setCellValue('AS1', "状態2")
        ->setCellValue('AT1', "状態3")
        ->setCellValue('AU1', "状態4")
        ->setCellValue('AV1', "状態5")
        ->setCellValue('AW1', "状態6")
        ->setCellValue('AX1', "状態7")
        ->setCellValue('AY1', "状態8")
        ->setCellValue('AZ1', "状態9")
        ->setCellValue('BA1', "状態10")
        ->setCellValue('BB1', "笔记")
        ->setCellValue('BC1', "備考");
        
        $i = 2;
        if($results) {
            foreach($results as $result) {
                $workSheet
                ->setCellValue('A'.$i, "n")
                ->setCellValue('B'.$i, $result->cp_number)
                ->setCellValue('C'.$i, $result->cp_tm)
                ->setCellValue('D'.$i, "")
                ->setCellValue('E'.$i, $result->category_down)
                //->setCellValue('F'.$i, $result->name." ".$result->category." ".$result->model." ".$result->modelDetail." ".$result->category_down)
                ->setCellValue('F'.$i, $result->cp_title)
                ->setCellValue('G'.$i, $result->cp_detail)
                ->setCellValue('H'.$i, $result->cp_gg)
                ->setCellValue('I'.$i, $result->cp_bullet_1)
                ->setCellValue('J'.$i, $result->cp_bullet_2)
                ->setCellValue('K'.$i, $result->cp_bullet_3)
                ->setCellValue('L'.$i, $result->cp_bullet_4)
                ->setCellValue('M'.$i, $result->cp_bullet_5)
                ->setCellValue('N'.$i, $result->cp_bullet_6)
                ->setCellValue('O'.$i, $result->category_id."/".$result->category_down_id)
                ->setCellValue('P'.$i, "個")
                ->setCellValue('Q'.$i, "正常販売商品")
                ->setCellValue('R'.$i, "")
                ->setCellValue('S'.$i, "")
                ->setCellValue('T'.$i, $result->cp_sale1)
                ->setCellValue('U'.$i, "20151230")
                ->setCellValue('V'.$i, "20151230")
                ->setCellValue('W'.$i, "")
                ->setCellValue('X'.$i, $result->cp_url)
                ->setCellValue('Y'.$i, $result->cp_url_1)
                ->setCellValue('Z'.$i, $result->cp_url_2)
                ->setCellValue('AA'.$i, "")
                ->setCellValue('AB'.$i, "")
                ->setCellValue('AC'.$i, $result->cp_browse_node_1)
                ->setCellValue('AD'.$i, $result->cp_browse_node_2)
                ->setCellValue('AE'.$i, $result->cp_helpword)
                ->setCellValue('AF'.$i, $result->cp_helpword_1)
                ->setCellValue('AG'.$i, $result->cp_helpword_2)
                ->setCellValue('AH'.$i, $result->cp_helpword_3)
                ->setCellValue('AI'.$i, $result->cp_helpword_4)
                ->setCellValue('AJ'.$i, $result->cp_helpword_5)
                ->setCellValue('AK'.$i, $result->cp_helpword_6)
                ->setCellValue('AL'.$i, $result->cp_helpword_7)
                ->setCellValue('AM'.$i, $result->cp_helpword_8)
                ->setCellValue('AN'.$i, $result->cp_helpword_9)
                ->setCellValue('AO'.$i, "")
                ->setCellValue('AP'.$i, "0-0-0-0-0")
                ->setCellValue('AQ'.$i, "1")
                ->setCellValue('AR'.$i, $result->l_state1)
                ->setCellValue('AS'.$i, $result->l_state2)
                ->setCellValue('AT'.$i, $result->l_state3)
                ->setCellValue('AU'.$i, $result->l_state4)
                ->setCellValue('AV'.$i, $result->l_state5)
                ->setCellValue('AW'.$i, $result->l_state6)
                ->setCellValue('AX'.$i, $result->l_state7)
                ->setCellValue('AY'.$i, $result->l_state8)
                ->setCellValue('AZ'.$i, $result->l_state9)
                ->setCellValue('BA'.$i, $result->l_state10)
                ->setCellValue('BB'.$i, "")
                ->setCellValue('BC'.$i, $result->cp_bz);
                $i++;
            }
        
        }
     
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('模板数据');
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $time = date("Ymd-H_i_s");
        header('Content-Disposition: attachment;filename="'.$time.'.xlsx"');
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