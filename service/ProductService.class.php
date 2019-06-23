<?php
require_once dirname(__FILE__)."/../include/config_passport.php";
require_once dirname(__FILE__)."/../include/config.php";
require_once dirname(__FILE__)."/../include/config_base.php";
require_once dirname(__FILE__)."/../include/inc_functions.php";
define("COMMA", ",");
define("TAB", "\t");
define("PAGE_COUNT", 500);
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initTotal":
            $result = insert();
        break;
        case "initYear":
            $result = initReceiver();
        break;
        case "initNew":
            $result = initNew();
        break;
        case "initCategories":
            $result = initCategories();
        break;
        case "initLab":
            $result = initLab();
        break;
        case "initState":
            $result = initState();
        break;
        case "getCategoryDown":
            $result = getCategoryDown($_REQUEST["cp_categories"]);
        break;
        case "init":
            $result = initPage();
        break;
        case "writeNote":
            $result = writeKcNote();
        break;
        case "writeState":
            $result = writeKcState();
        break;
        case "out_excel":
            $result = out_excel();
        break;
        
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function initPage($pageCount=100000, $pageIndex=1, $exportType) {
        $result= array();
        $stext = trim($_REQUEST["stext"]);
        $textRelate = trim($_REQUEST["textRelate"]);
        $sstate1 = trim($_REQUEST["sstate1"]);
        $sstate2 = trim($_REQUEST["sstate2"]);
        $sstate3 = trim($_REQUEST["sstate3"]);
        $sstate4 = trim($_REQUEST["sstate4"]);
        $sstate5 = trim($_REQUEST["sstate5"]);
        $sstate6 = trim($_REQUEST["sstate6"]);
        $sstate7 = trim($_REQUEST["sstate7"]);
        $sstate8 = trim($_REQUEST["sstate8"]);
        $sstate9 = trim($_REQUEST["sstate9"]);
        $sstate10 = trim($_REQUEST["sstate10"]);
        $sstate12 = trim($_REQUEST["sstate12"]);
        $labid = $_REQUEST["labid"];
        $cp_categories = $_REQUEST["cp_categories"];
        $cp_categories_down = $_REQUEST["cp_categories_down"];
        $sort = $_REQUEST["sort"];
        $searchType = $_REQUEST["searchType"];
        $compare = $_REQUEST["compare"];
        $btnflag = $_REQUEST["btnflag"];
        $num = $_REQUEST["num"];
        if($_REQUEST["pageCount"]) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if($_REQUEST["pageIndex"]) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $sdate_out = $_REQUEST["sdate_out"];
        $edate_out = $_REQUEST["edate_out"];
        $ksql = new Dedesql(false);
        $query = "select a.id, a.cp_number, a.cp_parent, a.cp_tm, a.cp_name, REPLACE(a.cp_title, ' ', ' ') cp_title, REPLACE(a.cp_detail,'～','~') cp_detail, b.cp_sale, b.cp_cost,
            b.dtime, f.s_name s_name1, g.s_name s_name2, h.s_name s_name3, i.s_name s_name4, j.s_name s_name5, 
            b.l_state6 s_name6, b.l_state7 s_name7, l.s_name s_name8, b.l_state9 s_name9, b.l_state10 s_name10, k.s_name s_name11, b.l_state12 s_name12,
            b.l_note,b.l_asin, a.dtime cp_dtime, b.number, b.kid, b.l_id, b.l_floor, b.l_shelf, b.l_zone, b.l_horizontal, b.l_vertical,
            ifnull(b.col1,0)+ifnull(b.col2,0)+ifnull(b.col3,0)+ifnull(b.col4,0)+ifnull(b.col5,0)+ifnull(b.col6,0) buying_number, b.page_name_id, c.l_name";
        if(empty($_REQUEST["pageCount"])) {
            $query .= ", a.cp_dwname, a.cp_categories, a.cp_categories_down, a.cp_gg, cp_sale, a.cp_saleall,
                a.cp_sdate, a.cp_edate, a.cp_gys, a.cp_bz, a.cp_url, a.cp_url_1, a.cp_url_2, a.cp_url_3, a.cp_url_4,
                b.l_state1, b.l_state2, b.l_state3, b.l_state4, b.l_state5,
                b.l_state6, b.l_state7, b.l_state8, b.l_state9, b.l_state10,
                b.l_state11, b.l_state12, b.l_state13, b.l_state14, b.l_state15,
                a.cp_browse_node_1, a.cp_browse_node_2, a.cp_helpword, a.cp_helpword_1, a.cp_helpword_2, a.cp_helpword_3,
                a.cp_helpword_4, a.cp_helpword_5, a.cp_helpword_6, a.cp_helpword_7, a.cp_helpword_8, a.cp_helpword_9,
                a.cp_bullet_1, a.cp_bullet_2, a.cp_bullet_3, a.cp_bullet_4, a.cp_bullet_5, a.cp_bullet_6,
                d.addword addword, e.addword category_down_addword";
        }
        if($exportType =="export") {
            $query .= ", group_concat(b.l_state1) gc_l_state1, group_concat(b.l_state2) gc_l_state2, group_concat(b.l_state3) gc_l_state3, group_concat(b.l_state4) gc_l_state4, group_concat(case b.l_state5 when 20 then 0 when 22 then 8 when 24 then 7 else b.l_state5 end) gc_l_state5,
                group_concat(b.l_state6) gc_l_state6, group_concat(b.l_state7) gc_l_state7, group_concat(b.l_state8) gc_l_state8, group_concat(b.l_state9) gc_l_state9, group_concat(b.l_state10) gc_l_state10,
                group_concat(b.l_asin) gc_l_asin, group_concat(b.l_note) gc_l_note, group_concat(b.number) gc_number, group_concat(b.kid) gc_kid, group_concat(b.l_id) gc_l_id, group_concat(concat(b.l_floor,'-', b.l_shelf,'-', b.l_zone, '-',b.l_horizontal,'-', b.l_vertical)) gc_pos";
            
        }
        $query .= "  from jxc_product_basic a, jxc_product_mainkc b, jxc_lab c, jxc_categories d, jxc_categories e,
            jxc_state f, jxc_state g, jxc_state h, jxc_state i, jxc_state j, jxc_state k, jxc_state l
            where a.cp_categories = d.id 
                and a.cp_categories_down = e.id
                and IFNULL(b.l_state1 ,'0') = f.s_value
                and IFNULL(b.l_state2 ,'0') = g.s_value
                and IFNULL(b.l_state3 ,'0') = h.s_value
                and IFNULL(b.l_state4 ,'0') = i.s_value
                and IFNULL(b.l_state5 ,'0') = j.s_value
                and IFNULL(b.l_state8 ,'0') = l.s_value
                and IFNULL(b.l_state11 ,'0') = k.s_value
                and b.p_id = a.cp_number
                and b.l_id = c.id";
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                $lids = array();
                foreach ($strChk as $id_lid) {
                    $temp = explode("#",$id_lid);
                    array_push($ids, $temp[0]);
                    array_push($lids, $temp[1]);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        } else {
            if ($stext) {
                $temp = explode(" ",$stext);
                $query .= " and (";
                $queryTemp = "";
                $tempLength = count($temp);
                if($searchType) {
                    for ($i=0; $i<$tempLength; $i++) {
                        if($i<($tempLength-1)) {
                            $queryTemp .= " a.".$searchType." LIKE '%" . $temp[$i] . "%' ".$textRelate;
                        } else {
                            $queryTemp .= " a.".$searchType." LIKE '%" . $temp[$i] . "%' ";
                        }
                    }
                } else {
                    for ($i=0; $i<$tempLength; $i++) {
                        $stext = $temp[$i];
                        if($i<($tempLength-1)) {
                            $queryTemp .= "(a.cp_number LIKE '%" . $stext . "%'
                                    or a.cp_title LIKE '%" . $stext . "%'
                                    or b.l_note LIKE '%" . $stext . "%'
                                    or b.kid LIKE '%" . $stext . "%'
                                    or a.cp_bz LIKE '%" . $stext . "%'
                                    or a.cp_tm LIKE '%" . $stext . "%'
                                    or a.cp_detail LIKE '%" . $stext . "%'
                                    or a.cp_gg LIKE '%" . $stext . "%'
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
                                    or a.cp_helpword_9 LIKE '%" . $stext . "%') ".$textRelate;
                        } else {
                            $queryTemp .= " (a.cp_number LIKE '%" . $stext . "%'
                                    or a.cp_title LIKE '%" . $stext . "%'
                                    or b.l_note LIKE '%" . $stext . "%'
                                    or b.kid LIKE '%" . $stext . "%'
                                    or a.cp_bz LIKE '%" . $stext . "%'
                                    or a.cp_tm LIKE '%" . $stext . "%'
                                    or a.cp_detail LIKE '%" . $stext . "%'
                                    or a.cp_gg LIKE '%" . $stext . "%'
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
                                    or a.cp_helpword_9 LIKE '%" . $stext . "%') ";
                        }
                    }
                }
                $query .= $queryTemp.")";
            }
        }
        if(!empty($cp_categories)) {
            $query .= " and a.cp_categories = '{$cp_categories}'";
        }
        if(!empty($cp_categories_down)){
            $query .= " and a.cp_categories_down = '{$cp_categories_down}'";
        }
        if(!empty($labid)){
            $query .= " and b.l_id = '{$labid}'";
        }
        if(!empty($sstate1)){
            $query .= " and b.l_state1 = '{$sstate1}'";
        }
        if(!empty($sstate2)){
            $query .= " and b.l_state2 = '{$sstate2}'";
        }
        if(!empty($sstate3)){
            $query .= " and b.l_state3 = '{$sstate3}'";
        }
        if(!empty($sstate4)){
            $query .= " and b.l_state4 = '{$sstate4}'";
        }
        if(!empty($sstate5)){
            $query .= " and b.l_state5 = '{$sstate5}'";
        }
//         if(!empty($sstate6)){
//             $query .= " and b.l_state6 = '{$sstate6}'";
//         }
        if(!empty($sstate7)){
            $query .= " and {$sstate7}";
        }
//         if(!empty($sstate8)){
//             $query .= " and b.l_state8 = '{$sstate8}'";
//         }
//         if(!empty($sstate9)){
//             $query .= " and b.l_state9 = '{$sstate9}'";
//         }
//         if(!empty($sstate10)){
//             $query .= " and b.l_state10 = '{$sstate10}'";
//         }
        if(!empty($sstate12)){
            $query .= " and b.l_state12 <0";
			//如果l_state11=1, 并且标记时间已经是5天前, 恢复 l_state11 =0
			$sql = "update jxc_mainkc set l_state11=0 where l_state11=1 and datediff(now(), dtime) >=5";
			$ksql->setquery($sql);
			$ksql->execute();
			$ksql->GetOne();
			$sql = "update jxc_mainkc set l_state12 = (case 
			             when (number - l_state9*(select p_value from jxc_static  where p_type='SYSTEM_KC_BUYING_PARAM' and p_name = 'system_kc_buying_param'))<0
			                 then round(number - l_state9*(select p_value from jxc_static  where p_type='SYSTEM_KC_BUYING_PARAM' and p_name = 'system_kc_buying_param'), 2)
			             else 0 end)";
			$ksql->setquery($sql);
			$ksql->execute();
			$ksql->GetOne();
        }
        if(!empty($sdate)){
            $query .= " and a.cp_dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and a.cp_dtime <= '{$edate}'";
        }
        if(!empty($sdate_out)){
            $query .= " and b.dtime >= '{$sdate_out}'";
        }
        if(!empty($edate_out)){
            $query .= " and b.dtime <= '{$edate_out}'";
        }
        switch ($compare) {
            case "gt":
            $compare = ">";
            break;
            case "lt":
            $compare = "<";
            break;
            default:
            $compare = "=";
            break;
        }
        if($num != ""){
            $query .= " and b.number {$compare} '{$num}'";
        }
        if($btnflag == 1){
            $query .= " and b.number >0";
        }
        if($exportType =="export") {
            $query .= " group by a.cp_number";
        }
        if($sort){
            switch ($sort) {
                case 1:
                    $query .= " order by a.cp_number";
                break;
                case 2:
                    $query .= " order by a.cp_number desc";
                break;
                case 3:
                    $query .= " order by b.number";
                break;
                case 4:
                    $query .= " order by b.number desc";
                break;
                case 5:
                    $query .= " order by concat(b.l_floor, '-', b.l_shelf, '-', b.l_zone, '-', b.l_horizontal, '-', b.l_vertical)";
                break;
                case 6:
                    $query .= " order by concat(b.l_floor, '-', b.l_shelf, '-', b.l_zone, '-', b.l_horizontal, '-', b.l_vertical) desc";
                break;
                case 7:
                    $query .= " order by cp_sale1";
                break;
                case 8:
                    $query .= " order by cp_sale1 desc";
                break;
                case 9:
                    $query .= " order by b.l_asin";
                break;
                case 10:
                    $query .= " order by b.l_asin desc";
                break;
                case 11:
                case 13:
                case 15:
                case 17:
                case 19:
                case 21:
                case 23:
                case 25:
                case 27:
                case 29:
                case 31:
                case 33:
                case 35:
                    $sort=($sort-11)/2+1;
                    $query .= " order by l_state{$sort}+0 ";
                break;
                case 12:
                case 14:
                case 16:
                case 18:
                case 20:
                case 24:
                case 26:
                case 28:
                case 30:
                case 32:
                case 34:
                case 36:
                    $sort=($sort-12)/2+1;
                    $query .= " order by l_state{$sort}+0 desc";
                break;
                default:
                    $query .= " order by a.cp_number";
                break;
            }
        }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
//         echo $query;
//         echo '<br/>-------------------<br/>';
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $result[] = $row;
        }
        $ksql->close();
        if(!isset($_REQUEST["pageIndex"])) {
            foreach ($result as &$bean) {
                foreach ($bean as $key => $value) {
                    $bean[$key] = iconv("utf-8", "Shift_jis", $bean[$key]);
                }
            }
            return $result;
        }
		$total =initPageCount();
		$totaljson = "[{\"totalcount\":\"".$total."\"}]";
		return "{\"totalproperty\":".$totaljson.",\"results\":".json_encode($result, JSON_FORCE_OBJECT)."}";
    }
    
    function initPageCount() {
        $result= array();
        $stext = $_REQUEST["stext"];
        $textRelate = $_REQUEST["textRelate"];
        $sstate1 = trim($_REQUEST["sstate1"]);
        $sstate2 = trim($_REQUEST["sstate2"]);
        $sstate3 = trim($_REQUEST["sstate3"]);
        $sstate4 = trim($_REQUEST["sstate4"]);
        $sstate5 = trim($_REQUEST["sstate5"]);
        $sstate6 = trim($_REQUEST["sstate6"]);
        $sstate7 = trim($_REQUEST["sstate7"]);
        $sstate8 = trim($_REQUEST["sstate8"]);
        $sstate9 = trim($_REQUEST["sstate9"]);
        $sstate10 = trim($_REQUEST["sstate10"]);
        $sstate12 = trim($_REQUEST["sstate12"]);
        $labid = $_REQUEST["labid"];
        $searchType = $_REQUEST["searchType"];
        $cp_categories = $_REQUEST["cp_categories"];
        $cp_categories_down = $_REQUEST["cp_categories_down"];
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $sdate_out = $_REQUEST["sdate_out"];
        $edate_out = $_REQUEST["edate_out"];
        $compare = $_REQUEST["compare"];
        $num = $_REQUEST["num"];
        $btnflag = $_REQUEST["btnflag"];
        $ksql = new Dedesql(false);
        $query = "select count(0)
            from #@__basic a, #@__mainkc b, #@__lab c , jxc_categories d, jxc_categories e,
            jxc_state f, jxc_state g, jxc_state h, jxc_state i, jxc_state j, jxc_state k
            where a.cp_categories = d.id 
                and a.cp_categories_down = e.id
                and IFNULL(b.l_state1 ,'0') = f.s_value
                and IFNULL(b.l_state2 ,'0') = g.s_value
                and IFNULL(b.l_state3 ,'0') = h.s_value
                and IFNULL(b.l_state4 ,'0') = i.s_value
                and IFNULL(b.l_state5 ,'0') = j.s_value
                and IFNULL(b.l_state11 ,'0') = k.s_value
                and b.p_id = a.cp_number
                and b.l_id = c.id";
        if ($stext) {
            $temp = explode(" ",$stext);
            $query .= " and (";
            $queryTemp = "";
            $tempLength = count($temp);
            if($searchType) {
                for ($i=0; $i<$tempLength; $i++) {
                    if($i<($tempLength-1)) {
                        $queryTemp .= " a.".$searchType." LIKE '%" . $temp[$i] . "%' ".$textRelate;
                    } else {
                        $queryTemp .= " a.".$searchType." LIKE '%" . $temp[$i] . "%' ";
                    }
                }
            } else {
                for ($i=0; $i<$tempLength; $i++) {
                    $stext = $temp[$i];
                    if($i<($tempLength-1)) {
                        $queryTemp .= "(a.cp_number LIKE '%" . $stext . "%'
                                or a.cp_title LIKE '%" . $stext . "%'
                                or b.l_note LIKE '%" . $stext . "%'
                                or b.kid LIKE '%" . $stext . "%'
                                or a.cp_bz LIKE '%" . $stext . "%'
                                or a.cp_tm LIKE '%" . $stext . "%'
                                or a.cp_detail LIKE '%" . $stext . "%'
                                or a.cp_gg LIKE '%" . $stext . "%'
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
                                or a.cp_helpword_9 LIKE '%" . $stext . "%') ".$textRelate;
                    } else {
                        $queryTemp .= " (a.cp_number LIKE '%" . $stext . "%'
                                or a.cp_title LIKE '%" . $stext . "%'
                                or b.l_note LIKE '%" . $stext . "%'
                                or b.kid LIKE '%" . $stext . "%'
                                or a.cp_bz LIKE '%" . $stext . "%'
                                or a.cp_tm LIKE '%" . $stext . "%'
                                or a.cp_detail LIKE '%" . $stext . "%'
                                or a.cp_gg LIKE '%" . $stext . "%'
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
                                or a.cp_helpword_9 LIKE '%" . $stext . "%') ";
                    }
                }
            }
            $query .= $queryTemp.")";
        }
        
        if(!empty($cp_categories)) {
            $query .= " and a.cp_categories = '{$cp_categories}'";
        }
        if(!empty($cp_categories_down)){
            $query .= " and a.cp_categories_down = '{$cp_categories_down}'";
        }
        if(!empty($labid)){
            $query .= " and b.l_id = '{$labid}'";
        }
        if(!empty($sstate1)){
            $query .= " and b.l_state1 = '{$sstate1}'";
        }
        if(!empty($sstate2)){
            $query .= " and b.l_state2 = '{$sstate2}'";
        }
        if(!empty($sstate3)){
            $query .= " and b.l_state3 = '{$sstate3}'";
        }
        if(!empty($sstate4)){
            $query .= " and b.l_state4 = '{$sstate4}'";
        }
        if(!empty($sstate5)){
            $query .= " and b.l_state5 = '{$sstate5}'";
        }
//         if(!empty($sstate6)){
//             $query .= " and b.l_state6 = '{$sstate6}'";
//         }
        if(!empty($sstate7)){
            $query .= " and {$sstate7}";
        }
//         if(!empty($sstate8)){
//             $query .= " and b.l_state8 = '{$sstate8}'";
//         }
//         if(!empty($sstate9)){
//             $query .= " and b.l_state9 = '{$sstate9}'";
//         }
//         if(!empty($sstate10)){
//             $query .= " and b.l_state10 = '{$sstate10}'";
//         }
        if(!empty($sstate12)){
            $query .= " and b.l_state12 <0";
        }
        if(!empty($sdate)){
            $query .= " and a.cp_dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and a.cp_dtime <= '{$edate}'";
        }
        if(!empty($sdate_out)){
            $query .= " and b.dtime >= '{$sdate_out}'";
        }
        if(!empty($edate_out)){
            $query .= " and b.dtime <= '{$edate_out}'";
        }
        switch ($compare) {
            case "gt":
            $compare = ">";
            break;
            case "lt":
            $compare = "<";
            break;
            default:
            $compare = "=";
            break;
        }
        if($num != ""){
            $query .= " and b.number {$compare} '{$num}'";
        }
        if($btnflag == 1){
            $query .= " and b.number > 0";
        }
        $ksql->setquery($query);
        $ksql->execute();
        $result = $ksql->GetOne();
        return  $result[0];
    }
    
    function insert() {
        $ksql = new Dedesql(false);
        $query = "select sum(number) s, count(0) c from #@__mainkc";
        $ksql->setquery($query);
        $ksql->execute();
        $row = $ksql->getArray();
        $query = "SELECT IFNULL(sum(number), 0) s FROM `jxc_sale`  where SUBSTR(dtime FROM 1 FOR 10) = date_add(CURDATE(), interval -1 day)";
        $ksql->setquery($query);
        $ksql->execute();
        $row1 = $ksql->getArray();
        $ksql->close();
        return "倉庫統計:"."<font color=red>".$row['c']."</font>". "条"." <font color=red>".$row['s']."</font>". "件<br/>昨天贩卖总数"."<font color=red>".$row1['s']."件</font>";
         
    }
    function initReceiver() {
        $ksql = new Dedesql(false);
        $query = "select id, name from #@__year";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $selectHtml .= "<option value='{$row["id"]}'>{$row["name"]}</option>";
        }
        $ksql->close();
        return $selectHtml;
    }
    function initNew() {
        $ksql = new Dedesql(false);
        $query = "select id, name from #@__new";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $selectHtml .= "<option value='{$row["id"]}'>{$row["name"]}</option>";
        }
        $ksql->close();
        return $selectHtml;
    }
    function initCategories() {
        $ksql = new Dedesql(false);
        $query = "select id, categories from #@__categories where reid = 0";
        $ksql->setquery($query);
        $ksql->execute();
        while ($row = $ksql->GetAssoc()) {
            $selectHtml .= "<option value='{$row["id"]}'>{$row["categories"]}</option>";
        }
        $ksql->close();
        return $selectHtml;
    }
    function getCategoryDown($category) {
        $ksql = new Dedesql(false);
        $query = "select id, categories from #@__categories where reid = ".$category;
        $ksql->setquery($query);
        $ksql->execute();
        $selectHtml = "";
        while ($row = $ksql->GetAssoc()) {
            $selectHtml .= "<option value='{$row["id"]}'>{$row["categories"]}</option>";
        }
        $ksql->close();
        return $selectHtml;
    }
    function initLab() {
        $ksql = new Dedesql(false);
        $query = "select * from #@__lab";
        $ksql->setquery($query);
        $ksql->execute();
        $selectHtml = "";
        while ($row = $ksql->GetAssoc()) {
            $selectHtml .= "<option value='{$row["id"]}'>{$row["l_name"]}</option>";
        }
        $ksql->close();
        return $selectHtml;
    }
    function initState() {
        $parent_id = $_REQUEST["sid"];
        $ksql = new Dedesql(false);
        $query = "select * from #@__state where parent_id = ".$parent_id;
        $ksql->setquery($query);
        $ksql->execute();
        $selectHtml = "<option value='0'>クリア</option>";
        while ($row = $ksql->GetAssoc()) {
            $selectHtml .= "<option value='{$row["s_value"]}'>{$row["s_name"]}</option>";
        }
        $ksql->close();
        return $selectHtml;
    }

    function writeKcNote() {
        $writeNoteText = trim($_REQUEST["writeNoteText"]);
        $writeASINText = trim($_REQUEST["writeASINText"]);
        $isWriteNoteText = trim($_REQUEST["isWriteNoteText"]);
        $isWriteASINText = trim($_REQUEST["isWriteASINText"]);
        $ksql = new Dedesql(false);
        $query = "update #@__mainkc a set ";
        if($isWriteNoteText ==1) {
            $query .=" a.l_note = '" . $writeNoteText. "', ";
        } else {
            $query .=" a.l_note = a.l_note, ";
        }
        if($isWriteASINText ==1) {
            if(preg_match('/^#.*/',$writeASINText)) {
                $query .=" a.l_asin = '" . str_replace("#", "", $writeASINText). "'";
            } else {
                $query .=" a.l_asin = concat(a.l_asin, '" . $writeASINText. "')";
            }
        } else {
            $query .=" a.l_asin = a.l_asin";
        }
        
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                $lids = array();
                foreach ($strChk as $id_lid) {
                    $temp = explode("#",$id_lid);
                    array_push($ids, $temp[0]);
                    array_push($lids, $temp[1]);
                }
                $query .= " where a.kid in ('" . implode("','", $lids) . "')";
            }
        }
        $ksql->setquery($query);
        return $ksql->ExecuteNoneQuery2();
    }
    
    function writeKcState() {
        $ksql = new Dedesql(false);
        $query = "update #@__mainkc a set ";
        for ($i=1; $i<12; $i++) {
            if(isset($_REQUEST["writeState{$i}Select"]) && $_REQUEST["writeState{$i}Select"] !="") {
                $query .= "a.l_state{$i} = '" . $_REQUEST["writeState{$i}Select"]. "',";
            }
        }
        if(strripos($query, ",") == strlen($query)-1) {
            $query = subStr($query, 0, strlen($query) - 1);
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                $lids = array();
                foreach ($strChk as $id_lid) {
                    $temp = explode("#",$id_lid);
                    array_push($ids, $temp[0]);
                    array_push($lids, $temp[1]);
                }
                $query .= " where a.kid in ('" . implode("','", $lids) . "')";
            }
        }
        $ksql->setquery($query);
        return $ksql->ExecuteNoneQuery2();
    }
    
    function out_excel() {
        switch ($_REQUEST["shop"]) {
            case "rakuten":
                //出力ファイル名の作成
                $csv_file = "../download/LETTO_Store_". date ( "YmdHis" ) .'.csv';
                
                //楽天用CSVファイル作成
                $csv_data = iconv("utf-8", "Shift_jis", "コントロールカラム,商品管理番号（商品URL）,商品番号,全商品ディレクトリID,タグID,PC用キャッチコピー,モバイル用キャッチコピー,"
                    . "商品名,販売価格,表示価格,消費税,送料,個別送料,送料区分1,送料区分2,代引料,倉庫指定,商品情報レイアウト,注文ボタン,資料請求ボタン,"
                    . "商品問い合わせボタン,再入荷お知らせボタン,のし対応,PC用商品説明文,モバイル用商品説明文,スマートフォン用商品説明文,"
                    . "PC用販売説明文,商品画像URL,商品画像名（ALT）,動画,販売期間指定,注文受付数,在庫タイプ,在庫数,在庫数表示,項目選択肢別在庫用横軸項目名,"
                    . "項目選択肢別在庫用縦軸項目名,項目選択肢別在庫用残り表示閾値,RAC番号,サーチ非表示,闇市パスワード,カタログIDなしの理由,在庫戻しフラグ,在庫切れ時の注文受付,"
                    . "在庫あり時納期管理番号,在庫切れ時納期管理番号,予約商品発売日,ポイント変倍率,ポイント変倍率適用期間,ヘッダー・フッター・レフトナビ,"
                    . "表示項目の並び順,共通説明文（小）,目玉商品,共通説明文（大）,レビュー本文表示,あす楽配送管理番号,海外配送管理番号,サイズ表リンク,"
                    . "医薬品説明文,医薬品注意事項,二重価格文言管理番号\n");
                
                $str1 = iconv("utf-8", "Shift_jis", '<table width="400" bgcolor="000000"  cellspacing="1" cellpadding="3">'
                    . '<tr><th colspan="2" align="center" bgcolor="DCDCDC"><b><font color="000000">商品説明</font></b></th></tr>'
                        . '<tr><th align="left" bgcolor="DCDCDC" width="18%"><font size="2" color="000000">管理番号</font></th><td bgcolor="ffffff">'
                            . '<font size="2" color="000000">');
                $str2 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">メーカー名</font></th>'
                    . '<td bgcolor="ffffff"><font size="2" color="000000">');
                $str3 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">仕様</font></th>'
                    . '<td bgcolor="ffffff"><font size="2" color="000000">');
                $str4 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">バッテリーの型番</font></th>'
                    . '<td bgcolor="ffffff"><font size="2" color="000000">');
                $str5 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">パソコンの型番</font></th>'
                    . '<td bgcolor="ffffff"><font size="2" color="000000">');
                $str6 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">弊社の安心ポイント</font></th>'
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
                $constBattery = iconv("utf-8", "Shift_jis", '【対応バッテリーの品番】');
                $constComputer = iconv("utf-8", "Shift_jis", '【対応パソコンの型番】');
                $constGoodcode = iconv("utf-8", "Shift_jis", '商品コード');
                $myfile = fopen($csv_file, "a") or die("Unable to open file!");
                for ($i=1; $i<=100; $i++) {
                    $results = initPage(PAGE_COUNT, $i);
                    if($results) {
                        foreach ($results as $bean) {
                            //追加分类用1
//                             $csv_data .= $bean['addword'].COMMA;
                            //追加分类用2
//                             $csv_data .= $bean['category_down_addword'].COMMA;
                            //コントロールカラム
                            $csv_data .= "n".COMMA.COMMA;
                            //商品番号
                            $csv_data .= $bean['cp_number'].COMMA;
                            //全商品ディレクトリID
                            $csv_data .= $bean['addword'].COMMA;
                            //タグID
                            $csv_data .= $bean['category_down_addword'].COMMA;
                            $csv_data .= insertSign(2, COMMA);
                            //商品名
                            $csv_data .= $bean['cp_title'].COMMA;
                            //販売価格
                            $csv_data .= $bean['cp_sale1'].COMMA.COMMA;
                            //消費税
                            $csv_data .= "1".COMMA;
                            //送料
                            $csv_data .= "0".COMMA;
                            $csv_data .= insertSign(3, COMMA);
                            //代引料
                            $csv_data .= "0".COMMA;
                            //倉庫指定
                            $csv_data .= "0".COMMA;
                            //商品情報レイアウト
                            $csv_data .= "1".COMMA;
                            //注文ボタン
                            $csv_data .= "1".COMMA;
                            //資料請求ボタン
                            $csv_data .= "0".COMMA;
                            //商品問い合わせボタン
                            $csv_data .= "1".COMMA;
                            //再入荷お知らせボタン
                            $csv_data .= "0".COMMA;
                            //モバイル表示
//                             $csv_data .= "1".COMMA;
                            //のし対応
                            $csv_data .= "0".COMMA;
                
                            //メーカー名取得
                            $brands = explode("・", $bean['cp_name']);
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
                            $cat = $bean['cp_categories'];
                            $str4 = iconv("utf-8", "Shift_jis", '</font></td></tr><tr><th align="left" bgcolor="DCDCDC"><font size="2" color="000000">説明</font></th>'
                                . '<td bgcolor="ffffff"><font size="2" color="000000">');
                            $str5 = '</font></td></tr>';
                            $str6 = '</table>';
                            //PC用商品説明文
                            $csv_data .= $str1.$bean['cp_number'].$str2.$maker.$str3.strRepacre($bean['cp_detail']).$str4.strRepacre($bean['cp_gg']).$str5.$str6;
                            $csv_data .= COMMA;
                            //モバイル用商品説明文
                            $csv_data .= strRepacre($bean['cp_gg']).COMMA;
                            //スマートフォン用商品説明文
                            $csv_data .= $str1.$bean['cp_number'].$str2.$maker.$str3.strRepacre($bean['cp_detail']).$str4.strRepacre($bean['cp_gg']).$str5.$str6;
                            $csv_data .= COMMA.COMMA;
                            //商品画像URL
                            $csv_data .= $bean['cp_url']." ".$bean['cp_url_1']." ".$bean['cp_url_2']." ".$bean['cp_url_3']." ".$bean['cp_url_4'].COMMA;
                            $csv_data .= COMMA.COMMA.COMMA;
                            //注文受付数
                            $csv_data .= "-1".COMMA;
                            //在庫タイプ
                            $csv_data .= "1".COMMA;
                            //在庫数
                            $csv_data .= $bean['number'].COMMA;
                            //在庫数表示
                            $csv_data .= "1".COMMA;
                            $csv_data .= insertSign(4, COMMA);
                            //サーチ非表示
                            $csv_data .= "0".COMMA;
                            $csv_data .= COMMA.COMMA;
                            //在庫戻しフラグ
                            $csv_data .= "1".COMMA;
                            //在庫切れ時の注文受付
                            $csv_data .= "1".COMMA;
                            //在庫あり時納期管理番号
                            $csv_data .= "3".COMMA;
                            //在庫切れ時納期管理番号
                            $csv_data .= "3".COMMA;
                            //ヘッダー・フッター・レフトナビ
                            //表示項目の並び順
                            //共通説明文（小）
                            //目玉商品
                            $csv_data .= COMMA.COMMA.COMMA;
                            //共通説明文（大）
                            $csv_data .= iconv("utf-8", "Shift_jis", "自動選択".COMMA."自動選択".COMMA."自動選択".COMMA."自動選択".COMMA."自動選択".COMMA);
                            //レビュー本文表示
                            $csv_data .= "2".COMMA;
                            $csv_data .= COMMA.COMMA;
                            $csv_data .= COMMA.COMMA;
                            $csv_data .= COMMA.COMMA.COMMA;
                            $csv_data .= "\n";
    //                         $i++;
    //                         if($i >= FILE_WRITE_LINE) {
    //                             fwrite($myfile, $csv_data);
    //                             $i =0;
    //                             $csv_data="";
    //                         }
                        }
                        fwrite($myfile, $csv_data);
                        $csv_data="";
                    } else {
                        break;
                    }
                }
                fclose($myfile);
                
                //MIMEタイプの設定
//                 header("Content-Type: application/octet-stream");
//                 //名前を付けて保存のダイアログボックスのファイル名の初期値
//                 header("Content-Disposition: attachment; filename={$csv_file}");
            
                // データの出力
                echo("<a href='".$csv_file."'>Download ".$csv_file."</a>".iconv("utf-8", "Shift_jis", "右クリックは、(として保存します)"));
                break;
            case "amazon":
                $excel_file = "../download/Amazon_Store_". date ( "YmdHis" ) .'.xls';
                $myfile = fopen($excel_file, "a") or die("Unable to open file!");
                
                $array = array("配送料無料", "無料", "訳あり", "%OFF", "NEWモデル", "あす楽", "オススメ", "お得", "お買い得", "セール", "Sale", 
                        "メール便", "Mail便", "mail便", "楽ギフ", "割引", "激安", "再入荷", "最新", "新作", "新製品", "新発売", "新品", "即納", 
                        "代引き", "代引", "定価", "同梱不可", "特価", "配送", "発送", "未使用", "予約");
                for ($i=0;$i<count($array); $i++) {
                    $array[$i]= iconv("utf-8", "Shift_jis", $array[$i]);
                }
                //Amazon用Excelファイル作成
                $excel_data = iconv("utf-8", "Shift_jis","TemplateType=Computers\tVersion=2013.1106\t上3行は Amazon.com 記入用です。上3行は変更または削除しないでください。\n");
                $excel_data .= iconv("utf-8", "Shift_jis", "商品管理番号\t商品コード(JANコード等)\t商品コードのタイプ\t商品名\tブランド名\tメーカー名\t商品タイプ\tメーカー型番\t商品説明文\t"
                    . "アップデート・削除\tパッケージ商品数\t商品の販売価格\tメーカー希望価格\t通貨コード\t在庫数\tリードタイム(出荷までにかかる作業日数)\t"
                    . "商品のコンディション\t商品のコンディション説明\t使用しない支払い方法\t配送日時指定SKUリスト\tセール価格\tセール開始日\tセール終了日\t"
                    . "リベート名1\tリベート名2\tリベートメッセージ1\tリベートメッセージ2\tリベート開始日1\tリベート開始日2\tリベート終了日1\tリベート終了日2\t"
                    . "TAXコード\t情報解禁日(mm/dd/yyyy)\t予約商品の販売開始日\t商品の入荷予定日\t最大同梱可能個数\tギフトメッセージ\tギフト包装\tメーカー製造中止\t"
                    . "商品コードなしの理由\t商品の直径\t商品の直径の単位\t配送重量\t発送重量の単位\t商品の幅\t商品の奥行\t商品の高さ\t商品の長さの単位\t商品の重量\t"
                    . "商品の重量の単位\tカタログ番号\t商品説明の箇条書き1\t商品説明の箇条書き2\t商品説明の箇条書き3\t商品説明の箇条書き4\t商品説明の箇条書き5\t"
                    . "検索キーワード1\t検索キーワード2\t検索キーワード3\t検索キーワード4\t検索キーワード5\t検索キーワード6\t検索キーワード7\t検索キーワード8\t検索キーワード9"
                    . "\t検索キーワード10\t推奨ブラウズノード1\t推奨ブラウズノード2\tプラチナキーワード1\t"
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
                        . "generic_keywords5\tgeneric_keywords6\tgeneric_keywords7\tgeneric_keywords8\tgeneric_keywords9\tgeneric_keywords10\t"
                        . "recommended_browse_nodes1\trecommended_browse_nodes2\tplatinum_keywords1\tplatinum_keywords2\t"
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
                

                for ($i=1; $i<=100; $i++) {
                    $results = initPage(PAGE_COUNT, $i);
                    if($results) {
                        foreach ($results as $bean) {
                            $brands = explode("・", $bean['cp_name']);
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
                
                            //商品番号
                            $excel_data .= $bean['cp_number'].TAB.TAB;
                            //商品コードのタイプ
                            $excel_data .= "UPC".TAB;
                            //商品名
                            $cptitle = str_replace($array, "", $bean['cp_title']);
                            $excel_data .= $cptitle.TAB;
                            //ブランド名
                            $excel_data .= $brand.TAB;
                            //メーカー名
                            $excel_data .= $maker.TAB;
                            //商品タイプ
                            $excel_data .= "ComputerComponent".TAB.$bean['cp_helpword'].TAB;
                            //商品説明文PHP_EOL
                            $excel_data .= strRepacre($bean['cp_gg'])."<br>".strRepacre($bean['cp_detail']).TAB;
                            //アップデート・削除
                            $excel_data .= "Update".TAB.TAB;
                            //商品の販売価格
                            $excel_data .= $bean['cp_sale1'].TAB.TAB;
                            //通貨コード
                            $excel_data .= "JPY".TAB;
                            //在庫数
                            $excel_data .= $bean['number'].TAB.TAB.TAB;
                            //商品のコンディション説明
                            $excel_data .= iconv("utf-8", "Shift_jis", "代引き、コンビニ受取&支払いなどにも対応しています。"
                                . "<br>産地品質検査センター設置しており、100％品質保証対応でございます。"
                                . "<br>日本国内本社でお客様対応しております。"
                                . "<br>まとめ買い等もお気軽にご相談ください。".TAB);
                            $excel_data .=insertSign(31, TAB);
                            //商品の重量の単位
                            $excel_data .= "ComputerComponent".TAB.TAB;
                            //商品説明の箇条書き1
                            $excel_data .= $bean['cp_bullet_1'].TAB;
                            //商品説明の箇条書き2
                            $excel_data .= $bean['cp_bullet_2'].TAB;
                            //商品説明の箇条書き3
                            $excel_data .= $bean['cp_bullet_3'].TAB;
                            //商品説明の箇条書き4
                            $excel_data .= $bean['cp_bullet_4'].TAB;
                            //商品説明の箇条書き5
                            $excel_data .= $bean['cp_bullet_5'].TAB;
                            //検索キーワード1
                            $excel_data .= $bean['cp_helpword'].TAB;
                            //検索キーワード2
                            $excel_data .= $bean['cp_helpword_1'].TAB;
                            //検索キーワード3
                            $excel_data .= $bean['cp_helpword_2'].TAB;
                            //検索キーワード4
                            $excel_data .= $bean['cp_helpword_3'].TAB;
                            //検索キーワード5
                            $excel_data .= $bean['cp_helpword_4'].TAB;
                            //検索キーワード6
                            $excel_data .= $bean['cp_helpword_5'].TAB;
                            //検索キーワード7
                            $excel_data .= $bean['cp_helpword_6'].TAB;
                            //検索キーワード8
                            $excel_data .= $bean['cp_helpword_7'].TAB;
                            //検索キーワード9
                            $excel_data .= $bean['cp_helpword_8'].TAB;
                            //検索キーワード10
                            $excel_data .= $bean['cp_helpword_9'].TAB;
                            //推奨ブラウズノード1
                            $excel_data .= $bean['cp_browse_node_1'].TAB;
                            //推奨ブラウズノード2
                            $excel_data .= $bean['cp_browse_node_2'].TAB;
                            $excel_data .= insertSign(5, TAB);
                            //商品メイン画像URL
                            $excel_data .= $bean['cp_url'].TAB;
                            //商品のサブ画像URL1
                            $excel_data .= $bean['cp_url_1'].TAB;
                            //商品のサブ画像URL2
                            $excel_data .= $bean['cp_url_2'].TAB;
                            //商品のサブ画像URL3
                            $excel_data .= $bean['cp_url_3'].TAB;
                            //商品のサブ画像URL4
                            $excel_data .= $bean['cp_url_4'].TAB;
                            $excel_data .= "\n";
                    
                        }
                        fwrite($myfile, $excel_data);
                        $excel_data="";
                    } else {
                        break;
                    }
                }
                fclose($myfile);
                
//                 //MIMEタイプの設定
//                 header("Content-Type: application/vnd.ms-excel");
//                 //名前を付けて保存のダイアログボックスのファイル名の初期値
//                 header("Content-Disposition: attachment;filename={$excel_file}");
                //出力ファイル名の作成
            
                // データの出力
                echo("<a href='".$excel_file."'>Download ".$excel_file."</a>".iconv("utf-8", "Shift_jis", "右クリックは、(として保存します)"));
            
                // データの出力
//                 echo($excel_data);
                
                break;
            case "zone":
                $csv_file = "../download/ZONE_Store_". date ( "YmdHis" ) .'.xls';
                $myfile = fopen($csv_file, "a") or die("Unable to open file!");
                $csv_data = iconv("utf-8", "Shift_jis", "商品名\t商品コード\tmainkc_id\t倉庫号\tゾーン\tバーコード\t在庫数\n");
                
                for ($i=1; $i<=100; $i++) {
                    $results = initPage(PAGE_COUNT, $i);
                    if($results) {
                        foreach ($results as $bean) {
                            // 商品名
                            $csv_data .= $bean['cp_title'].TAB;
                            // 商品コード
                            $csv_data .= $bean['cp_number'].TAB;
                            // mainkc_id
                            $csv_data .= $bean['kid'].TAB;
                            // 倉庫号
                            $csv_data .= $bean['l_id'].TAB;
                            // ゾーン
                            $csv_data .= $bean['l_floor'].'-'.$bean['l_shelf'].'-'.$bean['l_zone'].'-'.$bean['l_horizontal'].'-'.$bean['l_vertical'].TAB;
                            // バーコード
                            $csv_data .= $bean['cp_tm'].TAB;
                            // 在庫数
                            $csv_data .= $bean['number'].TAB;
                    
                            $csv_data .= "\n";
                        }
                        fwrite($myfile, $csv_data);
                        $csv_data="";
                    } else {
                        break;
                    }
                }
                fclose($myfile);
            
//                 //MIMEタイプの設定
//                 header("Content-Type: application/octet-stream");
//                 //名前を付けて保存のダイアログボックスのファイル名の初期値
//                 header("Content-Disposition: attachment; filename={$csv_file}");
                
                //出力ファイル名の作成
                
                // データの出力
                echo("<a href='".$csv_file."'>Download ".$csv_file."</a>".iconv("utf-8", "Shift_jis", "右クリックは、(として保存します)"));
                
                // データの出力
//                 echo($csv_data);
                break;
            case "export":
                $csv_file = "../download/Template_Store_". date ( "YmdHis" ) .'.xls';
                $myfile = fopen($csv_file, "a") or die("Unable to open file!");
                $columnsArray = array();
                for($i=0;$i<100; $i++) {
                    array_push($columnsArray,0);
                }
                $columns=$_REQUEST["columns"];
                foreach ($columns as $column) {
                    $columnsArray[$column]=1;
                }
				$csv_data = "";
					
                //CSVファイル作成
                $array = array("CONTROL","P_CODE","BAR_CODE","PARENT","MAKER","TITLE","EXAMPLE","DETAIL","BULLET1","BULLET2","BULLET3",
                    "BULLET4","BULLET5","BULLET6","CATOGRY","DW","TYPE","PRICE1","PRICE2","PRICE3","S_DATE","E_DATE","JJ","URL","URL1",
                    "URL2","URL3","URL4","BROWSE1","BROWSE2","KEYWORD1","KEYWORD2","KEYWORD3","KEYWORD4","KEYWORD5","KEYWORD6","KEYWORD7",
                    "KEYWORD8","KEYWORD9","KEYWORD10","KID","L_ID","POSITION","NUMBER",
                    "STATE1","STATE2","STATE3","STATE4","STATE5","STATE6","STATE7","STATE8","STATE9","STATE10","NOTE","BZ","ASIN");
                for ($i=0;$i<count($array); $i++) {
					if($columnsArray[$i]){
						$csv_data .= $array[$i].TAB;
					}
                }
				$csv_data .= "\n";
                $array = array("コントロール","商品コード","バーコード","親子関連","メーカ","タイトル","仕様","商品説明","箇条書き1",
							"箇条書き2","箇条書き3","箇条書き4","箇条書き5","箇条書き6","商品分類","単位","商品タイプ","仕入単価",
							"メーカー希望卸売価格","販売価格","生産日付","廃棄日付","仕入先","メインURL"," サブURL1","サブURL2","サブURL3","サブURL4",
							"推奨ブラウズノード1","推奨ブラウズノード2","キーワード1","キーワード2","キーワード3","キーワード4","キーワード5","キーワード6",
                            "キーワード7","キーワード8","キーワード9","キーワード10","mainkc_id","倉庫号","在庫位置","在庫数",
                            "状態1","状態2","状態3","状態4","状態5","状態6","状態7","状態8","状態9","状態10","注釈","備考","ASIN");
                for ($i=0;$i<count($array); $i++) {
					if($columnsArray[$i]){
						$csv_data .= iconv("utf-8", "Shift_jis", $array[$i]).TAB;
					}
                }
				$csv_data .= "\n";
                $index=0;
                for ($i=1; $i<=100; $i++) {
                    $results = initPage(PAGE_COUNT, $i, "export");
                    if($results) {
                        foreach ($results as $bean) {
                            if($columnsArray[$index++]) {
                                $csv_data .= "n".TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_number'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_tm'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_parent'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_name'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_title'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_gg'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_detail'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bullet_1'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bullet_2'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bullet_3'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bullet_4'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bullet_5'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bullet_6'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_categories'].'/'.$bean['cp_categories_down'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_dwname'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_jj'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_sale'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_saleall'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_sale1'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_sdate'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_edate'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_gys'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_url'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_url_1'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_url_2'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_url_3'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_url_4'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_browse_node_1'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_browse_node_2'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_1'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_2'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_3'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_4'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_5'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_6'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_7'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_8'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_helpword_9'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_kid'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_id'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_pos'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_number'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state1'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state2'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state3'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state4'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state5'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state6'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state7'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state8'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state9'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_state10'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_note'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['cp_bz'].'"'.TAB;
                            }
                            if($columnsArray[$index++]) {
                                $csv_data .= '"'.$bean['gc_l_asin'].'"'.TAB;
                            }
                            $csv_data .= "\n";
                            $index =0;
                        }
                        fwrite($myfile, $csv_data);
                        $csv_data="";
                    } else {
                        break;
                    }
                }
                fclose($myfile);
                
//                 //MIMEタイプの設定
//                 header("Content-Type: application/octet-stream");
//                 //名前を付けて保存のダイアログボックスのファイル名の初期値
//                 header("Content-Disposition: attachment; filename={$csv_file}");

                //出力ファイル名の作成
                
                // データの出力
                echo("<a href='".$csv_file."'>Download ".$csv_file."</a>".iconv("utf-8", "Shift_jis", "右クリックは、(として保存します)"));
                
                // データの出力
//                 echo($csv_data);
            default:
                break;
            }
    }
    function insertSign($count, $sign){
        for($i=0; $i<$count; $i++) {
            $str.=$sign;
        }
        return $str;
    }
?>  

