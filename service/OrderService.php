<?php
/** Include PHPExcel */
require_once '../PHPExcel.php';
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';

$orderService = new OrderService();
$result = "";
if(isset($_REQUEST["flag"])) {
    switch ($_REQUEST["flag"]) {
        case "insert":
            $result = $orderService->insert();
        break;
        case "out_excel":
            $result = $orderService->out_excel(0);
        break;
        case "out_excel1":
            $result = $orderService->out_excel(1);
        break;
        case "filenames":
            $result = $orderService->filenames();
        break;
        
        default:
            return "error";
        break;
    }
}
echo $result;
class OrderService {
        
    function filenames() {
        $f_sday = $_REQUEST["f_sday"];
        $f_eday = $_REQUEST["f_eday"];
        
        $nsql= new ezSQL_mysql();
        $notesql="select distinct filename from `jxc_orders` a
            where a.dtime>='".$f_sday."' and a.dtime<= '".$f_eday."' order by filename desc";
        $results = $nsql->get_results($notesql);
        echo mysql_error();
        return json_encode($results);
    }
    
    function uploadFile($file, $filetempname) {
        $importStat=array("n"=>0,"u"=>0,"d"=>0,"e"=>0,"m"=>0,"filename"=>0);
        // 自己设置的上传文件存放路径
        $filePath = '/home/p-mon/tousho.co.jp/public_html/kura2014/upload/';
        $str = "";
        
        // 下面的路径按照你PHPExcel的路径来修改
        set_include_path('/home/p-mon/tousho.co.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());
        
        date_default_timezone_set("PRC");
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
            for ($j = 2; $j <= $highestRow; $j ++) {
                $strs = array();
                for ($k = 0; $k < $highestColumn; $k ++) {
                    $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                    $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName$j")->getValue();
    //                 echo "$columnName$j"."----".$cellValue."----";
                    array_push($strs, $cellValue); // 读取单元格
                }

                $index=0;
                $notesql="INSERT INTO `jxc_orders` (`order_id`, `o_status`, `o_time`, `cp_id`, `cp_name`, `number`, `price`, `pay_method`, 
                    `require_amount`, `postcode1`, `postcode2`, `address1`, `address2`, `address3`, `name1`, `name2`, `phone1`, 
                    `phone2`, `phone3`, `send_time`, `time_zone`, `specify_date`, `baggage_num`, `delivery_company`, 
                    `delivery_year`, `delivery_month`, `delivery_day`, `delivery_time`, `delivery_type`, filename)
                    VALUES  ('".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])
                ."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])
                ."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])
                ."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])
                ."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])
                ."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".trim($strs[$index++])."','".$time."')";
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
        
    function out_excel($flag) {
        $where = "";
        $and = "";
        if($flag==0) {
            $sday = $_REQUEST["sday"];
            $eday = $_REQUEST["eday"];
            $where = "where a.dtime>='".$sday."' and a.dtime<= '".$eday."'";
            $and = "and a.dtime>='".$sday."' and a.dtime<= '".$eday."'";
        } else if($flag==1) {
            $filename = $_REQUEST["filename"];
            $where = "where a.filename= '".$filename."'";
            $and = "and a.filename= '".$filename."'";
        }
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

        $sql = "SELECT `order_id`, `o_status`, `o_time`, `cp_id`, `cp_name`, `number`, `price`, 
            case `pay_method` when '代金引換' then 2 else 0 end pay_method, case `pay_method` when '代金引換' then require_amount else 0 end require_amount, `postcode1`, `postcode2`, 
            `address1`, `address2`, `address3`, `name1`, `name2`, `phone1`, `phone2`, `phone3`, `send_time`, `time_zone`,
            `specify_date`, `baggage_num`, `delivery_company`, `delivery_year`, `delivery_month`, `delivery_day`, `delivery_time`, 
            `delivery_type` FROM `jxc_orders` a ". $where;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($sql);
        
        $workSheet
        ->setCellValue('A1',  "お客様管理番号\n半角英数字20文字")
        ->setCellValue('B1',  "送り状種類\n半角数字1文字\n 0 : 発払い\n 2 : コレクト\n 3 : ＤＭ便\n 4 : タイムサービス\n 5 : 着払い\n 6 : メール便速達\n 7 : ネコポス\n 8 : 宅急便コンパクト\n\n(※宅急便_必須項目)\n(※ＤＭ便_必須項目)")
        ->setCellValue('C1',  "クール区分\n半角数字1文字\n0または空白 : 通常\n 1 : クール冷凍\n 2 : クール冷蔵\n\n※タイムサービスの場合は通常")
        ->setCellValue('D1',  "伝票番号\n半角数字12文字\n荷主採番のみ必要")
        ->setCellValue('E1',  "出荷予定日\n半角10文字\n｢YYYY/MM/DD｣で入力してください。\n\n(※宅急便_必須項目)\n(※ＤＭ便_必須項目)")
        ->setCellValue('F1',  "お届け予定日\n半角10文字\n｢YYYY/MM/DD｣で入力してください。\n※入力なしの場合、印字されません。")
        ->setCellValue('G1',  "配達時間帯\n半角4文字\n発払い、コレクト、着払い、宅急便コンパクト\n空白 : 指定なし\n0812 : 午前中\n1214 : 12～14時\n1416 : 14～16時\n1618 : 16～18時\n1820 : 18～20時\n2021 : 20～21時\nタイムのみ\n0010 : 午前10時まで\n0017 : 午後5時まで")
        ->setCellValue('H1',  "お届け先コード\n半角英数字20文字")
        ->setCellValue('I1',  "お届け先電話番号\n半角数字15文字ハイフン含む\n\n(※宅急便_必須項目)\n(※ＤＭ便_必須項目)")
        ->setCellValue('J1',  "お届け先電話番号枝番\n半角数字2文字")
        ->setCellValue('K1',  "お届け先郵便番号\n半角数字8文字\nハイフンなし7文字も可\n\n(※宅急便_必須項目)\n(※ＤＭ便_必須項目)")
        ->setCellValue('L1',  "お届け先住所\n全角/半角\n都道府県（４文字）\n市区郡町村（１２文字）\n町・番地（１６文字）\n\n(※宅急便_必須項目)\n(※ＤＭ便_必須項目)")
        ->setCellValue('M1',  "お届け先アパートマンション名\n全角/半角 \n16文字/32文字 ")
        ->setCellValue('N1',  "お届け先会社・部門１\n全角/半角\n25文字/50文字 ")
        ->setCellValue('O1',  "お届け先会社・部門２\n全角/半角 \n25文字/50文字 ")
        ->setCellValue('P1',  "お届け先名\n全角/半角\n16文字/32文字 \n\n(※宅急便_必須項目)\n(※ＤＭ便_必須項目)")
        ->setCellValue('Q1',  "お届け先名(ｶﾅ)\n半角カタカナ 50文字 ")
        ->setCellValue('R1',  "敬称\n全角/半角 2文字/4文字\nＤＭ便のみ指定可能\n【入力例】\n様・御中・殿・行・係・宛先・先生 ")
        ->setCellValue('S1',  "ご依頼主コード\n半角英数字 20文字 ")
        ->setCellValue('T1',  "ご依頼主電話番号\n半角数字15文字ハイフン含む\n\n(※宅急便_必須項目)\n")
        ->setCellValue('U1',  "ご依頼主電話番号枝番\n半角数字 2文字 ")
        ->setCellValue('V1',  "ご依頼主郵便番号\n半角数字8文字\nハイフンなし半角7文字も可 \n\n(※宅急便_必須項目)\n")
        ->setCellValue('W1',  "ご依頼主住所\n全角/半角32文字/64文字\n都道府県（４文字）\n市区郡町村（１２文字）\n町・番地（１６文字）\n\n(※宅急便_必須項目)")
        ->setCellValue('X1',  "ご依頼主アパートマンション\n全角/半角 16文字/32文字 ")
        ->setCellValue('Y1',  "ご依頼主名\n全角/半角 16文字/32文字 \n\n(※宅急便_必須項目)")
        ->setCellValue('Z1',  "ご依頼主名(ｶﾅ)\n半角カタカナ 50文字")
        ->setCellValue('AA1', "品名コード１\n半角英数字 30文字 ")
        ->setCellValue('AB1', "品名１\n全角/半角 25文字/50文字 \n\n(※宅急便_必須項目)")
        ->setCellValue('AC1', "品名コード２\n半角英数字 30文字")
        ->setCellValue('AD1', "品名２\n全角/半角 25文字/50文字 ")
        ->setCellValue('AE1', "荷扱い１\n全角/半角 10文字/20文字 ")
        ->setCellValue('AF1', "荷扱い２\n全角/半角 10文字/20文字 ")
        ->setCellValue('AG1', "記事\n全角/半角 16文字/32文字 ")
        ->setCellValue('AH1', "ｺﾚｸﾄ代金引換額（税込)\n半角数字 7文字\n\n送り状種類がコレクトの場合は必須\n300,000円以下　1円以上")
        ->setCellValue('AI1', "内消費税額等\n半角数字 7文字\n\n送り状種類がコレクトの場合は必須 \nコレクト代金引換額（税込)以下")
        ->setCellValue('AJ1', "止置き\n半角数字 1文字\n0 : 利用しない\n1 : 利用する ")
        ->setCellValue('AK1', "営業所コード\n半角数字 6文字\n\n止置きを利用する場合は必須 ")
        ->setCellValue('AL1', "発行枚数\n半角数字 2文字\n発払い・タイムのみ指定可能")
        ->setCellValue('AM1', "個数口表示フラグ\n半角数字 1文字\n1 : 印字する\n2 : 印字しない\n\n※宅急便コンパクトは対象外")
        ->setCellValue('AN1', "請求先顧客コード\n半角数字12文字\n\n(※宅急便_必須項目)")
        ->setCellValue('AO1', "請求先分類コード\n空白または半角数字3文字\n\n")
        ->setCellValue('AP1', "運賃管理番号\n半角数字2文字\n\n(※宅急便_必須項目)")
        ->setCellValue('AQ1', "注文時カード払いデータ登録\n半角数字 1文字\n0 : 無し\n1 : 有り ")
        ->setCellValue('AR1', "注文時カード払い加盟店番号\n半角英数字 9文字 \n\n注文時カード払いデータ有りの場合は必須 ")
        ->setCellValue('AS1', "注文時カード払い申込受付番号１\n半角英数字 23文字\n\n注文時カード払いデータ有りの場合は必須 ")
        ->setCellValue('AT1', "注文時カード払い申込受付番号２\n半角英数字 23文字")
        ->setCellValue('AU1', "注文時カード払い申込受付番号３\n半角英数字 23文字")
        ->setCellValue('AV1', "お届け予定ｅメール利用区分\n半角数字 1文字\n0 : 利用しない\n1 : 利用する ")
        ->setCellValue('AW1', "お届け予定ｅメールe-mailアドレス\n半角英数字＆記号 60文字\n\nお届け予定eメールを利用する場合は必須 ")
        ->setCellValue('AX1', "入力機種\n半角数字 1文字\n1 : ＰＣ\n2 : 携帯電話\n\nお届け予定eメールを利用する場合は必須")
        ->setCellValue('AY1', "お届け予定ｅメールメッセージ\n全角 74文字\n\n\nお届け予定eメールを利用する場合は必須")
        ->setCellValue('AZ1', "お届け完了ｅメール利用区分\n半角数字 1文字\n0 : 利用しない\n1 : 利用する ")
        ->setCellValue('BA1', "お届け完了ｅメールe-mailアドレス\n半角英数字 60文字\n\nお届け完了eメールを利用する場合は必須 ")
        ->setCellValue('BB1', "お届け完了ｅメールメッセージ\n全角 159文字 \n\nお届け完了eメールを利用する場合は必須 ")
        ->setCellValue('BC1', "収納代行利用区分\n半角数字 １文字\n0 : 無し\n1 : 有り ")
        ->setCellValue('BD1', "予備	")
        ->setCellValue('BE1', "収納代行請求金額(税込)\n半角数字 ７文字\n\n収納代行を利用する場合は必須\n300,000円以下　1円以上 ")
        ->setCellValue('BF1', "収納代行内消費税額等\n半角数字 ７文字\n\n収納代行を利用する場合は必須\n収納代行請求金額以下 ")
        ->setCellValue('BG1', "収納代行請求先郵便番号\n半角数字8文字\n\n収納代行を利用する場合は必須\nハイフンなし半角7文字も可 ")
        ->setCellValue('BH1', "収納代行請求先住所\n全角/半角32文字/64文字 \n都道府県（４文字）\n市区郡町村（１２文字）\n町・番地（１６文字） \n\n収納代行を利用する場合は必須")
        ->setCellValue('BI1', "収納代行請求先アパートマンション\n全角/半角 16文字/32文字 ")
        ->setCellValue('BJ1', "収納代行請求先会社・部門名１\n全角/半角 25文字/50文字 ")
        ->setCellValue('BK1', "収納代行請求先会社・部門名２\n全角/半角 25文字/50文字 ")
        ->setCellValue('BL1', "収納代行請求先名(漢字)\n全角/半角 16文字/32文字\n\n収納代行を利用する場合は必須 ")
        ->setCellValue('BM1', "収納代行請求先名(カナ)\n半角カタカナ 50文字\n \n収納代行を利用する場合は必須 ")
        ->setCellValue('BN1', "収納代行問合せ先名(漢字)\n全角/半角 16文字/32文字\n\n収納代行を利用する場合は必須 ")
        ->setCellValue('BO1', "収納代行問合せ先郵便番号\n半角数字8文字\n\n収納代行を利用する場合は必須\nハイフンなし半角7文字も可 ")
        ->setCellValue('BP1', "収納代行問合せ先住所\n全角/半角 32文字/64文字 \n都道府県（４文字）\n市区郡町村（１２文字）\n町・番地（１６文字）\n\n収納代行を利用する場合は必須")
        ->setCellValue('BQ1', "収納代行問合せ先アパートマンション\n全角/半角 16文字/32文字 ")
        ->setCellValue('BR1', "収納代行問合せ先電話番号\n半角数字15文字\n\n収納代行を利用する場合は必須\nハイフン含む ")
        ->setCellValue('BS1', "収納代行管理番号\n半角英数字 20文字")
        ->setCellValue('BT1', "収納代行品名\n全角/半角 25文字/50文字")
        ->setCellValue('BU1', "収納代行備考\n全角/半角 14文字/28文字")
        ->setCellValue('BV1', "予備０１")
        ->setCellValue('BW1', "予備０２")
        ->setCellValue('BX1', "予備０３")
        ->setCellValue('BY1', "予備０４")
        ->setCellValue('BZ1', "予備０５")
        ->setCellValue('CA1', "予備０６")
        ->setCellValue('CB1', "予備０７")
        ->setCellValue('CC1', "予備０８")
        ->setCellValue('CD1', "予備０９")
        ->setCellValue('CE1', "予備１０")
        ->setCellValue('CF1', "予備１１")
        ->setCellValue('CG1', "予備１２")
        ->setCellValue('CH1', "予備１３")
        ->setCellValue('CI1', "投函予定メール利用区分\n半角1文字\n 0 : 利用しない\n 1 : 利用する PC宛て\n 2 : 利用する モバイル宛て\n\n投函予定メールを利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CJ1', "投函予定メールe-mailアドレス\n半角60文字\n\n投函予定メールを利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CK1', "投函予定メールメッセージ\n全角74文字\n\n投函予定メールを利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CL1', "投函完了メール(お届け先宛)利用区分\n半角1文字\n 0 : 利用しない\n 1 : 利用する PC宛て\n 2 : 利用する モバイル宛て\n\n投函完了メール（受人宛て）を利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CM1', "投函完了メール(お届け先宛)e-mailアドレス\n半角60文字\n\n投函完了メール（受人宛て）を利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CN1', "投函完了メール(お届け先宛)メッセージ\n全角159文字\n\n投函完了メール（受人宛て）を利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CO1', "投函完了メール(ご依頼主宛)利用区分\n半角1文字\n 0 : 利用しない\n 1 : 利用する PC宛て\n 2 : 利用する モバイル宛て\n\n投函完了メール（出人宛て）を利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CP1', "投函完了メール(ご依頼主宛)e-mailアドレス\n半角60文字\n\n投函完了メール（出人宛て）を利用する場合は必須(ネコポスのみ)")
        ->setCellValue('CQ1', "投函完了メール(ご依頼主宛)メッセージ\n全角159文字\n\n投函完了メール（出人宛て）を利用する場合は必須(ネコポスのみ)");
        
        $i = 2;
        if($results) {
            foreach($results as $result) {
                $workSheet
                ->setCellValue('B'.$i, $result->pay_method)
                ->setCellValue('E'.$i, date("Y/m/d"))
                ->setCellValueExplicit('I'.$i, $result->phone1.$result->phone2.$result->phone3, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit('K'.$i, $result->postcode1.$result->postcode2, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('L'.$i, $result->address1)
                ->setCellValue('M'.$i, $result->address2)
                ->setCellValue('N'.$i, $result->address3)
                ->setCellValue('P'.$i, $result->name1.$result->name2)
                ->setCellValue('T'.$i, "048-779-8159")
                ->setCellValue('V'.$i, "3600816")
                ->setCellValue('W'.$i, "埼玉県熊谷市石原3丁目267番地")
                ->setCellValue('X'.$i, "持田ビル1F")
                ->setCellValue('Y'.$i, "イーライズ")
                ->setCellValue('AB'.$i, "PC　パーツ")
                ->setCellValue('AH'.$i, $result->require_amount)
                ->setCellValueExplicit('AN'.$i, "0485949956", PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValueExplicit('AP'.$i, "01", PHPExcel_Cell_DataType::TYPE_STRING);
                $i++;
            }
        
        }
     
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('ヤマト用');
        
        $objPHPExcel->createSheet();
        // Add some data
        $workSheet = $objPHPExcel->setActiveSheetIndex(1);

        $sql = "SELECT `order_id`, `o_status`, `o_time`, `cp_id`, `cp_name`, a.`number` anumber, `price`, 
            pay_method, case `pay_method` when '代金引換' then require_amount else 0 end require_amount, `postcode1`, `postcode2`, 
            `address1`, `address2`, `address3`, `name1`, `name2`, `phone1`, `phone2`, `phone3`, `send_time`, `time_zone`,
            `specify_date`, `baggage_num`, `delivery_company`, `delivery_year`, `delivery_month`, `delivery_day`, `delivery_time`, 
            `delivery_type`, b.number bnumber, b.l_floor, b.l_shelf, b.l_zone, b.l_horizontal, b.l_vertical 
            FROM `jxc_orders` a, jxc_mainkc b where a.cp_id = b.p_id ". $and;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($sql);
        $workSheet
        ->mergeCells('F1:L1')
        ->setCellValue('F1',  "検　　品　　表")
        ->setCellValue('A2',  "※塗りつぶし部分が手書きでチェック")
        ->mergeCells('N2:O2')
        ->setCellValue('N2',  "検品日")
        ->mergeCells('P2:Q2')
        ->setCellValue('P2',  date("Y/m/d"))
        ->mergeCells('N3:O3')
        ->setCellValue('N3',  "サイト名")
        ->mergeCells('P3:Q3')

        ->setCellValue('A5',  "受注番号/注文番号")
        ->setCellValue('B5',  "お客様名")
        ->setCellValue('C5',  "商品コード")
        ->setCellValue('D5',  "出庫確認")
        ->setCellValue('E5',  "出荷確認")
        ->setCellValue('F5',  "お支払について")
        ->mergeCells('G5:H5')
        ->setCellValue('G5',  "代引金額")
        ->setCellValue('I5',  "注文数")
        ->setCellValue('J5',  "在庫数")
        ->setCellValue('K5',  "在庫位置")
        ->setCellValue('L5',  "送り状番号")
        ->setCellValue('M5',  "伝票確認")
        ->setCellValue('N5',  "出荷済")
        ->setCellValue('O5',  "発送確認")
        ->setCellValue('P5',  "返品/交換")
        ->setCellValue('Q5',  "発送中止");
        
        $i = 6;
        if($results) {
            foreach($results as $result) {
                $workSheet
                ->setCellValue('A'.$i, $result->order_id)
                ->setCellValue('B'.$i, $result->name1.$result->name2)
                ->setCellValue('C'.$i, $result->cp_id)
                ->setCellValue('D'.$i, "□")
                ->setCellValue('E'.$i, "□")
                ->setCellValue('F'.$i, $result->pay_method)
                ->mergeCells('G'.$i.':H'.$i)
                ->setCellValue('G'.$i, $result->require_amount)
                ->setCellValue('I'.$i, "")
                ->setCellValue('J'.$i, $result->bnumber)
                ->setCellValue('K'.$i, $result->l_floor.'-'.$result->l_shelf.'-'.$result->l_zone.'-'.$result->l_horizontal.'-'.$result->l_vertical)
                ->setCellValue('L'.$i, "")
                ->setCellValue('M'.$i, "□")
                ->setCellValue('N'.$i, "□")
                ->setCellValue('O'.$i, "□")
                ->setCellValue('P'.$i, "")
                ->setCellValue('Q'.$i, "□");
                $i++;
            }
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('検品表');
        
        
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
        $objWriter->save('php://output');
        exit;
    }
}
?>  