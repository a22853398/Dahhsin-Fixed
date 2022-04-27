<?php
include '../commonfile/config_mang.inc.php';
include $config['templatebpath'] . 'class.TemplatePower.inc.php';
include '../commonfile/pageft_mang.inc.php';
include '00security_function.php';
$tpl_str       = '折價活動商品銷售查詢';
$tpl_name      = 'order_SaleSearchEvent';
$table_name    = 'order_list';
$owner_type    = 'mang';
$owner_array01 = owner_check01($table_name);

//$opt                 = strip_input('inputadm', $_REQUEST['opt']);
$pid                 = strip_input('inputadm', $_REQUEST['pid']);//頁碼
$page                = strip_input('inputadm', $_REQUEST['page']);//頁數
$sel00_type          = strip_input('inputadm', $_REQUEST['sel00_type']);
//$search_key1         = strip_input('inputadm', $_REQUEST['search_key1']);
//$search_orderid      = strip_input('inputadm', $_REQUEST['search_orderid']);
//$search_process_type = strip_input('inputadm', $_REQUEST['search_process_type']);
//$search_pay_type     = strip_input('inputadm', $_REQUEST['search_pay_type']);
//$search_orderdate1   = strip_input('inputadm', $_REQUEST['search_orderdate1']);
//$search_orderdate2   = strip_input('inputadm', $_REQUEST['search_orderdate2']);


#--- 接收HTML欄位的值 ---#
$start_date = strip_input('inputadm', $_REQUEST['start_date']);//起始日期
$end_date = strip_input('inputadm', $_REQUEST['end_date']);//結束日期
$search_type = strip_input('inputadm', $_REQUEST['search_type']);//搜尋方式
$search_key = strip_input('inputadm', $_REQUEST['search_key']);//搜尋關鍵字
$search_type2 = strip_input('inputadm', $_REQUEST['search_type2']);//搜尋方式二



$tpl = new TemplatePower('./' . $tpl_name . '.htm');//輸出的網址
$tpl->assignInclude('external', './00external.php');
$tpl->assignInclude('mainMenu', './00mainMenu.php');
$tpl->prepare();

$tpl->assignglobal('tpl_str', $tpl_str);
$tpl->assignglobal('tpl_name', $tpl_name);

$tpl->gotoBlock('_ROOT');

//輸出分類
$strSQL1 = "select '1' as list_id, '活動商品銷售數量查詢' as list_title
";
$res = SqlQuery1($strSQL1);
while (!$res->EOF) {
    $tpl->newBlock('tablist01_row');
    if ($sel00_type == '') {
        $sel00_type = $res->fields['list_id'];
    }
    if ($res->fields['list_id'] == $sel00_type) {
        $tpl->assign('sel00_type_sel', 'id="tab_active"');
        $tpl->assign('lv00_current_sel', 'id="tab_current"');
    }
    $tpl->assign('list_id', $res->fields['list_id']);
    $tpl->assign('list_title', $res->fields['list_title']);
    $res->MoveNext();
}
//輸出分類

$tpl->gotoBlock('_ROOT');
//Tab UI選單顏色
$tpl->assign('sel00_type' . $sel00_type, 'id="tab_active"');
$tpl->assign('lv00_current' . $sel00_type, 'id="tab_current"');
//Tab UI選單顏色



//基本變數
$tpl->assign('sel00_type', $sel00_type);
if ($sel00_type == 'S1') {
    $tpl->newBlock('block_row01_row');
    $tpl->gotoBlock('_ROOT');
}

$tpl->assign('search_orderid', $search_orderid);
$tpl->assign('search_process_type' . $search_process_type, 'selected="selected"');
$tpl->assign('search_pay_type' . $search_pay_type, 'selected="selected"');
$tpl->assign('search_orderdate1', $search_orderdate1);
$tpl->assign('search_orderdate2', $search_orderdate2);



//基本變數

####################################################################################################
//資料列表
/*
$tpl->gotoBlock('_ROOT');
switch ($sel00_type) {
case 'S1':
    $searchstr = "";
    if ($search_key1 != "") {
        $searchstr .= "OR a.gcname like '%" . $search_key1 . "%' OR a.orderid like '%" . $search_key1 . "%' ";
    }

    if ($searchstr != "") {
        $searchstr = "and ( " . substr($searchstr, 2, strlen($searchstr)) . " ) ";
    }

    if ($search_pay_type != "") {
        $searchstr .= " and a.pay_type = '" . $search_pay_type . "' ";
    }

    if ($search_orderdate1 != "") {
        $searchstr .= " and a.add_date >= '" . $search_orderdate1 . "' and a.add_date <= '" . $search_orderdate2 . "' ";
    }

    if ($search_orderid != "") {
        $searchstr .= " and a.orderid like '%" . $search_orderid . "%' ";
    }

    if ($search_process_type != "") {
        $searchstr .= " and a.process_type = '" . $search_process_type . "' ";
    }

    $strSQL1 = "select a.orderid,a.ord_price,a.mbr_serialid,a.gcname,a.gcname_ex,a.pay_type,a.pay_typestr,a.tran_type,a.tran_typestr,a.add_date,a.process_type,a.pay_check,a.add_ipaddress,
  	(select var01 from shop_defset where lv00_type = '6' and var03 = a.process_type and visible = 'Y' ) as process_typestr,
  	(select var01 from shop_defset where lv00_type = '7' and var03 = a.pay_check and visible = 'Y' ) as pay_check_typestr
  	from " . $table_name . " as a
  	where 1=1 " . $searchstr . " order by a.orderid DESC ";
    $strSQL2 = "select COUNT(*) from (select a.orderid from " . $table_name . " as a where 1=1 " . $searchstr . "  ) as tmp1 ";
    break;

default:
    $strSQL1 = "select a.orderid,a.ord_price,a.mbr_serialid,a.gcname,a.gcname_ex,a.pay_type,a.pay_typestr,a.tran_type,a.tran_typestr,a.add_date,a.process_type,a.pay_check,a.add_ipaddress,
  	(select var01 from shop_defset where lv00_type = '6' and var03 = a.process_type and visible = 'Y' ) as process_typestr,
  	(select var01 from shop_defset where lv00_type = '7' and var03 = a.pay_check and visible = 'Y' ) as pay_check_typestr
  	from " . $table_name . " as a
  	where 1=1
  	order by a.orderid desc ";
    $strSQL2 = "select COUNT(a.orderid) from " . $table_name . " as a where 1=1 ";
    break;
}
//echo $strSQL1.'<br />';
//echo $strSQL2.'<br />';
*/
/*
$total = GetQueryValueCount1($strSQL2, 'value');
pageft($total, $page_splt01);
$res = SqlQueryLimit1($strSQL1, $displaypg, $firstcount);
*/
/*
$tpl->assign('pagenav', $pagenav);
$tpl->assign('pagejump01', $pagejump01);
$tpl->assign('page_splt01_add', ($page_splt01 * ($page - 1)));
$tpl->assign('page_splt01', $page_splt01);
$tpl->assign('page', $page);
*/
/*
$i = 0;
while (!$res->EOF) {
    $i++;
    $tpl->newBlock('Table1_row');
    $tpl->assign('rowid01ck', ($i % 2 == 1) ? 'class="odd"' : '');

    //if($owner_array01[2] == '1'){
    /*
    if($sel00_type == 'X1'){
    if($owner_array01[3] == '1')
    $tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del_real" title="刪除" alt="刪除" />');
    if($owner_array01[2] == '1')
    $tpl->assign('bt_undel', '<img src="js/icon/accept.png" class="bt_undel" title="啟用" alt="啟用" />');
    }else{
    if($owner_array01[2] == '1')
    $tpl->assign('bt_undel', '<img src="js/icon/delete.png" class="bt_del" title="停用" alt="停用" />' );
    }
     */
/*
    if ($owner_array01[3] == '1' && ($res->fields['batch_date'] == '' || $res->fields['batch_date'] == '0000-00-00 00:00:00') && $res->fields['OrderFreightType'] == '0') {
        $tpl->assign('bt_del', '<img src="js/icon/cross.png" class="bt_del" title="取消" alt="取消" />');
    }

    $tpl->assign('bt_edit', '<img src="js/icon/page_edit.png" class="bt_edit" title="檢視" alt="檢視" />');
    //}

    $tpl->assign('serialid', $res->fields['orderid']);
    $tpl->assign("serialidbt", '<input type="hidden" name="serialid[]" value="' . $res->fields['orderid'] . '" />');
    //$tpl->assign('sortid', $res->fields['sortid'] );
    //$tpl->assign("sortid", '<input type="text" size="3" name="sortid[]" value="'.$res->fields['sortid'].'" title="'.$res->fields['sortid'].'" />' );

    $tpl->assign('orderid', $res->fields['orderid']);
    $tpl->assign('ord_price', $res->fields['ord_price']);
    $tpl->assign('mbr_serialid', $res->fields['mbr_serialid']);
    $tpl->assign('gcname', $res->fields['gcname']);
    $tpl->assign('gcname_ex', $res->fields['gcname_ex']);
    $tpl->assign('pay_type', $res->fields['pay_type']);

    //if($res->fields['pay_type'] == '02')
    //  $tpl->assign('pay_typestr', '<span style="color: red;">'.$res->fields['pay_typestr'].'</style>' );
    //else
    $tpl->assign('pay_typestr', $res->fields['pay_typestr']);

    $tpl->assign('tran_type', $res->fields['tran_type']);
    $tpl->assign('tran_typestr', $res->fields['tran_typestr']);

    if ($res->fields['process_type'] == '04') {
        $tpl->assign('process_typestr', '<span style="color: red;">' . $res->fields['process_typestr'] . '</style>');
    } else {
        $tpl->assign('process_typestr', $res->fields['process_typestr']);
    }

    if ($res->fields['pay_check'] == '02') {
        $tpl->assign('pay_check_typestr', '<span style="color: red;">' . $res->fields['pay_check_typestr'] . '</style>');
    } else {
        $tpl->assign('pay_check_typestr', $res->fields['pay_check_typestr']);
    }

    $tpl->assign('add_date', $res->fields['add_date']);

    $arr01 = explode(",", $res->fields['add_ipaddress']);
    //$tpl->assign('add_ipaddress', $res->fields['add_ipaddress']);
    $tpl->assign('add_ipaddress', $arr01[1]);

    $res->MoveNext();
}

$tpl->gotoBlock('_ROOT');
//輸出處理狀態
$strSQL1 = "select var01,var02,var03 from shop_defset where lv00_type = '6' and visible = 'Y' order by sortid ";
//echo $strSQL1.'<br />';
$res = SqlQuery1($strSQL1);
while (!$res->EOF) {
    $tpl->newBlock('process_type_row');
    if ($res->fields['var03'] == $search_process_type) {
        $tpl->assign('list_sel', 'selected="selected"');
    }
    $tpl->assign('list_id', $res->fields['var03']);
    $tpl->assign('list_title', $res->fields['var01']);
    $res->MoveNext();
}
//輸出處理狀態

$tpl->gotoBlock('_ROOT');
//輸出付款方式
$strSQL1 = "select var01,var02,var03 from shop_defset where lv00_type = '3' and visible = 'Y' order by sortid ";
//echo $strSQL1.'<br />';
$res = SqlQuery1($strSQL1);
while (!$res->EOF) {
    $tpl->newBlock('pay_type_row');
    if ($res->fields['var03'] == $search_pay_type) {
        $tpl->assign('list_sel', 'selected="selected"');
    }
    $tpl->assign('list_id', $res->fields['var03']);
    $tpl->assign('list_title', $res->fields['var01']);
    $res->MoveNext();
}
*/
//輸出付款方式
####################################################################################################

#--- 擷取標題純文字，捨去html，回傳 String ---#
function pickUpTitleText($string){
    $t1 = strpos($string, ">");//判斷是不是彩色標題 int，找第一個 ">" 在哪
    $t2 = strrpos($string, "<");//int從後面找第一個 "<" 在哪
    $t3 = substr($string, $t1+1, $t2-($t1+1));//擷取字串，string $t1要+1是因為會連">"一起抓，第三個參數為長度，$t2-($t1+1)因為前面的起始位置是$t1+1，所以要一起算
    if($t1 > 0){
        return $t3;
    }else{
        return $string;
    }
    //return $t1;
}
#--- 日期字串 ---#
$currentTime = date('Y-m-d', strtotime("now"));//現在時間
//如果沒有選時間的話，時間初始化
if($start_date == ""){
    $start_date = date('Y-m-01', strtotime("now"));//時間初始化為本月
}
if($end_date == ""){
    $end_date = $currentTime;
}
$end_date_SQL = date('Y-m-d', strtotime($end_date."+1 day"));//因為SQL的時間不會包含後面的，所以後面要加一天


#--- 現役促銷活動名稱 ---#
$strSQL01 = "
    SELECT title01,
        serialid,
        news_date,
        news_end_date
    FROM discount_event01
    WHERE visible='Y'
    AND news_end_date >= '".$currentTime."'
    AND news_date <= '".$currentTime."'
";
$res01 = SqlQuery1($strSQL01);

$list01 = array();//陣列，下面最後轉json給html存取時間用的
while(!$res01->EOF){
    $title01 = $res01 -> fields['title01'];//取出SQL值
    $title01Text = pickUpTitleText($title01);
    $event_value01 = $res01 -> fields['serialid'];//活動編號
    $event_start_date01 = $res01 -> fields['news_date'];//活動開始日期
    $event_end_date01 = $res01 -> fields['news_end_date'];//活動結束日期
    
    //加入到 $list01 陣列
    $ary_temp = array( 
        'title' => $title01Text, 
        'value' => $event_value01, 
        'st_date' => $event_start_date01, 
        'ed_date' => $event_end_date01
        );
    array_push($list01, $ary_temp);
    
    //指派給HTML，要搭配 <!-- START BLOCK : dis_event_now -->的標籤使用
    $tpl->newBlock('dis_event_now');
    $tpl->assign('dis_event_now', $title01Text);//指派給HTML
    $tpl->assign('dis_event_now_serialid', $event_value01);
    $tpl->assign('dis_event_now_start', $event_start_date01);
    $tpl->assign('dis_event_now_end',$event_end_date01);
    $res01->MoveNext();
}
$tpl->gotoBlock('_ROOT');
#--- 現役促銷活動名稱 END ---#

#--- 過期非停用促銷活動名稱 ---#
$strSQL02 = "
    SELECT title01, 
        serialid,
        news_date,
        news_end_date
    FROM discount_event01
    WHERE visible='Y'
    AND news_end_date < '".$currentTime."'
";
$res02 = SqlQuery1($strSQL02);
while(!$res02->EOF){
    $title02 = $res02 -> fields['title01'];//取出SQL值
    $title02Text= pickUpTitleText($title02);//擷取純文字
    $event_value02 = $res02 -> fields['serialid'];//活動編號
    $event_start_date02 = $res02 -> fields['news_date'];//活動開始日期
    $event_end_date02 = $res02 -> fields['news_end_date'];//活動結束日期
    
    //加入到 $list01 陣列
    $ary_temp = array( 
        'title' => $title02Text, 
        'value' => $event_value02, 
        'st_date' => $event_start_date02, 
        'ed_date' => $event_end_date02
        );
    array_push($list01, $ary_temp);
    
    //指派給HTML
    $tpl->newBlock('dis_event_past');
    $tpl->assign('dis_event_past', $title02Text);//指派給HTML
    $tpl->assign('dis_event_past_serialid', $event_value02);
    $tpl->assign('dis_event_past_start', $event_start_date02);
    $tpl->assign('dis_event_past_end',$event_end_date02);
    $res02->MoveNext();
}
$tpl->gotoBlock('_ROOT');
#--- 過期非停用促銷活動名稱 END ---#

#--- 現行任選活動名稱 ---#
$strSQL03 = "
    SELECT title01,
        serialid,
        news_date,
        news_end_date
    FROM choose_event01
    WHERE visible='Y'
    AND news_end_date >= '".$currentTime."'
    AND news_date <= '".$currentTime."'
";
$res03 = SqlQuery1($strSQL03);
while(!$res03->EOF){
    $title03 = $res03 -> fields['title01'];//取出SQL值
    $title03Text = pickUpTitleText($title03);//擷取純文字
    $event_value03 = $res03 -> fields['serialid'];//活動編號
    $event_start_date03 = $res03 -> fields['news_date'];//活動開始日期
    $event_end_date03 = $res03 -> fields['news_end_date'];//活動結束日期
    
    //加入到 $list01 陣列
    $ary_temp = array( 
        'title' => $title03Text, 
        'value' => $event_value03, 
        'st_date' => $event_start_date03, 
        'ed_date' => $event_end_date03
        );
    array_push($list01, $ary_temp);
    
    //指派給HTML
    $tpl->newBlock('choose_event_now');
    $tpl->assign('choose_event_now', $title03Text);//指派給HTML
    $tpl->assign('choose_event_now_serialid', $event_value03);
    $tpl->assign('choose_event_now_start', $event_start_date03);
    $tpl->assign('choose_event_now_end',$event_end_date03);
    $res03->MoveNext();
}
$tpl->gotoBlock('_ROOT');
#--- 現行任選活動名稱 END ---#

#--- 過期任選非停用活動名稱 ---#
$strSQL04 = "
    SELECT title01,
        serialid,
        news_date,
        news_end_date
    FROM choose_event01
    WHERE visible='Y'
    AND news_end_date < '".$currentTime."'
";
$res04 = SqlQuery1($strSQL04);
while(!$res04->EOF){
    $title04 = $res04 -> fields['title01'];//取出SQL值
    $title04Text= pickUpTitleText($title04);//擷取純文字
    $event_value04 = $res04 -> fields['serialid'];//活動編號
    $event_start_date04 = $res04 -> fields['news_date'];//活動開始日期
    $event_end_date04 = $res04 -> fields['news_end_date'];//活動結束日期
    
    //加入到 $list01 陣列
    $ary_temp = array( 
        'title' => $title04Text, 
        'value' => $event_value04, 
        'st_date' => $event_start_date04, 
        'ed_date' => $event_end_date04
        );
    array_push($list01, $ary_temp);
    
    //指派給HTML
    $tpl->newBlock('choose_event_past');
    $tpl->assign('choose_event_past', $title04Text);//指派給HTML
    $tpl->assign('choose_event_past_serialid', $event_value04);
    $tpl->assign('choose_event_past_start', $event_start_date04);
    $tpl->assign('choose_event_past_end',$event_end_date04);
    $res04->MoveNext();
}
$tpl->gotoBlock('_ROOT');
#--- 過期任選非停用活動名稱 END ---#

//SQL字串範本
$strSQL_sold ="
    SELECT 
        order_list.orderid AS orderid,
        order_list.gcname AS gcname,
        order_list.add_date AS add_date,
        order_listdetail.prod_name AS prod_name,
        SUM(order_listdetail.ord_qty) AS ord_qty,
        order_listdetail.ord_price_add AS ord_price,
        order_list.add_ipaddress AS add_ipaddress,
        order_listdetail.prod_num AS prod_num,
        order_listdetail.ord_type AS ord_type,
        order_listdetail.ord_typestr AS ord_typestr,
        discount_event01_sublist01.lv01_id AS dis_event
    FROM order_list
    JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
    JOIN discount_event01_sublist01 ON discount_event01_sublist01.var01 = order_listdetail.prod_num
    WHERE 1=1
    AND order_list.process_type NOT IN ('X1','X2')
    AND order_listdetail.ord_type NOT IN ('T01','G01','C01')
    AND order_list.add_date  BETWEEN '2021-06-01' AND '2021-06-30'
    AND discount_event01_sublist01.lv01_id IN ('A21052400019885')
    GROUP BY order_listdetail.prod_num, order_listdetail.ord_type
    ORDER BY ord_qty DESC
";
/*
SELECT 
        order_list.orderid AS orderid,
        order_list.gcname AS gcname,
        order_list.add_date AS add_date,
        order_listdetail.prod_name AS prod_name,
        SUM(order_listdetail.ord_qty) AS ord_qty,
        order_listdetail.ord_price_add AS ord_price,
        order_list.add_ipaddress AS add_ipaddress,
        order_listdetail.prod_num AS prod_num,
        order_listdetail.ord_type AS ord_type,
        order_listdetail.ord_typestr AS ord_typestr,
        discount_event01_sublist01.lv01_id AS dis_event
    FROM order_list
    JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
    JOIN discount_event01_sublist01 ON discount_event01_sublist01.var01 = order_listdetail.prod_num
    WHERE order_list.process_type NOT IN ('X1','X2')
    AND order_listdetail.ord_type NOT IN ('T01','G01','C01')
    AND order_list.add_date  BETWEEN '2021-06-01' AND '2021-07-01'
    AND discount_event01_sublist01.lv01_id IN ('A21052400019885')
    GROUP BY order_listdetail.prod_num, order_listdetail.ord_type
    ORDER BY ord_qty DESC
*/

//下面要回傳給HTML顯示的字串
$search_type_str ="";
//搜尋條件的SQL文
$strSQL_selected="";//搜尋方法，任選館OR促銷
$strSQL_selected_value="";//選擇的活動
$strSQL_selected_ord_type="";//商品類型
switch($search_type){
    case 'search_type_dis_event':
        $strSQL_selected = "JOIN discount_event01_sublist01 ON discount_event01_sublist01.var01 = order_listdetail.prod_num";
        $strSQL_selected_value = "AND discount_event01_sublist01.lv01_id = '".$search_type2."' ";
        $strSQL_selected_ord_type = "AND order_listdetail.ord_type = 'D01' ";
        $search_type_str = "促銷折扣館";
        break;
    case 'search_type_choose_event':
        $strSQL_selected = "JOIN choose_event01_sublist01 ON choose_event01_sublist01.var01 = order_listdetail.prod_num";
        $strSQL_selected_value = "AND choose_event01_sublist01.lv01_id = '".$search_type2."' ";
        $strSQL_selected_ord_type = "AND order_listdetail.ord_type = 'F01' ";
        $search_type_str = "任選館";
        break;
}
$strSQL_search_key="";//關鍵字
if($search_key !== ""){
    $strSQL_search_key .= "AND" . "(
                                    order_listdetail.prod_num LIKE'%".$search_key."%' 
                                OR  order_listdetail.prod_name LIKE'%".$search_key."%'
                                OR  order_listdetail.prod_namesub LIKE '%".$search_key."%'
                                )";
}


//搜尋SQL
$strSQL_sold1="
    SELECT 
        order_list.orderid AS orderid,
        order_list.gcname AS gcname,
        order_list.add_date AS add_date,
        order_list.add_ipaddress AS add_ipaddress,
        order_listdetail.prod_name AS prod_name,
        SUM(order_listdetail.ord_qty) AS ord_qty,
        order_listdetail.ord_price_add AS ord_price,
        order_listdetail.prod_num AS prod_num,
        order_listdetail.ord_type AS ord_type,
        order_listdetail.ord_typestr AS ord_typestr,
        order_listdetail.prod_namesub AS prod_namesub
        
    FROM order_list
    JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
    ".$strSQL_selected."
    WHERE 1=1
    AND order_list.process_type NOT IN ('X1','X2')
    AND order_listdetail.ord_type NOT IN ('T01','G01','C01')
    AND order_list.add_date  BETWEEN '".$start_date."' AND '".$end_date_SQL."'
    ".$strSQL_search_key."
    ".$strSQL_selected_value."
    ".$strSQL_selected_ord_type."
    GROUP BY order_listdetail.prod_num, order_listdetail.ord_type, order_listdetail.ord_price_add
    ORDER BY ord_qty DESC
";

//分頁，計算筆數的SQL
$strSQL2_sold1 = "
    SELECT COUNT(*) FROM (".$strSQL_sold1.") AS tmp1
";

//印出資料&計算分頁
if($search_type == ""){
    //不做事
}else{
    //筆數&分頁
    $total_order = GetQueryValueCount1($strSQL2_sold1, 'value');//計算筆數
    pageft($total_order, $page_splt01);
    $resSold01 = SqlQueryLimit1($strSQL_sold1, $displaypg, $firstcount);
    $tpl->assign('pagenav', $pagenav);
    $tpl->assign('pagejump01', $pagejump01);
    $tpl->assign('page_splt01_add', ($page_splt01 * ($page - 1)));
    $tpl->assign('page_splt01', $page_splt01);
    $tpl->assign('page', $page);
    
    
    //$resSold01 = SqlQuery1($strSQL_sold1);
    $j = 0;//用來判斷奇偶數的數
    while(!$resSold01->EOF){
        $j++;
        //指派給HTML，搭配註釋標籤
        $tpl->newBlock('Table2_row');
        //一行與另一行顏色不同
        $tpl->assign('rowid02ck', ($j % 2 == 1) ? 'class="odd"' : '');
        $tpl->assign('orderid', $resSold01->fields['orderid']);//訂單編號
        $tpl->assign('prod_num', $resSold01->fields['prod_num']);//商品編號
        $tpl->assign('sold_qty', $resSold01->fields['ord_qty']);//賣出數量
        $tpl->assign('add_date', $resSold01->fields['add_date']);//新增時間
        $tpl->assign('ord_type', $resSold01->fields['ord_type']);//商品類型
        $tpl->assign('ord_typestr', $resSold01->fields['ord_typestr']);//商品類型說明
        $tpl->assign('prod_name', $resSold01->fields['prod_name']);//商品名稱
        $tpl->assign('prod_price', $resSold01->fields['ord_price']);//商品單價
        $tpl->assign('gcname', $resSold01->fields['gcname']);//訂購姓名
        $tpl->assign('add_ipaddress', $resSold01->fields['add_ipaddress']);//新增位址
        $tpl->assign('prod_namesub', $resSold01->fields['prod_namesub']);//商品副標
        
        $resSold01->MoveNext();
    }
    $tpl->gotoBlock('_ROOT');
}


#--- 傳回到HTML ---#

$tpl->assign('start_date', $start_date);//起始日期
$tpl->assign('end_date', $end_date);//結束日期
$tpl->assign('search_type', $search_type);//搜尋方式
$tpl->assign('search_type2', $search_type2);//搜尋方式二(值/編碼)
$tpl->assign('search_key', $search_key);//關鍵字
$tpl->assign('search_type_str',$search_type_str);//顯示於html搜尋方法的字串
$tpl->assign('json_event',json_encode($list01));//給html存取資料用的陣列轉json
//print_r($list01);
/*
for($i = 0; $i<count($list01); $i++){
    echo "<br>";
    foreach($list01[$i] as $w => $x){
        if($w == 'st_date'){
            echo $x;    
        }
    }
}
*/
//echo json_encode($list01);
//搜尋方法二顯示的文字
/*
    $v="";
    $t="";
    $tp_ary = array();
    for($i = 0; $i<count($list01); $i++){
        foreach($list01[$i] as $w => $x){
            if($w == 'value'){//把陣列中的值拿出來
                $v = $x;
                echo $v."<br>";
            }
        }
        foreach($list01[$i] as $w => $x){
            if($w == 'title'){
                $t = $x;   
            }
        }
    }
*/    
    //echo $list01[0]['value'];

$search_type2_str= "";
    for($i = 0; $i < count($list01); $i++){
        if($list01[$i]["value"] == $search_type2){
            $search_type2_str = $list01[$i]["title"];
        }
    }
$tpl->assign('search_type2_str', $search_type2_str);//搜尋方式二(呈現的文字)




$tpl->printToScreen();
?>
