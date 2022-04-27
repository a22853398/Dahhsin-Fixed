<?php
include '../commonfile/config_mang.inc.php';
include $config['templatebpath'] . 'class.TemplatePower.inc.php';
include '../commonfile/pageft_mang.inc.php';
include '00security_function.php';
$tpl_str       = '銷售排行查詢';
$tpl_name      = 'order_SaleRank';
$table_name    = 'order_list';
$owner_type    = 'mang';
$owner_array01 = owner_check01($table_name);


$pid                 = strip_input('inputadm', $_REQUEST['pid']);//頁碼
$page                = strip_input('inputadm', $_REQUEST['page']);//頁數
$sel00_type          = strip_input('inputadm', $_REQUEST['sel00_type']);


#--- 接收HTML欄位的值 ---#
$start_date = strip_input('inputadm', $_REQUEST['start_date']);//起始日期
$end_date = strip_input('inputadm', $_REQUEST['end_date']);//結束日期
$search_type = strip_input('inputadm', $_REQUEST['search_type']);//搜尋方式


$tpl = new TemplatePower('./' . $tpl_name . '.htm');//輸出的網址
$tpl->assignInclude('external', './00external.php');
$tpl->assignInclude('mainMenu', './00mainMenu.php');
$tpl->prepare();

$tpl->assignglobal('tpl_str', $tpl_str);
$tpl->assignglobal('tpl_name', $tpl_name);

$tpl->gotoBlock('_ROOT');
//輸出分類
$strSQL1 = "select '1' as list_id, '銷售排行' as list_title
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


//SQL字串範本
$strSQL_sold ="
    SELECT 
        order_list.orderid AS orderid, 
        order_listdetail.prod_num AS prod_num, 
        SUM(order_listdetail.ord_qty) AS sold_qty, 
        order_list.add_date AS add_date,
        order_listdetail.ord_type AS ord_type,
        order_listdetail.ord_typestr AS ord_typestr,
        order_listdetail.prod_name AS prod_name,
        order_listdetail.ord_total_add AS prod_price,
        order_list.gcname AS gcname,
        order_list.add_ipaddress AS add_ipaddress,
        SUM(order_listdetail.ord_total_add) AS total_amount,
        order_listdetail.prod_namesub AS prod_namesub
    FROM order_list 
    JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
    WHERE order_list.process_type NOT IN ('X1','X2')
    AND order_listdetail.ord_type NOT IN ('C01','T01')
    AND order_list.add_date BETWEEN '2021-06-01' AND '2021-06-30'
    GROUP BY order_listdetail.prod_num, order_listdetail.ord_type
    ORDER BY sold_qty DESC, prod_num ASC
";


//搜尋條件的SQL字串
$strSQL_selected_GROUPBY = "";
switch($search_type){
    case 'prod_qty':
    case 'prod_amount':
        $strSQL_selected_GROUPBY .= " GROUP BY order_listdetail.prod_num";
        break;
    case 'prod_qty_separate':
    case 'prod_amount_separate':    
        $strSQL_selected_GROUPBY .= " GROUP BY order_listdetail.prod_num, order_listdetail.ord_type, order_listdetail.ord_price_add";
        break;
}
$strSQL_selected_ORDERBY = "";
switch($search_type){
    case 'prod_qty':
    case 'prod_qty_separate':
        $strSQL_selected_ORDERBY .= " ORDER BY sold_qty DESC, prod_num ASC";
        break;
    case 'prod_amount':
    case 'prod_amount_separate':  
        $strSQL_selected_ORDERBY .= " ORDER BY total_amount DESC, prod_num ASC";
        break;
}

//搜尋字串
$strSQL2_sold = "
    SELECT 
        order_list.orderid AS orderid, 
        order_listdetail.prod_num AS prod_num, 
        SUM(order_listdetail.ord_qty) AS sold_qty, 
        order_list.add_date AS add_date,
        order_listdetail.ord_type AS ord_type,
        order_listdetail.ord_typestr AS ord_typestr,
        order_listdetail.prod_name AS prod_name,
        order_listdetail.ord_price_add AS prod_price,
        order_list.gcname AS gcname,
        order_list.add_ipaddress AS add_ipaddress,
        SUM(order_listdetail.ord_total_add) AS total_amount,
        order_listdetail.prod_namesub AS prod_namesub
    FROM order_list 
    JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
    WHERE order_list.process_type NOT IN ('X1','X2')
    AND order_listdetail.ord_type NOT IN ('C01','T01','G01')
    AND order_list.add_date BETWEEN '".$start_date."' AND '".$end_date_SQL."'
    ".$strSQL_selected_GROUPBY."
    ".$strSQL_selected_ORDERBY."
";
//echo $strSQL2_sold;
//分頁，計算筆數的SQL
$strSQL2_sold1 = "
    SELECT COUNT(*) FROM (".$strSQL2_sold.") AS tmp1
";


//印出資料&計算分頁
if($search_type !==""){
    
    //筆數&分頁
    $total_order = GetQueryValueCount1($strSQL2_sold1, 'value');//計算筆數
    pageft($total_order, $page_splt01);
    $resSold01 = SqlQueryLimit1($strSQL2_sold, $displaypg, $firstcount);
    $tpl->assign('pagenav', $pagenav);
    $tpl->assign('pagejump01', $pagejump01);
    $tpl->assign('page_splt01_add', ($page_splt01 * ($page - 1)));
    $tpl->assign('page_splt01', $page_splt01);
    $tpl->assign('page', $page);
    
    //$resSold01 = SqlQuery1($strSQL_sold);
    $j = 0;//用來判斷奇偶數的數
    while(!$resSold01->EOF){
        $j++;
        //指派給HTML，搭配註釋標籤
        $tpl->newBlock('Table2_row');
        //一行與另一行顏色不同
        
        $tpl->assign('rowid02ck', ($j % 2 == 1) ? 'class="odd"' : '');
        $tpl->assign('rank', $j+(intval($page)-1)*$page_splt01);//排行的數字
        $tpl->assign('orderid', $resSold01->fields['orderid']);//訂單編號
        $tpl->assign('prod_num', $resSold01->fields['prod_num']);//商品編號
        $tpl->assign('sold_qty', $resSold01->fields['sold_qty']);//賣出數量
        $tpl->assign('add_date', $resSold01->fields['add_date']);//新增時間
        $tpl->assign('ord_type', $resSold01->fields['ord_type']);//商品類型
        $tpl->assign('ord_typestr', $resSold01->fields['ord_typestr']);//商品類型說明
        $tpl->assign('prod_name', $resSold01->fields['prod_name']);//商品名稱
        $tpl->assign('prod_price', $resSold01->fields['prod_price']);//商品單價
        $tpl->assign('gcname', $resSold01->fields['gcname']);//訂購姓名
        $tpl->assign('add_ipaddress', $resSold01->fields['add_ipaddress']);//新增位址
        $tpl->assign('total_amount',$resSold01->fields['total_amount']);//金額總計
        $tpl->assign('prod_namesub',$resSold01->fields['prod_namesub']);//商品副標
        
        $resSold01->MoveNext();
    }
    $tpl->gotoBlock('_ROOT');
    
}else{
    //不做事
}
    



#--- 傳回到HTML ---#

$tpl->assign('start_date', $start_date);//起始日期
$tpl->assign('end_date', $end_date);//結束日期
$tpl->assign('search_type', $search_type);//搜尋方式
//$tpl->assign('search_key1', $search_key1);//
$tpl->assign('search_key2', $search_key2);//關鍵字，陣列，不使用
$tpl->assign('search_key2_value', $search_key2_value);//關鍵字，值
$tpl->assign('search_type2', $search_type2);//搜尋方式二，陣列，不使用
$tpl->assign('search_type2_value', $search_type2_value);//搜尋方式二，值


$search_type_str = "";//搜尋方式的字串，要反映到HTML用的
switch($search_type){
    case 'prod_qty':
        $search_type_str = "銷售數量，商品種類不分開";
        break;
    case 'prod_qty_separate':
        $search_type_str = "銷售數量，商品種類分開";
        break;
    case 'prod_amount':
        $search_type_str = "銷售金額，商品種類不分開";
        break;
    case 'prod_amount_separate':
        $search_type_str = "銷售金額，商品種類分開";
        break;    
}
$tpl->assign('search_type_str',$search_type_str);//顯示於html搜尋方法的字串


$tpl->printToScreen();
?>
