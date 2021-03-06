<?php
require_once "include/config_passport.php";
require_once "include/config.php";
require_once "include/config_base.php";
require_once "include/inc_functions.php";

$query="select * from #@__basic";

$cp_sdate = $_REQUEST["cp_sdate"];
$cp_edate = $_REQUEST["cp_edate"];
$cp_sdate = $_REQUEST["cp_sdate"];
$strChk = $_REQUEST["strChk"];
$shop = $_REQUEST["shop"];

// 日付範囲
if ($cp_sdate != '' and $cp_edate != '' ) {
    $query .= " where DATE(cp_dtime) >= cast('".$cp_sdate."' as date) and DATE(cp_dtime) <= cast('".$cp_edate."' as date)";
} else if ($cp_sdate != '' and $cp_edate == '' ) {
    $query .= " where DATE(cp_dtime) >= cast('".$cp_sdate."' as date)";
} else if ($cp_sdate == '' and $cp_edate != '' ) {
    $query .= " where DATE(cp_dtime) <= cast('".$cp_edate."' as date)";
}
// チェックボックス選択
if ($cp_sdate != '' or $cp_edate != '' ) {
    if ($strChk != '') {
        $query .= " and cp_number in (".$strChk.")";
    }
} else {
    if ($strChk != '') {
        $query .= " where cp_number in (".$strChk.")";
    }
}


$row=New Dedesql(false);
$row->setquery($query);
$row->execute();

if($shop == "rakuten") {
    //楽天用CSVファイル作成
    $array = array("コントロールカラム","商品管理番号（商品URL）","商品番号","全商品ディレクトリID","タグID","PC用キャッチコピー","モバイル用キャッチコピー",
        "商品名","販売価格","表示価格","消費税","送料","個別送料","送料区分1","送料区分2","代引料","倉庫指定","商品情報レイアウト","注文ボタン","資料請求ボタン",
            "商品問い合わせボタン","再入荷お知らせボタン","モバイル表示","のし対応","PC用商品説明文","モバイル用商品説明文","スマートフォン用商品説明文",
            "PC用販売説明文","商品画像URL","商品画像名（ALT）","動画","販売期間指定","注文受付数","在庫タイプ","在庫数","在庫数表示","項目選択肢別在庫用横軸項目名",
            "項目選択肢別在庫用縦軸項目名","項目選択肢別在庫用残り表示閾値","RAC番号","サーチ非表示","闇市パスワード","カタログID","在庫戻しフラグ","在庫切れ時の注文受付",
            "在庫あり時納期管理番号","在庫切れ時納期管理番号","予約商品発売日","ポイント変倍率","ポイント変倍率適用期間","ヘッダー・フッター・レフトナビ",
            "表示項目の並び順","共通説明文（小）","目玉商品","共通説明文（大）","レビュー本文表示","サイズ表リンク");
    for ($i=0;$i<count($array); $i++) {
        $csv_data .= iconv("utf-8", "Shift_jis", $array[$i]).",";
    }
    $csv_data.="\n";
    $comma = ",";
    $str1 = iconv("utf-8", "Shift_jis",'<table width="700" bgcolor="000000"  cellspacing="1" cellpadding="3">'
            . '<tr><th colspan="2" align="center" bgcolor="DCDCDC"><b><font color="000000">商品説明</font></b></th></tr>'
            . '<tr><th align="left" bgcolor="DCDCDC" width="18%"><font size="2" color="000000">管理番号</font></th><td bgcolor="ffffff">'
            . '<font size="2" color="000000">');
    $str2 = iconv("utf-8", "Shift_jis",'</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">メーカー名</font></th>'
            . '<td bgcolor="ffffff"><font size="2" color="000000">');
    $str3 = iconv("utf-8", "Shift_jis",'</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">製品仕様</font></th>'
            . '<td bgcolor="ffffff"><font size="2" color="000000">');
    $str4 = iconv("utf-8", "Shift_jis",'</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">バッテリーの型番</font></th>'
            . '<td bgcolor="ffffff"><font size="2" color="000000">');
    $str5 = iconv("utf-8", "Shift_jis",'</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">パソコンの型番</font></th>'
            . '<td bgcolor="ffffff"><font size="2" color="000000">');
    $str6 = iconv("utf-8", "Shift_jis",'</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">弊社の安心ポイント</font></th>'
            . '<td bgcolor="ffffff"><font size="2" color="000000">'
            . '★代引き、コンビニ受取&支払いなどにも対応しています。<br>'
            . '★産地品質検査センター設置しており、100％品質保証対応でございます。<br>'
            . '★日本国内本社でお客様対応しております。まとめ買い等もお気軽にご相談ください。<br>'
            . '★ご希望の商品と当方の商品イメージは一致することをご確認下さい。<br>'
            . '★ご不明がございましたら、ご問い合わせ下さい。<br>'
            . '★一般的にバルク品とは、メーカー保証書や説明書が付属されていない簡易包装の商品となります。<br>'
            . '★安心なPSE規格製品を販売しております。<br>'
            . '★100％で、1ヶ月保証付きます。<br>'
            . '★製品には過充電過放電との保護回路付き。<br>'
            . '★製品不良の場合は30日以内交換しております。<br>'
            . '</font></td></tr></table>');
    $constBattery = iconv("utf-8", "Shift_jis",'【対応バッテリーの品番】');
    $constComputer = iconv("utf-8", "Shift_jis",'【対応パソコンの型番】');
    $constGoodcode = iconv("utf-8", "Shift_jis",'商品コード');
//     $objExcel->getActiveSheet()->setCellValue('A1', "123");
     while($rs=$row->getArray()){
         for ($i=1; $i<=57; $i++) {
             switch($i){
                 case "1":
                     //コントロールカラム
                     $csv_data .= "n".$comma;
                     break;
                 case "3":
                     //商品番号
                     $csv_data .= $rs['cp_number'].$comma;
                     break;
                 case "8":
                     //商品名
                     $csv_data .= strRepacreBrToSpace(iconv("utf-8", "Shift_jis", $rs['cp_title'])).$comma;
                     break;
                 case "9":
                     //販売価格
                     $csv_data .= $rs['cp_sale1'].$comma;
                     break;
                 case "11":
                     //消費税
                     $csv_data .= "1".$comma;
                     break;
                 case "12":
                     //送料
                     $csv_data .= "0".$comma;
                     break;
                 case "16":
                     //代引料
                     $csv_data .= "0".$comma;
                     break;
                 case "17":
                     //倉庫指定
                     $csv_data .= "0".$comma;
                     break;
                 case "18":
                     //商品情報レイアウト
                     $csv_data .= "1".$comma;
                     break;
                 case "19":
                     //注文ボタン
                     $csv_data .= "1".$comma;
                     break;
                 case "20":
                     //資料請求ボタン
                     $csv_data .= "0".$comma;
                     break;
                 case "21":
                     //商品問い合わせボタン
                     $csv_data .= "1".$comma;
                     break;
                 case "22":
                     //再入荷お知らせボタン
                     $csv_data .= "0".$comma;
                     break;
                 case "23":
                     //モバイル表示
                     $csv_data .= "1".$comma;
                     break;
                 case "24":
                     //のし対応
                     $csv_data .= "0".$comma;
                     break;
                 case "25":
                     //PC用商品説明文
                 case "27":
                     $battery = '';
                     $computer = '';
                     $detail = $rs['cp_detail'];
                  
                     if ($detail != "") {
                         //メーカー名取得
                         $brands = explode("・", $rs['cp_name']);
                         $brand = "";
                         $maker = "";
                         if (count($brands) > 0) {
                             $brand = str_replace("【", "", $brands[0]);
                             $brand = str_replace("】", "", $brand);
                             $brand = delSpace($brand);
                             if (strstr($brand, "/")) {
                                 $oem = explode("/", $brand);
                                 $maker = $oem[0];
                                 $brand = $oem[1];
                             } else {
                                 $maker = $brand;
                             }
                         }
                         //バッテリーの型番取得
                         //パソコンの型番取得
                         $intBattery = mb_strpos($detail, $constBattery);
                         $intComputer = mb_strpos($detail, $constComputer);
                         $intGoodcode = mb_strpos($detail, $constGoodcode);

                         //バッテリーの型番は先頭にある場合
                         if ($intBattery < $intComputer) {
                             $battery = mb_substr($detail, $intBattery, $intComputer-$intBattery);
                             $computer = mb_substr($detail, $intComputer, $intGoodcode-$intComputer);
                         } elseif ($intBattery > $intComputer) {
                             $computer = mb_substr($detail, $intComputer, $intBattery-$intComputer);
                             $battery = mb_substr($detail, $intBattery, $intGoodcode-$intBattery);
                         }

                         if ($battery != "") {
                             $battery = str_replace($constBattery, "", $battery);
                             $battery = strRepacre($battery);
                         }
                         if ($computer != "") {
                             $computer = str_replace($constComputer, "", $computer);
                             $computer = strRepacre($computer);
                         }
                     }
                       $cat = $rs['cp_categories'];
                      if($cat!=13){
                          $str4 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">商品詳細</font></th>'
                                  . '<td bgcolor="ffffff"><font size="2" color="000000">');
                          $str5 = '</font></td></tr>';
                          $str6 = '</table>';
                          $strstr = str_replace($rs['cp_detail'], "", $detail);
                          $strstr = strRepacre($detail);
                     }
                     //スマートフォン用商品説明文
                     $csv_data .= $str1.$rs['cp_number'].$str2.$maker.$str3.strRepacre($rs['cp_gg']);
                     if($cat!=13){
                          $csv_data .= $str4.$strstr.$str5.$str6;
                     }else{
                          $csv_data .= $str4.$battery.$str5.$computer.$str6;
                     }                 
                     $csv_data .= $comma;
                     break;
                 case "26":
                     //モバイル用商品説明文
                     $csv_data .= strRepacre($rs['cp_gg']).$comma;
                     break;
                 case "29":
                     //商品画像URL
                     $csv_data .= $rs['cp_url']." ".$rs['cp_url_1']." ".$rs['cp_url_2']." ".$rs['cp_url_3']." ".$rs['cp_url_4'].$comma;
                     break;
                 case "33":
                     //注文受付数
                     $csv_data .= "-1".$comma;
                     break;
                 case "34":
                     //在庫タイプ
                     $csv_data .= "1".$comma;
                     break;
                 case "35":
                     //在庫数
                     //$csv_data .= "2".$comma;
                     $csv_data .= $rs['number'].$comma;
                     break;
                 case "36":
                     //在庫数表示
                     $csv_data .= "1".$comma;
                     break;
                 case "41":
                     //サーチ非表示
                     $csv_data .= "0".$comma;
                     break;
                 case "44":
                     //在庫戻しフラグ
                     $csv_data .= "1".$comma;
                     break;
                 case "45":
                     //在庫切れ時の注文受付
                     $csv_data .= "1".$comma;
                     break;
                 case "46":
                     //在庫あり時納期管理番号
                     $csv_data .= "3".$comma;
                     break;
                 case "47":
                     //在庫切れ時納期管理番号
                     $csv_data .= "3".$comma;
                     break;
                 case "51":
                     //ヘッダー・フッター・レフトナビ
                 case "52":
                     //表示項目の並び順
                 case "53":
                     //共通説明文（小）
                 case "54":
                     //目玉商品
                 case "55":
                     //共通説明文（大）
                     $csv_data .= iconv("utf-8", "Shift_jis", "自動選択").$comma;
                     break;
                 case "56":
                     //レビュー本文表示
                     $csv_data .= "2".$comma;
                     break;
                 case "57":
                     $csv_data .= "";
                     break;
                 default :
                     $csv_data .= $comma;
                     break;
             }
         }
         $csv_data .= "\n";
     }

     //出力ファイル名の作成
     $csv_file = "LETTO_Store_". date ( "YmdHis" ) .'.csv';
     //MIMEタイプの設定
     header("Content-Type: application/octet-stream");
     //名前を付けて保存のダイアログボックスのファイル名の初期値
     header("Content-Disposition: attachment; filename={$csv_file}");

     // データの出力
     echo($csv_data);
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="links_out'. date ( "YmdHis" ) .'.xlsx"');
// header('Cache-Control: max-age=0');
// $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
// $objWriter->save('php://output');
exit;
} else if ($shop == "amazon") {
    $array = array("配送料無料", "無料", "訳あり", "%OFF", "NEWモデル", "あす楽", "オススメ", "お得", "お買い得", "セール", "Sale", 
            "メール便", "Mail便", "mail便", "楽ギフ", "割引", "激安", "再入荷", "最新", "新作", "新製品", "新発売", "新品", "即納", 
            "代引き", "代引", "定価", "同梱不可", "特価", "配送", "発送", "未使用", "予約");
    for ($i=0;$i<count($array); $i++) {
        $array[$i]= iconv("utf-8", "Shift_jis", $array[$i]);
    }
    //Amazon用Excelファイル作成
    $excel_data = iconv("utf-8", "Shift_jis","TemplateType=Computers\tVersion=2013.1106\t上3行は Amazon.com 記入用です。上3行は変更または削除しないでください。\n");
    $excel_data .= iconv("utf-8", "Shift_jis","商品管理番号\t商品コード(JANコード等)\t商品コードのタイプ\t商品名\tブランド名\tメーカー名\t商品タイプ\tメーカー型番\t商品説明文\t"
        . "アップデート・削除\tパッケージ商品数\t商品の販売価格\tメーカー希望価格\t通貨コード\t在庫数\tリードタイム(出荷までにかかる作業日数)\t"
        . "商品のコンディション\t商品のコンディション説明\t使用しない支払い方法\t配送日時指定SKUリスト\tセール価格\tセール開始日\tセール終了日\t"
        . "リベート名1\tリベート名2\tリベートメッセージ1\tリベートメッセージ2\tリベート開始日1\tリベート開始日2\tリベート終了日1\tリベート終了日2\t"
        . "TAXコード\t情報解禁日(mm/dd/yyyy)\t予約商品の販売開始日\t商品の入荷予定日\t最大同梱可能個数\tギフトメッセージ\tギフト包装\tメーカー製造中止\t"
        . "商品コードなしの理由\t商品の直径\t商品の直径の単位\t配送重量\t発送重量の単位\t商品の幅\t商品の奥行\t商品の高さ\t商品の長さの単位\t商品の重量\t"
        . "商品の重量の単位\tカタログ番号\t商品説明の箇条書き1\t商品説明の箇条書き2\t商品説明の箇条書き3\t商品説明の箇条書き4\t商品説明の箇条書き5\t"
        . "検索キーワード1\t検索キーワード2\t検索キーワード3\t検索キーワード4\t検索キーワード5\t推奨ブラウズノード1\t推奨ブラウズノード2\tプラチナキーワード1\t"
        . "プラチナキーワード2\tプラチナキーワード3\tプラチナキーワード4\tプラチナキーワード5\t商品メイン画像URL\t商品のサブ画像URL1\t商品のサブ画像URL2\t"
        . "商品のサブ画像URL3\t商品のサブ画像URL4\t商品のサブ画像URL5\t商品のサブ画像URL6\t商品のサブ画像URL7\t商品のサブ画像URL8\tカラーサンプル画像URL\t"
        . "フルフィルメントセンターID\t商品パッケージの長さ(cm)\t商品パッケージの幅(cm)\t商品パッケージの高さ(cm)\t商品パッケージの長さの単位\t包装時の重さ\t"
        . "商品パッケージの重量の単位\t親子関係の指定\t親商品のSKU(商品管理番号)\t親子関係のタイプ\tバリエーションテーマ\t原産国\t法規上の免責条項\t警告\t"
        . "カラー\tカラーマップ\tサイズ\tHDD容量の単位\tVRAMサイズ単位(XSDはGBまで)\t画面の解像度\tディスプレイ最大解像度\t画面サイズ\t画面サイズの単位\t"
        . "ディスプレイ方式\tモニターチューナー方式\tグレースケール\t光源タイプ\tコネクタタイプ\t入力タイプ\tスピーカー公称出力\t電圧\tワット数\t電源\t"
        . "電池のタイプ\tバッテリ個数\t電池の有無\t電池付属\tバッテリ平均持続時間\tバッテリ平均持続時間(スタンバイ状態)\tバッテリ充電時間\t"
        . "リチウム電池エネルギー含有量\tリチウム電池パッケージ仕様\tリチウム電池ボルト数\tリチウム電池重量\tリチウムイオン電池数\tリチウムメタル電池数\t"
        . "メーカー製品保証タイプ\t修理保証の説明\t部品保証の説明\t出品者保証の説明\tアダルト商品\tプリンタワイヤレスタイプ1\tプリンタワイヤレスタイプ2\t"
        . "プリンタワイヤレスタイプ3\tRAM容量\tRAM容量の単位\tプロセッサブランド\tプロセッサの速度\tプロセッサ速度の単位\tプロセッサのタイプ\tプロセッサ数\t"
        . "プロセッサソケット\tハードウェアプラットフォーム\tHDD容量1\tHDD容量2\tHDD容量3\tHDD容量4\tHDD容量5\tHDD容量6\tHDD容量7\tHDD容量8\t"
        . "HDDインターフェース1\tHDDインターフェース2\tHDDインターフェース3\tHDDインターフェース4\tハードディスク回転数(/rpm)\tハードディスク方式\t"
        . "メモリタイプ1\tメモリタイプ2\tメモリタイプ3\tメモリタイプ4\tメモリタイプ5\tメモリタイプ6\tメモリタイプ7\tメモリタイプ8\tメモリタイプ9\t"
        . "メモリタイプ10\tオペレーティングシステム1\tオペレーティングシステム2\t追加ドライブ1\t追加ドライブ2\t追加ドライブ3\t追加ドライブ4\t追加ドライブ5\t"
        . "追加ドライブ6\t追加ドライブ7\t追加ドライブ8\t追加ドライブ9\t追加ドライブ10\t同梱ソフト\tユニットラックサイズ\tグラフィックの説明1\t"
        . "グラフィックの説明2\tVRAMサイズ1\tVRAMサイズ2\tグラフィックカードインターフェース1\tグラフィックカードインターフェース2\tVRAMタイプ\t"
        . "グラフィックハードウェア\t搭載光学ドライブ\t搭載記憶装置タイプ\tノートブックディスプレイ方式\tインクカラー1\tインクカラー2\tインクカラー3\t"
        . "インクカラー4\tインクカラー5\tインク・トナー互換対応機種\tポート数\t最大解像度(モノクロ)\t最大解像度(カラー)\t最大印刷速度(モノクロ)\t"
        . "最大印刷速度(カラー)\tプリント技術\t印刷メディアタイプ\t読み取り解像度\t最大給紙枚数\t最大読み取りサイズ\t最小読み取りサイズ\t読み取り速度\t"
        . "プリンタメディア最大サイズ\tプリンタ出力タイプ\tカラースクリーン\tフォームファクタ\tデータ転送速度\tバッファサイズ\tバッファサイズの単位\t"
        . "最大メモリ容量\t最大メモリ容量の単位\t書き込み速度\t特記事項\tアンプタイプ\tオーディオ出力モード\tチップセットのタイプ\tデジタルオーディオ処理性能 \t"
        . "デジタルメディアフォーマット\t通信インターフェース\t有効入力領域\t利き手\tオートフォーカス\tプログラムボタン\t収容量\tキーボードの説明\t"
        . "素材タイプ\t最大通信距離\t最大通信距離の単位\tメモリ拡張スロット(空き)\t移動検知方式\t出力ワット数\t記録容量\tスピーカー出力チャンネル数\t"
        . "スピーカーチャンネル構成\tスピーカー最大出力\t速度測定\t互換CPUタイプ1\t互換CPUタイプ2\t互換CPUタイプ3\t互換CPUタイプ4\n");
    
    $excel_data .= "item_sku\texternal_product_id\texternal_product_id_type\titem_name\tbrand_name\tmanufacturer\tfeed_product_type\t"
            . "part_number\tproduct_description\tupdate_delete\titem_package_quantity\tstandard_price\tlist_price\tcurrency\tquantity\t"
            . "fulfillment_latency\tcondition_type\tcondition_note\toptional_payment_type_exclusion\tdelivery_schedule_group_id\tsale_price\t"
            . "sale_from_date\tsale_end_date\trebate_name1\trebate_name2\trebate_description1\trebate_description2\trebate_start_at1\t"
            . "rebate_start_at2\trebate_end_at1\trebate_end_at2\tproduct_tax_code\tproduct_site_launch_date\tmerchant_release_date\t"
            . "restock_date\tmax_aggregate_ship_quantity\toffering_can_be_gift_messaged\toffering_can_be_giftwrapped\t"
            . "is_discontinued_by_manufacturer\tmissing_keyset_reason\titem_display_diameter\titem_display_diameter_unit_of_measure\t"
            . "website_shipping_weight\twebsite_shipping_weight_unit_of_measure\titem_length\titem_width\titem_height\t"
            . "item_length_unit_of_measure\titem_weight\titem_weight_unit_of_measure\tcatalog_number\tbullet_point1\tbullet_point2\t"
            . "bullet_point3\tbullet_point4\tbullet_point5\tgeneric_keywords1\tgeneric_keywords2\tgeneric_keywords3\tgeneric_keywords4\t"
            . "generic_keywords5\trecommended_browse_nodes1\trecommended_browse_nodes2\tplatinum_keywords1\tplatinum_keywords2\t"
            . "platinum_keywords3\tplatinum_keywords4\tplatinum_keywords5\tmain_image_url\tother_image_url1\tother_image_url2\t"
            . "other_image_url3\tother_image_url4\tother_image_url5\tother_image_url6\tother_image_url7\tother_image_url8\tswatch_image_url\t"
            . "fulfillment_center_id\tpackage_length\tpackage_width\tpackage_height\tpackage_length_unit_of_measure\tpackage_weight\t"
            . "package_weight_unit_of_measure\tparent_child\tparent_sku\trelationship_type\tvariation_theme\tcountry_of_origin\t"
            . "legal_disclaimer_description\tsafety_warning\tcolor_name\tcolor_map\tsize_name\thard_drive_size_unit_of_measure\t"
            . "graphics_ram_size_unit_of_measure\tnative_resolution\tdisplay_resolution_maximum\tdisplay_size\tdisplay_size_unit_of_measure\t"
            . "display_technology\ttuner_technology\tgreyscale_depth\tlight_source_type\tconnector_type\tinput_type\t"
            . "speakers_nominal_output_power\tvoltage\twattage\tpower_source_type\tbattery_type\tnumber_of_batteries\tbatteries_required\t"
            . "are_batteries_included\tbattery_average_life\tbattery_average_life_standby\tbattery_charge_time\tlithium_battery_energy_content\t"
            . "lithium_battery_packaging\tlithium_battery_voltage\tlithium_battery_weight\tnumber_of_lithium_ion_cells\t"
            . "number_of_lithium_metal_cells\tmfg_warranty_description_type\tmfg_warranty_description_labor\tmfg_warranty_description_parts\t"
            . "seller_warranty_description\tis_adult_product\twireless_comm_standard1\twireless_comm_standard2\twireless_comm_standard3\t"
            . "computer_memory_size\tcomputer_memory_size_unit_of_measure\tcomputer_cpu_manufacturer\tcomputer_cpu_speed\t"
            . "computer_cpu_speed_unit_of_measure\tcomputer_cpu_type\tprocessor_count\tprocessor_socket\thardware_platform\thard_disk_size1\t"
            . "hard_disk_size2\thard_disk_size3\thard_disk_size4\thard_disk_size5\thard_disk_size6\thard_disk_size7\thard_disk_size8\t"
            . "hard_disk_interface1\thard_disk_interface2\thard_disk_interface3\thard_disk_interface4\thard_disk_rotational_speed\t"
            . "hard_disk_description\tsystem_ram_type1\tsystem_ram_type2\tsystem_ram_type3\tsystem_ram_type4\tsystem_ram_type5\t"
            . "system_ram_type6\tsystem_ram_type7\tsystem_ram_type8\tsystem_ram_type9\tsystem_ram_type10\toperating_system1\t"
            . "operating_system2\tadditional_drives1\tadditional_drives2\tadditional_drives3\tadditional_drives4\tadditional_drives5\t"
            . "additional_drives6\tadditional_drives7\tadditional_drives8\tadditional_drives9\tadditional_drives10\tsoftware_included\t"
            . "u_rack_size\tgraphics_description1\tgraphics_description2\tgraphics_ram1\tgraphics_ram2\tgraphics_card_interface1\t"
            . "graphics_card_interface2\tgraphics_ram_type\tgraphics_coprocessor\toptical_storage_installed_quantity\toptical_storage_device\t"
            . "notebook_display_technology\tink_color1\tink_color2\tink_color3\tink_color4\tink_color5\tcompatible_devices\tnumber_of_ports\t"
            . "max_print_resolution_black_white\tmax_print_resolution_color\tmax_printspeed_black_white\tmax_printspeed_color\t"
            . "printing_technology\tprint_media_type\tresolution_base\tmax_input_sheet_capacity\tmaximum_scanning_size\tminimum_scanning_size\t"
            . "scan_rate\tmedia_size_maximum\tprinter_output\thas_color_screen\tform_factor\tdata_transfer_rate\tbuffer_size\t"
            . "buffer_size_unit_of_measure\tmemory_storage_capacity\tmemory_storage_capacity_unit_of_measure\twrite_speed\tspecial_features\t"
            . "amplifier_type\taudio_output_mode\tchipset_type\tdigital_audio_capacity\tformat\tcommunication_interface\teffective_input_area\t"
            . "hand_orientation\thas_auto_focus\tis_programmable\tdisc_holder_capacity\tkeyboard_description\tmaterial_type\t"
            . "maximum_operating_distance\tmaximum_operating_distance_unit_of_measure\tmemory_slots_available\tmovement_detection_technology\t"
            . "output_wattage\trecording_capacity\toutput_channel_quantity\tsurround_sound_channel_configuration\tspeakers_maximum_output_power\t"
            . "speed_rating\tcompatible_processor_types1\tcompatible_processor_types2\tcompatible_processor_types3\tcompatible_processor_types4\n";
    
    $tab = "\t";
    while($rs=$row->getArray()){
            $brands = explode("・", $rs['cp_name']);
            $brand = "";
            $maker = "";
            if (count($brands) > 0) {
                $brand = str_replace("【", "", $brands[0]);
                $brand = str_replace("】", "", $brand);
                $brand = delSpace($brand);
                
                if (strstr($brand, "/")) {
                    $oem = explode("/", $brand);
                    $maker = $oem[0];
                    $brand = $oem[1];
                } else {
                    $maker = $brand;
                }
            }

            for ($i=1; $i<=246; $i++) {
                switch($i){
                    case "1":
                        //商品番号
                        $excel_data .= $rs['cp_number'].$tab;
                        break;
                    case "3":
                        //商品コードのタイプ
                        $excel_data .= "UPC".$tab;
                        break;
                    case "4":
                        //商品名
                         $count = count($array);
                        $cptitle = str_replace($array, "", $rs['cp_title']);
                        $excel_data .= strRepacreBrToSpace($cptitle).$tab;
                        break;
                    case "5":
                        //ブランド名
                        $excel_data .= $brand.$tab;
                        break;
                    case "6":
                        //メーカー名
                        $excel_data .= $maker.$tab;
                        break;
                    case "7":
                        //商品タイプ
                        $excel_data .= "ComputerComponent".$tab;
                        break;
                    case "9":
                        //商品説明文PHP_EOL
                        $excel_data .= strRepacre($rs['cp_gg'])."<br>".strRepacre($rs['cp_detail']).$tab;
                        break;
                    case "10":
                        //アップデート・削除
                        $excel_data .= "Update".$tab;
                        break;
                    case "12":
                        //商品の販売価格
                        $excel_data .= $rs['cp_sale1'].$tab;
                        break;
                    case "14":
                        //通貨コード
                        $excel_data .= "JPY".$tab;
                        break;
                    case "15":
                        //在庫数
                        //$excel_data .= "2".$tab;
                        $excel_data .= $rs['number'].$tab;
                        break;
                    case "18":
                        //商品のコンディション説明
                        $excel_data .= iconv("utf-8", "Shift_jis","代引き、コンビニ受取&支払いなどにも対応しています。"
                            . "<br>産地品質検査センター設置しており、100％品質保証対応でございます。"
                            . "<br>日本国内本社でお客様対応しております。"
                            . "<br>まとめ買い等もお気軽にご相談ください。").$tab;
                        break;
                    case "50":
                        //商品の重量の単位
                        $excel_data .= "ComputerComponent".$tab;
                        break;
                    case "52":
                        //商品説明の箇条書き1
                        $excel_data .= $rs['cp_bullet_1'].$tab;
                        break;
                    case "53":
                        //商品説明の箇条書き2
                        $excel_data .= $rs['cp_bullet_2'].$tab;
                        break;
                    case "54":
                        //商品説明の箇条書き3
                        $excel_data .= $rs['cp_bullet_3'].$tab;
                        break;
                    case "55":
                        //商品説明の箇条書き4
                        $excel_data .= $rs['cp_bullet_4'].$tab;
                        break;
                    case "56":
                        //商品説明の箇条書き5
                        $excel_data .= $rs['cp_bullet_5'].$tab;
                        break;
                    case "57":
                        //検索キーワード1
                        $excel_data .= $rs['cp_helpword'].$tab;
                        break;
                    case "58":
                        //検索キーワード2
                        $excel_data .= $rs['cp_helpword_1'].$tab;
                        break;
                    case "59":
                        //検索キーワード3
                        $excel_data .= $rs['cp_helpword_2'].$tab;
                        break;
                    case "60":
                        //検索キーワード4
                        $excel_data .= $rs['cp_helpword_3'].$tab;
                        break;
                    case "61":
                        //検索キーワード5
                        $excel_data .= $rs['cp_helpword_4'].$tab;
                        break;
                    case "62":
                        //推奨ブラウズノード1
                        $excel_data .= $rs['cp_browse_node_1'].$tab;
                        break;
                    case "63":
                        //推奨ブラウズノード2
                        $excel_data .= $rs['cp_browse_node_2'].$tab;
                        break;
                    case "69":
                        //商品メイン画像URL
                        $excel_data .= $rs['cp_url'].$tab;
                        break;
                    case "70":
                        //商品のサブ画像URL1
                        $excel_data .= $rs['cp_url_1'].$tab;
                        break;
                    case "71":
                        //商品のサブ画像URL2
                        $excel_data .= $rs['cp_url_2'].$tab;
                        break;
                    case "72":
                        //商品のサブ画像URL3
                        $excel_data .= $rs['cp_url_3'].$tab;
                        break;
                    case "73":
                        //商品のサブ画像URL4
                        $excel_data .= $rs['cp_url_4'].$tab;
                        break;
                    case "246":
                        $excel_data .= "";
                        break;
                    default :
                        $excel_data .= $tab;
                        break;
                }
            }
            $excel_data .= "\n";
    }
    
    //出力ファイル名の作成
    $excel_file = "Amazon_Store_". date ( "YmdHis" ) .'.xls';
    //MIMEタイプの設定
    header("Content-Type: application/vnd.ms-excel");
    //名前を付けて保存のダイアログボックスのファイル名の初期値
    header("Content-Disposition: attachment;filename={$excel_file}");

    // データの出力
    echo($excel_data);
    
}

exit();

//header("Content-type:application/vnd.ms-excel");
//header("Content-Disposition:filename=excel_basic.xls");
//echo "品番\t名称\t規格\t分類\t単位\t仕入単価\t仕入先\t\n";
//while($rs=$row->getArray()){
//$alln+=1;
//echo $rs['cp_number']."\t".$rs['cp_name']."\t".$rs['cp_gg']."\t".get_name($rs['cp_categories'],'categories').">".get_name($rs['cp_categories_down'],'categories')."\t".get_name($rs['cp_dwname'],'dw')."\t".$rs['cp_jj']."\t".get_name($rs['cp_number'],'gys')."\t\n";
//}
//echo "合   計\t\t\t\t\t\t\t商品種類：\t".$alln." 種類\t\n";
?>