<?php
    require_once '../include/ez_sql_core.php';
    require_once '../include/ez_sql_mysql.php';
    define("COMMA", ",");
    define("FILE_WRITE_LINE", 1000);
    date_default_timezone_set('PRC');
    define("PAGE_COUNT", 300);

    $result = "";
    switch ($_REQUEST["flag"]) {
        case "init":
            $result = initPage();
        break;
        case "out_excel":
            $result = out_excel();
        break;
        
        case "report_sale2_initPage":
            $result = report_sale2_initPage("1");
        break;
        case "report_sale2_out_excel":
            $result = report_sale2_out_excel();
        break;
        
        default:
            return "error";
        break;
    }
    echo $result;  
    
    /**
     * 仕入れ表报告
     */
    function report_sale2_initPage($arg) {
        $sday = $_REQUEST["sday"];
        $eday = $_REQUEST["eday"];
        $s_type = $_REQUEST["s_type"];
        $cp_categories = $_REQUEST["cp_categories"];
        $cp_categories_down = $_REQUEST["cp_categories_down"];
        $newsql = new ezSQL_mysql();
        //计算要join的日期
        // 第1步，获得选择的两个日期之间差的天数
        $ture_days=ceil((strtotime($eday) - strtotime($sday)) / 86400);
        // 第2步，在开始时间的基础上减去相差的天数，得到上一个开始时间, 并且转换回日期格式
        $last_sday = date('Y-m-d H:i:s',strtotime(-$ture_days.' Day',strtotime($sday)));
        
        //设置静态参数 平均数
        if(!empty($_REQUEST["avrg"])) {
            $avrg = $_REQUEST["avrg"];
            $query="update `jxc_static` set p_value = ".$avrg." where p_type = 'AVRAG_PARAM' and p_name='avrg'";
            $newsql->query($query);
        }
        
        //设置state9 = 贩卖总数 /平均数  保留1位小数
//          state9 state6, state7 都是统计的 全部s_type, 页面展示的只是s_type=0的

//         一笑而过  16:29:24
//         a.s_type 贩卖有两种。   0=正常更新 1=amazon
//         THE OTHER WAY  16:34:24
//         那就现状 吧 。以后有需要在调
	    $query="update jxc_mainkc x ,(select sum(a.number)/{$avrg} avrg , a.productid, b.cp_name, b.cp_title, min(a.sale) sale
	    from jxc_sale a,jxc_basic b, jxc_mainkc c
	    where a.del_flag =0 and to_days(a.dtime)>=to_days('$sday')
	    and to_days(a.dtime)<=to_days('$eday')";
	    if(!empty($cp_categories)) {
	        $query .= " and b.cp_categories = '{$cp_categories}'";
	    }
	    if(!empty($cp_categories_down)) {
	        $query .= " and b.cp_categories_down = '{$cp_categories_down}'";
	    }
	    $query .= " and a.productid=b.cp_number
                and a.productid = c.p_id
                GROUP BY a.productid, b.cp_name, b.cp_title
                order by sum(a.number) desc) y
		    set x.l_state9 = round(y.avrg,1)
		    where x.p_id = y.productid";

	    $result = $newsql->get_results($query);
	    
	    //设置state6,设置state7
	    $query="update jxc_mainkc m ,(select x.productid, x.sale*x.avrg/1000 param1, x.sale*(x.avrg - ifnull(y.avrg, 0))/1000 param2 from
    	    (select a.productid, min(a.sale) sale, sum(a.number)/{$avrg} avrg
    	    from jxc_sale a, jxc_mainkc c
    	    where a.del_flag =0
    	    and to_days(a.dtime)>=to_days('$sday') 
            and to_days(a.dtime)<=to_days('$eday')
    	    and a.productid = c.p_id
    	    GROUP BY a.productid) x left join
    	    (select a.productid, min(a.sale) sale, sum(a.number)/{$avrg} avrg
    	    from jxc_sale a, jxc_mainkc c
    	    where a.del_flag =0 
    	    and to_days(a.dtime)>=to_days('$last_sday') 
            and to_days(a.dtime)<=to_days('$sday')
	        and a.productid = c.p_id
	        GROUP BY a.productid) y
	        on x.productid = y.productid) n
		    set m.l_state6 = n.param2,
		        m.l_state7 = n.param1
		    where m.p_id = n.productid";
	    $result = $newsql->get_results($query);
	    
	    //设置静态参数 时间区间
	    $query="update jxc_static m set m.p_value='{$sday}~{$eday}' where m.p_type = 'REPORT_DATE' and m.p_name = 'date_last_term'";
	    $result = $newsql->get_results($query);
	    $query="update jxc_static m set m.p_value='{$last_sday}~{$sday}' where m.p_type = 'REPORT_DATE' and m.p_name = 'date_this_term'";
	    $result = $newsql->get_results($query);
	        
	        
	    //页面显示
        $query="select x.productid, x.cp_name, x.cp_title, x.cp_detail, x.sale, x.number, x.avrg, x.remain_left, ifnull(y.number, 0) last_number, ifnull(y.avrg, 0) last_avrg from 
            (select a.productid, b.cp_name, b.cp_title, b.cp_detail, min(a.sale) sale, sum(a.number) number, sum(a.number)/{$avrg} avrg, c.number-sum(a.number)/{$avrg} remain_left 
             from jxc_sale a,jxc_basic b, jxc_mainkc c 
             where a.del_flag =0";
             if($s_type >=0 ) {
                $query .= " and a.s_type =$s_type";
             }
             $query .= " and to_days(a.dtime)>=to_days('$sday') 
             and to_days(a.dtime)<=to_days('$eday')";
    	    if(!empty($cp_categories)) {
    	        $query .= " and b.cp_categories = '{$cp_categories}'";
    	    }
    	    if(!empty($cp_categories_down)) {
    	        $query .= " and b.cp_categories_down = '{$cp_categories_down}'";
    	    }
            $query .= " and a.productid=b.cp_number 
            and a.productid = c.p_id
            GROUP BY a.productid, b.cp_name, b.cp_title) x left join 
            (select a.productid, b.cp_name, b.cp_title, b.cp_detail, min(a.sale) sale, sum(a.number) number, sum(a.number)/{$avrg} avrg, c.number-sum(a.number)/{$avrg} remain_left 
             from jxc_sale a,jxc_basic b, jxc_mainkc c 
             where a.del_flag =0";
             if($s_type >=0 ) {
                $query .= " and a.s_type =$s_type";
             }
             $query .= " and to_days(a.dtime)>=to_days('$last_sday') 
             and to_days(a.dtime)<=to_days('$sday')";
    	    if(!empty($cp_categories)) {
    	        $query .= " and b.cp_categories = '{$cp_categories}'";
    	    }
    	    if(!empty($cp_categories_down)) {
    	        $query .= " and b.cp_categories_down = '{$cp_categories_down}'";
    	    }
            $query .= " and a.productid=b.cp_number 
            and a.productid = c.p_id
            GROUP BY a.productid, b.cp_name, b.cp_title) y
            on x.productid = y.productid
        order by x.number desc";
        $result = $newsql->get_results($query);
        if($arg=="excel") {
            return $result;
        }
		return "{\"sday\":\"{$sday}\",\"eday\":\"{$eday}\",\"last_sday\":\"{$last_sday}\",\"results\":".json_encode($result, JSON_FORCE_OBJECT)."}";

    }
    function initPage() {
        $newsql = new ezSQL_mysql();
        $result= array();
        $stext = trim($_REQUEST["stext"]);
        
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $query = "SELECT IFNULL(s_count, 0) s_count, IFNULL(in_count, 0) in_count, IFNULL(out_count, 0) out_count, dtime 
           FROM `jxc_report_storage` WHERE `productid` = '{$stext}'";
        if(!empty($sdate)){
            $query .= " and dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and dtime <= '{$edate}'";
        }
        $query .= " order by dtime";
        $result = $newsql->get_results($query);
		return json_encode($result, JSON_FORCE_OBJECT);
    }
    
    function prepare_data($pageCount, $pageIndex) {
        $stext = trim($_REQUEST["stext"]);
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        
        $query = "SELECT `productid`, IFNULL(s_count, 0) s_count, IFNULL(in_count, 0) in_count, IFNULL(out_count, 0) out_count, dtime
            FROM `jxc_report_storage` WHERE 1=1";
        if($stext) {
            $query .= " and (`productid` like '{$stext}%'";
        }
        if(!empty($sdate)){
            $query .= " and dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and dtime <= '{$edate}'";
        }
        $query .= " order by dtime";
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $newsql = new ezSQL_mysql();
        return $newsql->get_results($query);
    }
    
    function out_excel() {
        $csv_file = "../download/Report_". date ( "YmdHis" ) .'.csv';
        $myfile = fopen($csv_file, "w") or die("Unable to open file!");
        
        $csv_data = iconv("utf-8", "Shift_jis", "商品コード,在庫,入庫,出庫,日期\n");
        
        for ($i=1; $i<=100; $i++) {
            $results = prepare_data(PAGE_COUNT, $i);
            if($results) {
                foreach($results as $result) {
                    $csv_data .= $result->productid.COMMA;
                    $csv_data .= $result->s_count.COMMA;
                    $csv_data .= $result->in_count.COMMA;
                    $csv_data .= $result->out_count.COMMA;
                    $csv_data .= $result->dtime.COMMA;
                    $csv_data .= "\n";
                }
                fwrite($myfile, $csv_data);
                $csv_data="";
            }
        }
        fclose($myfile);
        //出力ファイル名の作成
//         $csv_file = date ( "YmdHis" ) .'.csv';
//         //MIMEタイプの設定
//         header("Content-Type: application/octet-stream");
//         //名前を付けて保存のダイアログボックスのファイル名の初期値
//         header("Content-Disposition: attachment; filename={$csv_file}");
        
//         // データの出力
//         echo($csv_data);
        
        

        //出力ファイル名の作成
        
        // データの出力
        echo("<a href='".$csv_file."'>Download ".$csv_file."</a>".iconv("utf-8", "Shift_jis", "右クリックは、(として保存します)"));
        exit;
    }
    
    function report_sale2_out_excel() {
        $csv_file = "../download/Report_". date ( "YmdHis" ) .'.csv';
        $myfile = fopen($csv_file, "w") or die("Unable to open file!");
        
        $csv_data = iconv("utf-8", "Shift_jis", "商品コード,タイトル,仕様,販売単価,平均数,合計数,上期平均数,上期合計数,仕入れ数,総合金額\n");
        
        for ($i=1; $i<=100; $i++) {
            $results = report_sale2_initPage("excel");
            if($results) {
                foreach($results as $result) {
                    $csv_data .= $result->productid.COMMA;
                    $csv_data .= $result->cp_title.COMMA;
                    $csv_data .= $result->cp_detail.COMMA;
                    $csv_data .= $result->sale.COMMA;
                    $csv_data .= $result->avrg.COMMA;
                    $csv_data .= $result->number.COMMA;
                    $csv_data .= $result->last_avrg.COMMA;
                    $csv_data .= $result->last_number.COMMA;
                    $csv_data .= $result->remain_left.COMMA;
                    $csv_data .= $result->number*$result->sale.COMMA;
                    $csv_data .= "\n";
                }
                fwrite($myfile, $csv_data);
                $csv_data="";
            }
        }
        fclose($myfile);
        //出力ファイル名の作成
        
        // データの出力
        echo("<a href='".$csv_file."'>Download ".$csv_file."</a>".iconv("utf-8", "Shift_jis", "右クリックは、(として保存します)"));
        exit;
    }
    
?>  
