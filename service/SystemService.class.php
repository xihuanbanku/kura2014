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
        case "loadPriceParam":
            $result = loadPriceParam();
        break;
        case "loadStaticParam":
            $result = loadStaticParam();
        break;
        case "updateStaticParam":
            $result = updateStaticParam();
        break;
        case "submitPriceParam":
            $result = submitPriceParam();
        break;
        case "submitBuyingParam":
            $result = submitBuyingParam();
        break;
        case "loadDesContent":
            $result = loadDesContent();
        break;
        case "loadUrlPrefix":
            $result = loadUrlPrefix();
        break;
        case "submitSystemBuyingBeforeTime":
            $result = submitSystemBuyingBeforeTime();
        break;
        case "query4Count":
            $result = query4Count();
        break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function loadPriceParam() {
        $which = trim($_REQUEST["which"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "select p_name, p_value from jxc_static where p_type='".$which."' order by sort";
        $result = $newsql->get_results($sql);
        return json_encode($result);
    }
    
    function query4Count() {
        if(isset($_REQUEST["model"])) {
            $stext = trim($_REQUEST["model"]);
        } else if(isset($_REQUEST["modelDetail"])) {
            $stext = trim($_REQUEST["modelDetail"]);
        }
        
        $newsql = new ezSQL_mysql();
        $sql = "select count(0) from jxc_basic a where (a.cp_number LIKE '%" . $stext . "%'
                    or a.cp_title LIKE '%" . $stext . "%'
                    or a.cp_bz LIKE '%" . $stext . "%'
                    or a.cp_tm LIKE '%" . $stext . "%'
                    or a.cp_detail LIKE '%" . $stext . "%'
                    or a.cp_parent LIKE '%" . $stext . "%'
                    or a.cp_helpword LIKE '%" . $stext . "%'
                    or a.cp_helpword_1 LIKE '%" . $stext . "%'
                    or a.cp_helpword_2 LIKE '%" . $stext . "%'
                    or a.cp_helpword_3 LIKE '%" . $stext . "%'
                    or a.cp_helpword_4 LIKE '%" . $stext . "%'
                    or a.cp_helpword_5 LIKE '%" . $stext . "%'
                    or a.cp_helpword_6 LIKE '%" . $stext . "%'
                    or a.cp_helpword_7 LIKE '%" . $stext . "%'
                    or a.cp_helpword_8 LIKE '%" . $stext . "%'
                    or a.cp_helpword_9 LIKE '%" . $stext . "%')";
        if(!empty($_REQUEST["category"])) {
            $sql.=" and cp_categories = ".$_REQUEST["category"];
        }
        $result = $newsql->get_var($sql);
        return $result;
    }
    
    function loadDesContent() {
        $cp_categories = trim($_REQUEST["cp_categories"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "select name, price_1, price_2, price_3, price_4, price_5 from jxc_description where id='".$cp_categories."'";
        $result = $newsql->get_results($sql);
        return json_encode($result);
    }
    
    function loadUrlPrefix() {
        $category_id = trim($_REQUEST["category_id"]);
        $new_id = trim($_REQUEST["new_id"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "select name from jxc_url_prefix where category_id='".$category_id."' and new_id = '".$new_id."'";
        $result = $newsql->get_results($sql);
        return json_encode($result);
    }
    
    function loadStaticParam() {
        $p_type = trim($_REQUEST["p_type"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "select p_name, p_value from jxc_static where p_type='".$p_type."' order by sort";
        $result = $newsql->get_results($sql);
        return json_encode($result);
    }
    
    function updateStaticParam() {
        $p_type = trim($_REQUEST["p_type"]);
        $p_name = trim($_REQUEST["p_name"]);
        $p_value = trim($_REQUEST["p_value"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_static set p_value = '".$p_value."' where p_type='".$p_type."' and p_name = '".$p_name."'";
        $result = $newsql->query($sql);
        return json_encode($result);
    }
    function submitSystemBuyingBeforeTime() {
        $system_buying1_before_date = trim($_REQUEST["system_buying1_before_date"]);
        $system_buying1_before_hour = trim($_REQUEST["system_buying1_before_hour"]);
        
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_static set p_value = '".$system_buying1_before_date."' where  p_name = 'system_buying1_before_date'";
        $result = $newsql->query($sql);
        $sql = "update jxc_static set p_value = '".$system_buying1_before_hour."' where  p_name = 'system_buying1_before_hour'";
        $result = $newsql->query($sql);
        return json_encode($result);
    }
    function submitBuyingParam() {
        $system_kc_buying_param = trim($_REQUEST["system_kc_buying_param"]);

        $result=0;
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_static set p_value = '".$system_kc_buying_param."' where p_type='SYSTEM_KC_BUYING_PARAM' and p_name = 'system_kc_buying_param'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_mainkc set l_state12 = (case when (number - l_state9*{$system_kc_buying_param})<0 then round(number - l_state9*{$system_kc_buying_param}, 2) else 0 end)"; 
        $result += $newsql->query($sql);
        return $result;
    }
    
    function submitPriceParam() {
        $which = trim($_REQUEST["which"]);
        $price_from = trim($_REQUEST["price_from"]);
        $price_to = trim($_REQUEST["price_to"]);
        $price_1 = trim($_REQUEST["price_1"]);
        $price_2 = trim($_REQUEST["price_2"]);
        $price_3 = trim($_REQUEST["price_3"]);
        $price_4 = trim($_REQUEST["price_4"]);
        $price_5 = trim($_REQUEST["price_5"]);
        $price_6 = trim($_REQUEST["price_6"]);
        $price_7 = trim($_REQUEST["price_7"]);
        $price_8 = trim($_REQUEST["price_8"]);
        $price_9 = trim($_REQUEST["price_9"]);
        $price_10 = trim($_REQUEST["price_10"]);
        
        $result=0;
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_static set `p_value`='$price_from' where p_type='".$which."' and p_name = 'price_from'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_to' where p_type='".$which."' and p_name = 'price_to'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_1' where p_type='".$which."' and p_name = 'price_1'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_2' where p_type='".$which."' and p_name = 'price_2'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_3' where p_type='".$which."' and p_name = 'price_3'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_4' where p_type='".$which."' and p_name = 'price_4'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_5' where p_type='".$which."' and p_name = 'price_5'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_6' where p_type='".$which."' and p_name = 'price_6'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_7' where p_type='".$which."' and p_name = 'price_7'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_8' where p_type='".$which."' and p_name = 'price_8'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_9' where p_type='".$which."' and p_name = 'price_9'"; 
        $result += $newsql->query($sql);
        $sql = "update jxc_static set `p_value`='$price_10' where p_type='".$which."' and p_name = 'price_10'"; 
        $result += $newsql->query($sql);
        //根据价格区间更新状态10, 价格区间 price_from <= x < price_to
        $sql="update jxc_mainkc a, jxc_basic b set a.l_state10=b.cp_sale*$price_1*$price_2*$price_3*$price_4*$price_5*$price_6*$price_7*$price_8*$price_9*$price_10 
                where a.p_id=b.cp_number and b.cp_sale >=$price_from and  b.cp_sale <$price_to";
        $result += $newsql->query($sql);
        return $result;
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
        $category = $_REQUEST["cp_categories"];
        $category_down = $_REQUEST["cp_categories_down"];
        $serial = trim($_REQUEST["serial"]);
        $model = trim($_REQUEST["model"]);
        $modelDetail = trim($_REQUEST["modelDetail"]);
        $cp_bullet_1 = $_REQUEST["cp_bullet_1"];
        $cp_bullet_2 = $_REQUEST["cp_bullet_2"];
        $cp_bullet_3 = $_REQUEST["cp_bullet_3"];
        $cp_bullet_4 = $_REQUEST["cp_bullet_4"];
        $cp_bullet_5 = $_REQUEST["cp_bullet_5"];
        $cp_bullet_6 = $_REQUEST["cp_bullet_6"];
        $remark = trim($_REQUEST["remark"]);
        
        $newsql = new ezSQL_mysql();
        $result = $newsql->get_var("select max(REPLACE(serial, '{$serial}', '')+0) from jxc_basic_copy where serial like '{$serial}%'");
        preg_match("/\d+/", $result, $number);
        if($number) {
            $next_id = intval($number[0])+1;
        } else {
            $next_id = 1;
        }
        
        $serialID = $serial.sprintf("%03d",$next_id);
        $result = $newsql->query("insert into jxc_basic_copy(`new`, `year`, `category`, `category_down`, `serial`, `model`, `modelDetail`, `userID`, cp_bullet_1, cp_bullet_2, cp_bullet_3, cp_bullet_4, cp_bullet_5, cp_bullet_6, remark, lid)
            values('$new','$year','$category','$category_down','$serialID','$model','$modelDetail','$userID', '$cp_bullet_1', '$cp_bullet_2', '$cp_bullet_3', '$cp_bullet_4', '$cp_bullet_5', '$cp_bullet_6', '$remark','$labid')") or mysql_error();
        $jxc_basic_copy_id = mysql_insert_id();
        $sql = "select count(0) from jxc_basic where cp_number='".$serialID."'";
        $result = $newsql->get_var($sql);
        if($result > 0) {
            $sql = "update jxc_basic_copy set del_flag = 1 where serial='".$serialID."'";
            $result = $newsql->query($sql);
            return "error1";
        }
        
        $sql = "select a.bar_code,a.serial, a.model, a.modelDetail, a.cp_bullet_1, a.cp_bullet_2, a.cp_bullet_3, a.cp_bullet_4, a.cp_bullet_5, a.cp_bullet_6,
            a.remark, f.name url_prefix, g.name des, b.`name`, b.`score` new_score, b.`name` new_name, e.`score` year_score,
            c.categories category, d.categories category_down, c.id category_id, d.id category_down_id, c.score category_score, d.score category_down_score,
            c.browse_node browse_node, d.browse_node category_down_browse_node, c.addword addword, d.addword category_down_addword
            from jxc_basic_copy a, jxc_new b, jxc_categories c, jxc_categories d, jxc_year e, jxc_url_prefix f, jxc_description g
            where a.new = b.id and a.category = c.id  and  a.category = f.category_id and a.new = f.new_id  and a.category = g.id and a.category_down = d.id and a.year = e.id and a.serial='{$serialID}'";
        $results = $newsql->get_row($sql);
        $insert = "INSERT INTO `jxc_basic` (`cp_number`, `cp_tm`, `cp_name`, `cp_title`, `cp_detail`, `cp_gg`, `cp_categories`, 
            `cp_categories_down`, `cp_sale1`, `cp_url`, `cp_url_1`, `cp_url_2`, `cp_browse_node_1`, `cp_browse_node_2`, 
            `cp_helpword`, `cp_helpword_1`, cp_bullet_1, cp_bullet_2, cp_bullet_3, cp_bullet_4, cp_bullet_5, cp_bullet_6, `cp_bz`)";
        $insert.=" VALUES ('".$serialID."',
            '".($results->bar_code+BARCODE_BASE_START)."',
            '"."【".$results->category."】"."・".$results->category_down."',
            '".$results->category_down." ".$results->model." ".$results->modelDetail." ".$results->new_name." ".$results->category."',
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
            '".$results->remark."')";
        $newsql->query($insert);
        
        $insert = "INSERT INTO `jxc_mainkc` (`p_id`, l_id, l_state1, l_state2, l_state3, l_state4, l_state5, l_state6, l_state7, l_state8, l_state9, l_state10)
            VALUES ('".$serialID."', '".$labid."', '".$l_state1."', '".$l_state2."', '".$l_state3."', '".$l_state4."', '".$l_state5."',
                '".$l_state6."', '".$l_state7."', '".$l_state8."', '".$l_state9."', '".$l_state10."')";
        $newsql->query($insert);
        
        return "[{\"jxc_basic_copy_id\":\"".$jxc_basic_copy_id."\",\"next_id\":\"".sprintf("%03d",$next_id)."\"}]";
         
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
        
        //初始化case when语句,为之后的查询做准备
        $sql = "select id, price_1, price_2, price_3, price_4, price_5 from jxc_description";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($sql);
        $casewhen = " case c.id";
        if($results) {
            foreach($results as $result) {
                $casewhen .= " when ".$result->id." then concat (".$result->price_1.",' ', ".$result->price_2.",' ', ".$result->price_3.",' ', ".$result->price_4.",' ', ".$result->price_5.")";
            }
        }
        $casewhen .= " end title";
        // Add some data
        $workSheet = $objPHPExcel->setActiveSheetIndex(0);

        $sql = "select a.bar_code,a.serial, a.model, a.modelDetail, a.cp_bullet_1, a.cp_bullet_2, a.cp_bullet_3, a.cp_bullet_4, a.cp_bullet_5, a.cp_bullet_6,
            a.remark, f.name url_prefix, g.name des, b.`name`, b.`score` new_score, e.`score` year_score,
            h.l_state1, h.l_state2, h.l_state3, h.l_state4, h.l_state5, h.l_state6, h.l_state7, h.l_state8, h.l_state9, h.l_state10,
            c.categories category, d.categories category_down, c.id category_id, d.id category_down_id, c.score category_score, d.score category_down_score,
            c.browse_node browse_node, d.browse_node category_down_browse_node, c.addword addword, d.addword category_down_addword, ".$casewhen."
            from jxc_basic_copy a, jxc_new b, jxc_categories c, jxc_categories d, jxc_year e, jxc_url_prefix f, jxc_description g, jxc_mainkc h
            where a.new = b.id and a.category = c.id and a.category = g.id  and a.category = f.category_id and a.new = f.new_id and a.category_down = d.id and a.year = e.id and a.serial = h.p_id and a.del_flag = 0 and a.dtime>='".$sday."' and a.dtime<= '".$eday."'";
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
                ->setCellValue('B'.$i, $result->serial)
                ->setCellValue('C'.$i, $result->bar_code+BARCODE_BASE_START)
                ->setCellValue('D'.$i, "")
                ->setCellValue('E'.$i, $result->category_down)
                //->setCellValue('F'.$i, $result->name." ".$result->category." ".$result->model." ".$result->modelDetail." ".$result->category_down)
                ->setCellValue('F'.$i, $result->title)
                ->setCellValue('G'.$i, "対応機種：".$result->model."\n仕様：".$result->modelDetail.
					"\n".$result->cp_bullet_1."\n".$result->cp_bullet_2."\n".$result->cp_bullet_3.
					"\n".$result->cp_bullet_4."\n".$result->cp_bullet_5."\n".$result->cp_bullet_6)
                ->setCellValue('H'.$i, $result->des)
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
                ->setCellValue('T'.$i, $result->category_score * ($result->new_score+$result->year_score+$result->category_down_score))
                ->setCellValue('U'.$i, "20151230")
                ->setCellValue('V'.$i, "20151230")
                ->setCellValue('W'.$i, "")
                ->setCellValue('X'.$i, $result->url_prefix.strtolower($result->serial).".jpg")
                ->setCellValue('Y'.$i, $result->url_prefix.strtolower($result->serial)."-1.jpg")
                ->setCellValue('Z'.$i, $result->url_prefix.strtolower($result->serial)."-2.jpg")
                ->setCellValue('AA'.$i, "")
                ->setCellValue('AB'.$i, "")
                ->setCellValue('AC'.$i, $result->browse_node)
                ->setCellValue('AD'.$i, $result->category_down_browse_node)
                ->setCellValue('AE'.$i, $result->model)
                ->setCellValue('AF'.$i, $result->modelDetail)
                ->setCellValue('AG'.$i, "")
                ->setCellValue('AH'.$i, "")
                ->setCellValue('AI'.$i, "")
                ->setCellValue('AJ'.$i, "")
                ->setCellValue('AK'.$i, "")
                ->setCellValue('AL'.$i, "")
                ->setCellValue('AM'.$i, "")
                ->setCellValue('AN'.$i, "")
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
                ->setCellValue('BC'.$i, $result->remark);
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