<?php
require ("service/ExcelService.php");
$type = $_REQUEST["type"];
$s_type = $_REQUEST["s_type"];
$sday = $_REQUEST["sday"];
$eday = $_REQUEST["eday"];
$avrg = 1;
$cp_categories="";
$cp_categories_down="";
if(!empty($_REQUEST["avrg"])) {
    $avrg = $_REQUEST["avrg"];
}
if(!empty($_REQUEST["cp_categories"])) {
    $cp_categories = $_REQUEST["cp_categories"];
}
if(!empty($_REQUEST["cp_categories_down"])) {
    $cp_categories_down = $_REQUEST["cp_categories_down"];
}
$es = new ExcelService();
switch ($type) {
    case "day1":
        $query = "select a.productid, b.cp_name, b.cp_title, b.cp_detail, a.sale, sum(a.number) number, sum(a.number)/{$avrg} avrg, c.number-sum(a.number)/{$avrg} remain_left 
                 from jxc_sale a,jxc_basic b, jxc_mainkc c 
                 where a.del_flag =0 and to_days(a.dtime)>=to_days('$sday') 
                 and to_days(a.dtime)<=to_days('$eday')
                 and a.s_type=$s_type";
        if(!empty($cp_categories)) {
            $query .= " and b.cp_categories = '{$cp_categories}'";
        }
        if(!empty($cp_categories_down)){
            $query .= " and b.cp_categories_down = '{$cp_categories_down}'";
        }
        $query .= " and a.productid=b.cp_number
                    and a.productid = c.p_id
                    GROUP BY a.productid, b.cp_name, b.cp_title, a.sale
                    order by sum(a.number) desc";
        $report_title = " 販売レポート_日報 ";
        $headers = array('商品コード','タイトル','仕様','販売単価','平均数','合計数','仕入れ数','総合金額');
        $es->out_excel($report_title, $query, $headers);
        break;
}
?>